<?php
// 城市商家业务收费标准历史
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICityRateHistoryBusiness extends CTAPICityRateBusiness
{
    public static $model_name = 'API\RunBuy\CityRateHistoryAPI';
    public static $table_name = 'city_rate_history';// 表名称
}