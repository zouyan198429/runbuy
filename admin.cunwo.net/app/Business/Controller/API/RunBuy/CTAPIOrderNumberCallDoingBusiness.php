<?php
// 下单号和付款单号叫号
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrderNumberCallDoingBusiness extends CTAPIOrderNumberCallBusiness
{
    public static $model_name = 'API\RunBuy\OrderNumberCallDoingAPI';
    public static $table_name = 'order_number_call_doing';// 表名称
}