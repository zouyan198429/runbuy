<?php
// 订单操作记录
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrdersRecordBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\OrdersRecord';

}