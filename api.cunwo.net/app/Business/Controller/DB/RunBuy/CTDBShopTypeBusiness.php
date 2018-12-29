<?php
// 店铺分类[一级分类]
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopTypeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\ShopType';
}