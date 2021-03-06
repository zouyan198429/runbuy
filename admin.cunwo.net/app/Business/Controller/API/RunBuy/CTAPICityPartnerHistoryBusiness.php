<?php
// 城市合伙人历史
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICityPartnerHistoryBusiness extends CTAPICityPartnerBusiness
{
    public static $model_name = 'API\RunBuy\CityPartnerHistoryAPI';
    public static $table_name = 'city_partner_history';// 表名称

}