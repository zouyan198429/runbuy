<?php
// 城市商家业务年收费标准
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICityAnnualFeeHistoryBusiness extends CTAPICityAnnualFeeBusiness
{
    public static $model_name = 'API\RunBuy\CityAnnualFeeHistoryAPI';
    public static $table_name = 'city_annual_fee_history';// 表名称
}