<?php
// 店铺商品属性
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIShopGoodsPropsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\ShopGoodsPropsAPI';
    public static $table_name = 'shop_goods_props';// 表名称

}