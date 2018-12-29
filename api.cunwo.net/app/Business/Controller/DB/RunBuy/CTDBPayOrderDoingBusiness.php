<?php
// 支付订单
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPayOrderDoingBusiness extends CTDBPayOrderBusiness
{
    public static $model_name = 'RunBuy\PayOrderDoing';

}