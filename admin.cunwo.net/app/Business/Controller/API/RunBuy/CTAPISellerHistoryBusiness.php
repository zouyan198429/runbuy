<?php
// 商家历史
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPISellerHistoryBusiness extends CTAPISellerBusiness
{
    public static $model_name = 'API\RunBuy\SellerHistoryAPI';
    public static $table_name = 'seller_history';// 表名称

}