<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPICartBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopGoodsBusiness;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $result =  CTAPICartBusiness::getList($request, $this, 1, [], ['shop', 'props', 'goods.siteResources', 'goods.props.propName', 'goods.props.propValName', 'goodsPrice.propName', 'goodsPrice.propValName']);
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
            $format_props = ($need_props == 2) ? CTAPIShopGoodsBusiness::formatProps($props, $formatCartPropArr) : [] ;
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
            $price_name = $v['goods_price']['prop_val_name']['main_name'] ?? '';
            $goods_name_full = empty($price_name) ? $goods_name : $goods_name . '[' . $price_name . ']';
            if($v['goods']['is_hot'] == 2) $goods_name_full .= '-热销';

            $formatList['shop' . $shop_id]['goods_list']['good' . $goods_id . 'p' . $prop_id . 'p' . $prop_price_id . ''] = [
                'cart_id' => $v['id'],
                'goods_id' => $goods_id,
                'goods_name' => $v['goods']['goods_name'] ?? '',
                'prop_id' => $prop_id,
                'prop_name' => $v['goods_price']['prop_name']['main_name'] ?? '',
                'price_id' => $prop_price_id,
                'price_name' => $v['goods_price']['prop_val_name']['main_name'] ?? '',
                'price' => $v['goods_price']['price'] ?? $v['goods']['price'],
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

}
