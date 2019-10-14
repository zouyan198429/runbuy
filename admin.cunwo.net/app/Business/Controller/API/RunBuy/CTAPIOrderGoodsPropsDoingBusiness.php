<?php
// 订单商品属性
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrderGoodsPropsDoingBusiness extends CTAPIOrderGoodsPropsBusiness
{
    public static $model_name = 'API\RunBuy\OrderGoodsPropsDoingAPI';
    public static $table_name = 'order_goods_props_doing';// 表名称

}