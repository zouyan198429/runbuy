<?php
// 店铺营业时间
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopOpenTimeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\ShopOpenTime';
}