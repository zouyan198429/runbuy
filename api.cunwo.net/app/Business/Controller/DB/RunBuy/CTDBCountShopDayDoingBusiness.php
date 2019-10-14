<?php
// 店铺每日结算
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCountShopDayDoingBusiness extends CTDBCountShopDayBusiness
{
    public static $model_name = 'RunBuy\CountShopDayDoing';

}