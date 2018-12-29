<?php
// 店铺商品历史
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopGoodsHistoryBusiness extends CTDBShopGoodsBusiness
{
    public static $model_name = 'RunBuy\ShopGoodsHistory';
}