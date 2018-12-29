<?php
// 店铺历史
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopHistoryBusiness extends CTDBShopBusiness
{
    public static $model_name = 'RunBuy\ShopHistory';
}