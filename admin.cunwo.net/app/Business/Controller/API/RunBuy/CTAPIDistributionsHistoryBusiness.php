<?php
// 分销历史
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIDistributionsHistoryBusiness extends CTAPIDistributionsBusiness
{
    public static $model_name = 'API\RunBuy\DistributionsHistoryAPI';
    public static $table_name = 'distributions_history';// 表名称
}