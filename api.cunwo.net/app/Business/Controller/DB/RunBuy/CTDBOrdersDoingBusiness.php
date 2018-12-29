<?php
// 订单
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrdersDoingBusiness extends CTDBOrdersBusiness
{
    public static $model_name = 'RunBuy\OrdersDoing';

}