<?php
// 店铺商品分类[一级分类]
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopGoodsTypeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\ShopGoodsType';
}