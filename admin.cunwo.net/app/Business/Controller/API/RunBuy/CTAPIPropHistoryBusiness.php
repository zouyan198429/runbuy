<?php
// 属性历史
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIPropHistoryBusiness extends CTAPIPropBusiness
{
    public static $model_name = 'API\RunBuy\PropHistoryAPI';
    public static $table_name = 'prop_history';// 表名称

}