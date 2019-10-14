<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPILabelsBusiness;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LabelsController extends BaseController
{

    // ajax获得列表数据
    public function ajax_alist(Request $request){
        // $this->InitParams($request);
        // $resultDatas = [];
        // return ajaxDataArr(1, $resultDatas, '');
        return  CTAPILabelsBusiness::getList($request, $this, 2 + 4, []); //
    }

    // ajax获得详情数据
    public function ajax_info(Request $request,$id = 0){
        // $this->InitParams($request);
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');

        $info = CTAPILabelsBusiness::getInfoData($request, $this, $id, [], '', []);// , ['city']
        return ajaxDataArr(1, $info, '');
    }
}
