<?php
// 订单商品
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrdersGoodsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\OrdersGoodsAPI';
    public static $table_name = 'orders_goods';// 表名称

}