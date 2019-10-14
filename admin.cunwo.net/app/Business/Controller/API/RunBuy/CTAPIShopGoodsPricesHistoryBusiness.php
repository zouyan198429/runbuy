<?php
// 店铺商品价格历史
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIShopGoodsPricesHistoryBusiness extends CTAPIShopGoodsPricesBusiness
{
    public static $model_name = 'API\RunBuy\ShopGoodsPricesHistoryAPI';
    public static $table_name = 'shop_goods_prices_history';// 表名称

}