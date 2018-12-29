<?php
// 统计跑腿注册
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICountSenderRegBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\CountSenderRegAPI';

}