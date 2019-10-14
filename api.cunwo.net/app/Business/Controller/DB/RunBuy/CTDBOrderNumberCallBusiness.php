<?php
// 下单号和付款单号叫号
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrderNumberCallBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\OrderNumberCall';

}