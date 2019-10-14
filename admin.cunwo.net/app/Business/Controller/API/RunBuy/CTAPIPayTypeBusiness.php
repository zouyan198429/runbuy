<?php
// 支付方式[一级分类]
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIPayTypeBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\PayTypeAPI';
    public static $table_name = 'pay_type';// 表名称

}