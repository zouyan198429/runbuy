<?php
// 店铺商品价格
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopGoodsPricesBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\ShopGoodsPrices';

}