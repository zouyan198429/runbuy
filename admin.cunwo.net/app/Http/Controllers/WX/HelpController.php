<?php

namespace App\Http\Controllers\WX;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpController extends BaseController
{
    // 获得站点介绍列表--所有
    public function siteIntroList(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    // 获得站点介绍详情
    public function siteIntroInfo(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }
}
