<?php
// 店铺业务类型历史
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIShopBusinessHistoryBusiness extends CTAPIShopBusinessBusiness
{
    public static $model_name = 'API\RunBuy\ShopBusinessHistoryAPI';
    public static $table_name = 'shop_business_history';// 表名称
}