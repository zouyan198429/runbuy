<?php
// 属性值历史
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIPropValHistoryBusiness extends CTAPIPropValBusiness
{
    public static $model_name = 'API\RunBuy\PropValHistoryAPI';
    public static $table_name = 'prop_val_history';// 表名称

}