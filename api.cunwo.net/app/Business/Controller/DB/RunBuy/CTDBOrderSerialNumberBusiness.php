<?php
// 下单号和付款单号
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrderSerialNumberBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\OrderSerialNumber';

}