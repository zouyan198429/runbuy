<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPICityBusiness;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CityController extends BaseController
{
    // 根据经纬度坐标，获得最近的城市信息
    public function getNearCity(Request $request){
        // $this->InitParams($request);
        $latitude = CommonRequest::get($request, 'latitude');// 纬度
        $longitude = CommonRequest::get($request, 'longitude');// 经度
        $reType = CommonRequest::getInt($request, 'reType');// $reType 返回类型 1 最近的一个城市[一维数组] 2 所有城市
        $formatType = CommonRequest::getInt($request, 'formatType');// $formatType  所有城市 返回时，数据格式 1 直接返回 2 所有城市 [二维数组--小程序城市切换页] 4 所有城市 [二维数组--sort_num升序] 8 所有城市 [二维数组--字母升序]
        $prams = [
            'latitude' => $latitude,// 纬度
            'longitude' => $longitude,// 经度
        ];
        $resultDatas = CTAPICityBusiness::getNearCityByLatLong($request, $this, $prams, $reType, $formatType,1);
         return ajaxDataArr(1, $resultDatas, '');
    }

    // 获得所有的城市信息
    public function getCitys(Request $request){
        // $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    /*
     * 获得订单饱和度
    订单饱和度说明：

    当前订单饱和度后面的那个0是个小太极图或者圆圈，红色或者蓝色或是绿色，红色代表非常忙，蓝色代表缓和，绿色代表有人有空，没有人上班就显示黑色吧。

    订单池里面的订单数除以上班的骑手人数：
    3:大于1.3，就是红色。
    2:大于0.5，小于1.3，就是蓝色。
    1:大于0，小于0.5，就是绿色。
    */
    public function getOrderSaturation(Request $request){
//        $this->InitParams($request);
//        $user_id = $this->user_id;
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        $info = CTAPICityBusiness::getInfoData($request, $this, $city_site_id, ['order_saturation'], '');
        $order_saturation = $info['order_saturation'] ?? 0;
        $re_num = 1;
        if($order_saturation > 1.3){
            $re_num = 3;
        }else if($order_saturation <= 1.3 && $order_saturation >= 1.3){
            $re_num = 2;
        }
        return ajaxDataArr(1, $re_num, '');
    }
}
