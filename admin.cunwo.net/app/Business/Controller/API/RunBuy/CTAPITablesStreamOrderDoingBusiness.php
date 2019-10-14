<?php
// 桌位就餐流订单
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPITablesStreamOrderDoingBusiness extends CTAPITablesStreamOrderBusiness
{
    public static $model_name = 'API\RunBuy\TablesStreamOrderDoingAPI';
    public static $table_name = 'tables_stream_order_doing';// 表名称
}