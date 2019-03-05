<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPIShopGoodsTypeBusiness;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopGoodsTypeController extends BaseController
{

    // ajax获得列表数据
    public function ajax_alist(Request $request){
        // 查询
        // seller_id
        // shop_id

        // $this->InitParams($request);
        // $resultDatas = [];
        // return ajaxDataArr(1, $resultDatas, '');
        return  CTAPIShopGoodsTypeBusiness::getList($request, $this, 1, []); //
    }
}
