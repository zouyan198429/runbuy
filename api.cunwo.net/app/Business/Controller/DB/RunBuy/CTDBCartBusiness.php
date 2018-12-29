<?php
// 购物车
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;

class CTDBCartBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\Cart';


}