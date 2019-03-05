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

}
