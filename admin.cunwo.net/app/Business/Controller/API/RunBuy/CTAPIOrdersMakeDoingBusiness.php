<?php
// 订单商品制作单
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrdersMakeDoingBusiness extends CTAPIOrdersMakeBusiness
{
    public static $model_name = 'API\RunBuy\OrdersMakeDoingAPI';
    public static $table_name = 'orders_make_doing';// 表名称
}