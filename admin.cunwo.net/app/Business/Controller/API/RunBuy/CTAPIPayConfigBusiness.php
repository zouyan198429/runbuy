<?php
// 支付配置
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIPayConfigBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\PayConfigAPI';
    public static $table_name = 'pay_config';// 表名称

}