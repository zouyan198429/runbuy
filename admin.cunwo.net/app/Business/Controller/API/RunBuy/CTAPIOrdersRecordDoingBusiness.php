<?php
// 订单操作记录
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrdersRecordDoingBusiness extends CTAPIOrdersRecordBusiness
{
    public static $model_name = 'API\RunBuy\OrdersRecordDoingAPI';
    public static $table_name = 'orders_record_doing';// 表名称

}