<?php

namespace App\Http\Controllers\WX;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends BaseController
{

    //  添加单个商品到购物车，已有的，数量+n
    public function addGood(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }


    // 修改商品数量
//    public function addGoodCount(Request $request){
//        $this->InitParams($request);
//        $resultDatas = [];
//        return ajaxDataArr(1, $resultDatas, '');
//    }


    // 获得当前用户所有的购物车商品，按商户分组
    public function getGoods(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }


    // 移除商品
    public function removeGood(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }


    // 清空用户的购物车
    public function empty(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

}
