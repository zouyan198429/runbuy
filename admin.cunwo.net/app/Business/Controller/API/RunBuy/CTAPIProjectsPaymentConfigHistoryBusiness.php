<?php
// 项目收款方式历史
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIProjectsPaymentConfigHistoryBusiness extends CTAPIProjectsPaymentConfigBusiness
{
    public static $model_name = 'API\RunBuy\ProjectsPaymentConfigHistoryAPI';
    public static $table_name = 'projects_payment_config_history';// 表名称
}