<?php
// 店铺品牌历史[一级分类]
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIBrandsHistoryBusiness extends CTAPIBrandsBusiness
{
    public static $model_name = 'API\RunBuy\BrandsHistoryAPI';
    public static $table_name = 'brands_history';// 表名称
}