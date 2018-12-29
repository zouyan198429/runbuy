<?php
// 订单商品
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrdersGoodsBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\OrdersGoods';

}