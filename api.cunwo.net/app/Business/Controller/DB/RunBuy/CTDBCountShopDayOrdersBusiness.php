<?php
// 店铺每日结算主订单
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCountShopDayOrdersBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\CountShopDayOrders';

}