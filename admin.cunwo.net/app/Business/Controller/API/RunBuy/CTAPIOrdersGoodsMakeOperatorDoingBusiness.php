<?php
// 订单商品制作操作员
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrdersGoodsMakeOperatorDoingBusiness extends CTAPIOrdersGoodsMakeOperatorBusiness
{
    public static $model_name = 'API\RunBuy\OrdersGoodsMakeOperatorDoingAPI';
    public static $table_name = 'orders_goods_make_operator_doing';// 表名称
}