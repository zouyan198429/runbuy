<?php
// 下单号和付款单号--当前的记录
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrderSerialNumberDoingBusiness extends CTAPIOrderSerialNumberBusiness
{
    public static $model_name = 'API\RunBuy\OrderSerialNumberDoingAPI';
    public static $table_name = 'order_serial_number_doing';// 表名称
}