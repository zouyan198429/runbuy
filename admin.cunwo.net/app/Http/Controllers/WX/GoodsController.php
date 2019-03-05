<?php

namespace App\Http\Controllers\WX;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends BaseController
{

    // 根据店铺id，分类id获取店铺的商品信息--有分页
    public function list(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

}
