<?php
// 店铺商品
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopGoodsBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\ShopGoods';
}