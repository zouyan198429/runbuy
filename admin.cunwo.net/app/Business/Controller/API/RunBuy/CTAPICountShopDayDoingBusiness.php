<?php
// 店铺每日结算
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICountShopDayDoingBusiness extends CTAPICountShopDayBusiness
{
    public static $model_name = 'API\RunBuy\CountShopDayDoingAPI';
    public static $table_name = 'count_shop_day_doing';// 表名称
}