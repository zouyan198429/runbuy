<?php
// 订单商品制作
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrdersMakeGoodsDoingBusiness extends CTAPIOrdersMakeGoodsBusiness
{
    public static $model_name = 'API\RunBuy\OrdersMakeGoodsDoingAPI';
    public static $table_name = 'orders_make_goods_doing';// 表名称
}