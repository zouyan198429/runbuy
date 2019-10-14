<?php
// 店铺商品属性历史
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIShopGoodsPropsHistoryBusiness extends CTAPIShopGoodsPropsBusiness
{
    public static $model_name = 'API\RunBuy\ShopGoodsPropsHistoryAPI';
    public static $table_name = 'shop_goods_props_history';// 表名称

}