<?php
// 店铺等级历史
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIGradeTableHistoryBusiness extends CTAPIGradeTableBusiness
{
    public static $model_name = 'API\RunBuy\GradeTableHistoryAPI';
    public static $table_name = 'grade_table_history';// 表名称
}