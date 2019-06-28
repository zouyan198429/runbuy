<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPICartBusiness;
use App\Business\Controller\API\RunBuy\CTAPICityBusiness;
use App\Business\Controller\API\RunBuy\CTAPICommonAddrBusiness;
use App\Business\Controller\API\RunBuy\CTAPIFeeScaleTimeBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopGoodsBusiness;
use App\Services\Map\Map;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CartController extends BaseController
{

    //  添加单个商品到购物车，已有的，数量+n
    public function ajax_save(Request $request){

        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        if(!is_numeric($id))  $id = 0;
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');// 城市id
//        $shop_id = CommonRequest::getInt($request, 'shop_id');// 店铺id
        $goods_id = CommonRequest::getInt($request, 'goods_id');// 商品id
        $prop_price_id = CommonRequest::getInt($request, 'prop_price_id');// 价格属性表id
        $amount = CommonRequest::getInt($request, 'amount');// 商品数量 ;0是删除

        $saveData = [
            'staff_id' => $this->user_id,
            'city_site_id' => $city_site_id,
//            'shop_id' => $shop_id,
            'goods_id' => $goods_id,
            'prop_price_id' => $prop_price_id,
            'amount' => $amount,
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPICartBusiness::replaceById($request, $this, $saveData, $id, true);
        return ajaxDataArr(1, $resultDatas, '');
    }


    // 修改商品数量
//    public function addGoodCount(Request $request){
//        $this->InitParams($request);
//        $resultDatas = [];
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    // 获得当前用户所有的购物车商品，按商户分组
    public function ajax_initCart(Request $request){
        // 参数 city_site_id 城市id
        $this->InitParams($request);
        $need_props = CommonRequest::getInt($request, 'need_props');// 是否需要返回商品属性 1不需要 2需要
        //  显示到定位点的距离
        CTAPICartBusiness::mergeRequest($request, $this, [
            'staff_id' => $this->user_id,
        ]);
        $result =  CTAPICartBusiness::getList($request, $this, 1, [], [
            'shop', 'props', 'goods.siteResources'
            , 'goods.props.propName', 'goods.props.propValName'// 属性名--订单后
            // , 'goods.props.prop.name', 'goods.props.propVal.name'// 属性名--订单前
            , 'goodsPrice.propName', 'goodsPrice.propValName'// 价格属性名--订单后
            // , 'goodsPrice.prop.name', 'goodsPrice.propVal.name'// 价格属性名--订单前
        ]);
        $data_list = $result['result']['data_list'] ?? [];
        $formatList = [];
        foreach($data_list as $k => $v){

            $goods = $v['goods'] ?? [];
            if(empty($goods)) continue;

            $shop = $v['shop'] ?? [];
            if(empty($shop)) continue;
            // 获得当前购物车属性
            $cartProps = $v['props'] ?? [];
            $formatCartPropArr = [];
            foreach($cartProps as $p_k => $p_v){
                if(!isset($formatCartPropArr[$p_v['prop_id']])) $formatCartPropArr[$p_v['prop_id']] = [];
                $formatCartPropArr[$p_v['prop_id']][] = $p_v['prop_val_id'];
            }
            // 属性及属性值处理
            $props = $v['goods']['props'] ?? [];
            $format_props = ($need_props == 2) ? CTAPIShopGoodsBusiness::formatProps($props, $formatCartPropArr, 2) : [] ;
            // 店铺状态  状态0待审核1审核通过2审核未通过4冻结(禁用)
            $shop_status = $v['shop']['status'] ?? 0;
            if($shop_status != 1) continue;
            // 店铺 status_business  经营状态  1营业中 2 歇业中 4 停业[店铺人工操作 8  关业[店铺平台操作]
            $shop_status_business = $v['shop']['status_business'] ?? 4;
            if($shop_status_business != 1) continue;

            // 商品 是否上架1上架2下架
            $is_sale = $v['goods']['is_sale'] ?? 2;
            if($is_sale != 1) continue;

            $shop_id = $v['shop_id'];
            if(!isset($formatList['shop' . $shop_id])) $formatList['shop' . $shop_id] = [
                'shop_id' => $shop_id,
                'shop_name' => $v['shop']['shop_name'] ?? '',
            ];
            $goods_id = $v['goods_id'];
            $prop_id = $v['goods_price']['prop_id'] ?? 0 ;
            $prop_price_id = $v['prop_price_id'];

            // 产品图片
            $site_resources =  $v['goods']['site_resources'] ?? [];
            if(!empty($site_resources)) $site_resources = Tool::formatResource($site_resources, 2) ;
            $resource_url = $site_resources[0]['resource_url'] ?? '';

            $goods_name = $v['goods']['goods_name'] ?? '';
            $price_name = $v['goods_price']['prop_val_name']['main_name'] ?? '';// $v['goods_price']['prop_val']['name']['main_name'] ?? '';
            $price_prop_name = $v['goods_price']['prop_name']['main_name'] ?? '';// $v['goods_price']['prop']['name']['main_name'] ?? '';
            $goods_name_full = empty($price_name) ? $goods_name : $goods_name . '[' . $price_name . ']';
            if($v['goods']['is_hot'] == 2) $goods_name_full .= '-热销';
            $tem_price_val = $v['goods_price']['price'] ?? $v['goods']['price'];
            $tem_price_val = Tool::formatMoney($tem_price_val, 2, '');

            $formatList['shop' . $shop_id]['goods_list']['good' . $goods_id . 'p' . $prop_id . 'p' . $prop_price_id . ''] = [
                'cart_id' => $v['id'],
                'goods_id' => $goods_id,
                'goods_name' => $v['goods']['goods_name'] ?? '',
                'prop_id' => $prop_id,
                'prop_name' => $price_prop_name,// $v['goods_price']['prop_name']['main_name'] ?? '',
                'price_id' => $prop_price_id,
                'price_name' => $price_name,// $v['goods_price']['prop_val_name']['main_name'] ?? '',
                'price' => $tem_price_val,// $v['goods_price']['price'] ?? $v['goods']['price'],
                'amount' => $v['amount'],
                'resource_url' => $resource_url,
                'goods_name_full' => $goods_name_full,
                'props' => $format_props,
                'cart_props' => $formatCartPropArr,
            ];

        }
        // pr($formatList);
        $result['result']['data_list'] = $formatList;
        return $result;
    }

    // 获得当前用户所有的购物车商品，按商户分组
    // 参数都必填 city_site_id:当前城市id; addr_id:收货地址id;second_num:送货速度 24:急递;36:极递;60:快递
    public function ajax_getStartPrice(Request $request)
    {
        $startPrice = 0;
        // 参数 city_site_id 城市id
        $this->InitParams($request);
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');// 当前城市id
        if( empty($city_site_id) || !is_numeric($city_site_id) ) return ajaxDataArr(1, $startPrice, '');

        $addr_id = CommonRequest::getInt($request, 'addr_id');// 收货地址id
        if(!is_numeric($addr_id) || $addr_id <=0) return ajaxDataArr(1, $startPrice, '');

        $second_num = CommonRequest::getInt($request, 'second_num');// 送货速度 24:急递;36:极递;60:快递
        $second_num_arr = [24, 36, 60];
        if( !in_array($second_num, $second_num_arr) )  return ajaxDataArr(1, $startPrice, '');

        // 获得城市信息
        $cityInfo = CTAPICityBusiness::getInfoData($request, $this, $city_site_id, ['price_distance_default', 'price_distance_every', 'price_shop_default', 'price_shop_every'], '');
        if(empty($cityInfo))   return ajaxDataArr(1, $startPrice, '');
        Log::info('根据距离算运费---City',[$cityInfo]);
        $price_distance_every = $cityInfo['price_distance_every'] ?? 0;
        $price_shop_every = $cityInfo['price_shop_every'] ?? 0;

        // 获得地址信息
        $addrInfo = CTAPICommonAddrBusiness::getInfoData($request, $this, $addr_id, ['real_name', 'longitude', 'latitude']);// , ['city']
        if(empty($addrInfo))   return ajaxDataArr(1, $startPrice, '');

        Log::info('根据距离算运费---CommonAddr',[$addrInfo]);
        $longitude = $addrInfo['longitude'] ?? '';
        $latitude = $addrInfo['latitude'] ?? '';
        if(empty($longitude) || empty($latitude))   return ajaxDataArr(1, $startPrice, '');

        //  显示到定位点的距离
        CTAPICartBusiness::mergeRequest($request, $this, [
            'staff_id' => $this->user_id,
        ]);
        $result = CTAPICartBusiness::getList($request, $this, 1, [], [
            'shop','goods'
        ]);
        $data_list = $result['result']['data_list'] ?? [];
        Log::info('根据距离算运费---当前城市，购物车的店铺信息',[$data_list]);

        if( empty($data_list) ) return ajaxDataArr(1, $startPrice, '');
        // 整理店铺信息
        $hasShopIds = [];
        $shopList = [];
        foreach($data_list as $k => $v){
            if(!in_array($v['shop_id'], $hasShopIds)){
                $goods = $v['goods'] ?? [];
                if(empty($goods)) continue;

                $shop = $v['shop'] ?? [];
                if(empty($shop)) continue;
                // 店铺状态  状态0待审核1审核通过2审核未通过4冻结(禁用)
                $shop_status = $v['shop']['status'] ?? 0;
                if($shop_status != 1) continue;
                // 店铺 status_business  经营状态  1营业中 2 歇业中 4 停业[店铺人工操作 8  关业[店铺平台操作]
                $shop_status_business = $v['shop']['status_business'] ?? 4;
                if($shop_status_business != 1) continue;

                // 商品 是否上架1上架2下架
                $is_sale = $v['goods']['is_sale'] ?? 2;
                if($is_sale != 1) continue;

                $tShop = $v['shop'] ?? [];
                if( empty($tShop) ) continue;
                $temShopInfo = [
                    'id' =>  $tShop['id'],
                    'shop_name' =>  $tShop['shop_name'],
                    'longitude' =>  $tShop['longitude'],
                    'latitude' =>  $tShop['latitude'],
                ];
                array_push($shopList, $temShopInfo);
                array_push($hasShopIds, $v['shop_id']);
            }
        }
        $hasShopIds = array_sort($hasShopIds);
        // 获得当前城市当前时间的时间段价格

        $nowTime =  date('H:i:s');
        $queryParams = [
            'where' => [
                ['city_site_id', '=', $city_site_id],
                ['begin_time', '<=', $nowTime],
                ['end_time', '>=', $nowTime],
            ],
            // 'whereIn' => [
            //   'id' => $subjectHistoryIds,
            //],
//            'select' => [
//                'id'
//            ],
//            'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
        ];
        $timeInfo = CTAPIFeeScaleTimeBusiness::getInfoByQuery($request, $this, '', $this->company_id, $queryParams);
        Log::info('根据距离算运费---FeeScaleTime',[$timeInfo]);
        if( empty($timeInfo) ) return ajaxDataArr(1, $startPrice, '');
        $init_price = $timeInfo['init_price'] ?? '';
        if(!is_numeric($init_price) || $init_price <0) return ajaxDataArr(1, $startPrice, '');

        // 计算各店铺到买家的距离
        Map::resolveDistance($shopList, $latitude, $longitude, 'distance', 0, 'desc', 'latitude', 'longitude', '');
        $shopList = array_values($shopList);
        Log::info('根据距离算运费---各店铺到买家的距离',[$shopList]);
        $distanceTotal = 0;
        $dataCount = count($shopList);
        if($dataCount > 1){
            foreach($shopList as $k => $v){
                // $temDistance = $v['distance'] ?? 0;
                $latitudeTem = $v['latitude'];
                $longitudeTem = $v['longitude'];
                if( ($k + 1) < $dataCount ){
                    $temLatitude = $shopList[$k + 1]['latitude'];
                    $temLongitude = $shopList[$k + 1]['longitude'];
                }else{
                    $temLatitude = $latitude;
                    $temLongitude = $longitude;
                }

                $temDistance = Map::getDistance($latitudeTem, $longitudeTem, $temLatitude, $temLongitude);

                Log::info('根据距离算运费---店铺' . $k . '与店铺' . ($k + 1) . '距离',[$temDistance]);
                if(!is_numeric($temDistance) || $temDistance <= 0 ) continue;
                $distanceTotal += $temDistance;
            }
        }else{
            $distanceTotal =  $shopList[0]['distance'] ?? 0;
        }
        Log::info('根据距离算运费---总距离[单位米]',[$distanceTotal]);
        if($distanceTotal > 1000)  $distanceTotal = $distanceTotal + 400;// 400
        $disVal = ceil($distanceTotal / 1000); // 距离换成公里:向上取整
        Log::info('根据距离算运费---总距离[单位公里]',[$disVal]);
        if($disVal <= 2){
            $startPrice = $init_price + ($dataCount - 1) * $price_shop_every;
        }else{
            $startPrice = $init_price + ($disVal - 2) * $price_distance_every + ($dataCount - 1) * $price_shop_every;
        }
        Log::info('根据距离算运费---second_num',[$second_num]);
        Log::info('根据距离算运费---second_num前的运费',[$startPrice]);
        // 急递 +2  极递 +0;快递 -1
        switch ($second_num)
        {
            case 24:// 急递
                $startPrice += 2;
                break;
            case 36:// 极递
                $startPrice += 0;
                break;
            case 60:// 快递
                $startPrice -= 1;
                break;
            default:
        }
        Log::info('根据距离算运费---最终运费',[$startPrice]);
        $startPrice = Tool::formatFloat($startPrice, 2, 4);
        return ajaxDataArr(1, $startPrice, '');
    }


    // 获得当前用户所有的购物车商品，按商户分组
//    public function ajax_alist(Request $request){
//        $this->InitParams($request);
//        return  CTAPICartBusiness::getList($request, $this, 2 + 4, [], ['oprateStaffHistory']);
//    }


    // 移除商品
//    public function ajax_del(Request $request){
//        $this->InitParams($request);
//        return CTAPICartBusiness::delAjax($request, $this);
//    }

    // 移除商品--通过店铺id shop_id:为空，则清空购物车
    public function ajax_del_shop(Request $request){
        $this->InitParams($request);
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');// 城市id
        $shop_id = CommonRequest::getInt($request, 'shop_id');// 店铺id

        $saveData = [
            'staff_id' => $this->user_id,
            'city_site_id' => $city_site_id,
        ];

//        if($shop_id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPICartBusiness::delByShopId($request, $this, $saveData, $shop_id, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    // 购物车商品属性操作 good_prop_table_id  多个用逗号分隔, 0 ：代表一个都没有选
    public function ajax_prop(Request $request){
        $this->InitParams($request);
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');// 城市id
        $cart_id = CommonRequest::getInt($request, 'cart_id');// 购物车表id
        $prop_id = CommonRequest::getInt($request, 'prop_id');// 属性id
        $good_prop_table_id = CommonRequest::get($request, 'good_prop_table_id');// 商品属性值表id; 多个用逗号分隔, 0 ：代表一个都没有选

        $saveData = [
            'staff_id' => $this->user_id,
            'city_site_id' => $city_site_id,
            'cart_id' => $cart_id,
            'prop_id' => $prop_id,
            'good_prop_table_id' => $good_prop_table_id,
        ];

//        if($shop_id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPICartBusiness::saveProps($request, $this, $saveData, $prop_id, true);
        return ajaxDataArr(1, $resultDatas, '');
    }
    // 清空用户的购物车
//    public function empty(Request $request){
//        $this->InitParams($request);
//        $resultDatas = [];
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    //  生成订单
    public function ajax_createOrder(Request $request){

        $this->InitParams($request);
        $cartIds = CommonRequest::get($request, 'cartIds');// 购物车id,多个逗号分隔
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');// 城市id
        $tableware = CommonRequest::getInt($request, 'tableware');// 需要的餐具数
        $second_num = CommonRequest::getInt($request, 'second_num');// 时间分钟数
        $total_run_price = CommonRequest::getInt($request, 'total_run_price');// 总跑腿费
        $remarks = CommonRequest::get($request, 'remarks');// 买家备注
        $addr_id = CommonRequest::getInt($request, 'addr_id');// 收货地址id

        $saveData = [
            'staff_id' => $this->user_id,
            'city_site_id' => $city_site_id,
            'addr_id' => $addr_id,
            'tableware' => $tableware,
            'remarks' => $remarks,
            'second_num' => $second_num,
            'total_run_price' => $total_run_price,
        ];
        $resultDatas = CTAPICartBusiness::createOrderByCartId($request, $this, $saveData, $cartIds, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

}
