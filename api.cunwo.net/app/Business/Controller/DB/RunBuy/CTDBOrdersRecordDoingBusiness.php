<?php
// 订单操作记录
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrdersRecordDoingBusiness extends CTDBOrdersRecordBusiness
{
    public static $model_name = 'RunBuy\OrdersRecordDoing';

}