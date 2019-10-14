<?php
// 店铺每日结算主订单
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCountShopDayOrdersDoingBusiness extends CTDBCountShopDayOrdersBusiness
{
    public static $model_name = 'RunBuy\CountShopDayOrdersDoing';

}