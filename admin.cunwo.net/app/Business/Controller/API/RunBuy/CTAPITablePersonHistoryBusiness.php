<?php
// 桌位人数分类历史[一级分类]
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPITablePersonHistoryBusiness extends CTAPITablePersonBusiness
{
    public static $model_name = 'API\RunBuy\TablePersonHistoryAPI';
    public static $table_name = 'table_person_history';// 表名称
}