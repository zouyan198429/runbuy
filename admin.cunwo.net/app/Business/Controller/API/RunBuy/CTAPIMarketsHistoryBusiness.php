<?php
// 商场历史[一级分类]
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIMarketsHistoryBusiness extends CTAPIMarketsBusiness
{
    public static $model_name = 'API\RunBuy\MarketsHistoryAPI';
    public static $table_name = 'markets_history';// 表名称
}