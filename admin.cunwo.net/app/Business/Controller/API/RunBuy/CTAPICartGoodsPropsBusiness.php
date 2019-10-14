<?php
// 购物车商品属性
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICartGoodsPropsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\CartGoodsPropsAPI';
    public static $table_name = 'cart_goods_props';// 表名称

}