<?php
// 订单商品属性
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrderGoodsPropsDoingBusiness extends CTDBOrderGoodsPropsBusiness
{
    public static $model_name = 'RunBuy\OrderGoodsPropsDoing';

}