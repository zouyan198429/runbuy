<?php

namespace App\Http\Controllers\WX;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderPayController extends BaseController
{
    // 订单付款
    public function pay(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    // 订单退款
    public function refund(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    // 支付保证金
    public function bond(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    // 充值
    public function recharge(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

}
