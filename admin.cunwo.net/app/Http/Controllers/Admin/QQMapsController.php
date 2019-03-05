<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class QQMapsController extends WorksController
{

    /**
     * 经纬度选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function latLngSelect(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $lat =  CommonRequest::get($request, 'lat');// 纬度
        $lng =  CommonRequest::get($request, 'lng');// 纬度
        if($lat == '0' && $lng == '0'){
            $lat = '';
            $lng = '';
        }
        $reDataArr['lat'] =  $lat;// 纬度
        $reDataArr['lng'] =  $lng;// 纬度
        $reDataArr['frm'] =  CommonRequest::getInt($request, 'frm');// 来源0非弹窗 1弹窗
        return view('admin.qqMaps.latLngSelect', $reDataArr);
    }

}
