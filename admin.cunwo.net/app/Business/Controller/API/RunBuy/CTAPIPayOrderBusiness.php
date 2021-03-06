<?php
// 支付订单
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIPayOrderBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\PayOrderAPI';
    public static $table_name = 'pay_order';// 表名称

}