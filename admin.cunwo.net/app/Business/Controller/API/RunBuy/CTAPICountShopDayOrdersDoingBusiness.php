<?php
// 店铺每日结算主订单
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICountShopDayOrdersDoingBusiness extends CTAPICountShopDayOrdersBusiness
{
    public static $model_name = 'API\RunBuy\CountShopDayOrdersDoingAPI';
    public static $table_name = 'count_shop_day_orders_doing';// 表名称
}