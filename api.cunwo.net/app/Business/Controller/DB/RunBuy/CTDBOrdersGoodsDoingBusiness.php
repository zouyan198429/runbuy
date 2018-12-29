<?php
// 订单商品
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrdersGoodsDoingBusiness extends CTDBOrdersGoodsBusiness
{
    public static $model_name = 'RunBuy\OrdersGoodsDoing';

}