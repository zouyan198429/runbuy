<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPISiteIntroBusiness;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SiteIntroController extends BaseController
{
    // ajax获得列表数据
    public function ajax_alist(Request $request){
        // $this->InitParams($request);
        // $resultDatas = [];
        // return ajaxDataArr(1, $resultDatas, '');
        return  CTAPISiteIntroBusiness::getList($request, $this, 1, []); // 2 + 4
    }

    // ajax获得详情数据
    public function ajax_info(Request $request,$id = 0){
        // $this->InitParams($request);
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');

        $info = CTAPISiteIntroBusiness::getInfoData($request, $this, $id, [], [], []);// , ['city']
        return ajaxDataArr(1, $info, '');
    }
}
