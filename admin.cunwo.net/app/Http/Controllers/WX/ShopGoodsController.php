<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPIShopGoodsBusiness;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopGoodsController extends BaseController
{

    // ajax获得列表数据
    public function ajax_alist(Request $request){
        // 查询
        // seller_id--必传
        //  shop_id
        //   type_id
        // is_hot

        // $this->InitParams($request);
        // $resultDatas = [];
        // return ajaxDataArr(1, $resultDatas, '');
        CTAPIShopGoodsBusiness::mergeRequest($request, $this, [
            'is_sale' => 1,// 是否上架1上架2下架
        ]);
        $result =  CTAPIShopGoodsBusiness::getList($request, $this, 2 + 4, [], [
            'city', 'cityPartner', 'seller'
            , 'shop', 'type', 'siteResources'
            // , 'priceProps.prop.name', 'priceProps.propVal.name'// 价格属性名--订单前
           //  , 'priceProps.propVal.name', 'priceProps.propName'// 属性名--订单后
            , 'priceProps.propName', 'priceProps.propValName' //含历史--用这个
            // , 'props.prop.name', 'props.propVal.name'// 属性名--订单前
            , 'props.propName', 'props.propValName'// 价格属性名--订单后 含历史--用这个
        ]); //
        $data_list = $result['result']['data_list'] ?? [];
        $temDataList = [];
        foreach($data_list as $k => $v){

            // 属性及属性值处理
            $props = $v['props'] ?? [];
            $format_props = CTAPIShopGoodsBusiness::formatProps($props, [], 2);
            $goods_name =  $v['goods_name'] ?? '';
            $price_list = $v['price_list'] ?? [];// 价格属性
            $resource_url = $v['resource_list'][0]['resource_url'] ?? '';
//            $intro = $v['intro'] ?? '';
//            $v['intro'] = replace_enter_char($intro,2);
            $tem_price_val = $v['price'] ?? '';
            $tem_price_val = Tool::formatMoney($tem_price_val, 2, '');
            $temInfo = [
                'id' => $v['id'],
                'is_sale' => $v['is_sale'],
                'intro' => $v['intro'],
                'goods_name' => $goods_name,
                'resource_url' => $resource_url,
                'is_hot' => $v['is_hot'],
                'is_hot_text' => $v['is_hot_text'] ?? '',
                'props' => $format_props,


                'price_val' => $tem_price_val,// $v['price'] ?? '',
                'price_name' => '',
                'price_id' => 0,
                'prop_id' => 0,
                'prop_name' => '',
                'prop_val_id' => 0,
                'goods_name_full' => $goods_name,
            ];
            foreach($price_list as $t_v){
                $tPriceArr = $temInfo;
                $price_name = $t_v['price_name'] ?? '';
                $goods_name_full = empty($price_name) ? $goods_name : $goods_name . '[' . $price_name . ']';
                if($v['is_hot'] == 2) $goods_name_full .= '-热销';
                $tem_price_val = $t_v['price_val'] ?? '0';
                $tem_price_val = Tool::formatMoney($tem_price_val, 2, '');
                $tPriceArr = array_merge($tPriceArr, [
                    'price_val' => $tem_price_val,// $t_v['price_val'] ?? '0',
                    'price_name' => $price_name,
                    'price_id' => $t_v['price_id'] ?? '0',
                    'prop_id' => $t_v['prop_id'] ?? '0',
                    'prop_name' => $t_v['prop_name'] ?? '',
                    'prop_val_id' => $t_v['prop_val_id'] ?? '0',
                    'goods_name_full' => $goods_name_full,
                ]);
                array_push($temDataList, $tPriceArr);

            }
        }
        $data_list = $temDataList;
        $data_list = array_values($data_list);
        $result['result']['data_list'] = $data_list;
        return $result;
    }
}
