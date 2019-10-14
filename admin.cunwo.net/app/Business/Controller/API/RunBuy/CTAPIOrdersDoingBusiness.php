<?php
// 订单
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrdersDoingBusiness extends CTAPIOrdersBusiness
{
    public static $model_name = 'API\RunBuy\OrdersDoingAPI';
    public static $table_name = 'orders_doing';// 表名称

}