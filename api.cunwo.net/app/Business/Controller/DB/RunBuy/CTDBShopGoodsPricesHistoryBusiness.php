<?php
// 店铺商品价格历史
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopGoodsPricesHistoryBusiness extends CTDBShopGoodsPricesBusiness
{
    public static $model_name = 'RunBuy\ShopGoodsPricesHistory';

}