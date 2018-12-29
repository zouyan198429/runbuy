<?php
// 钱包操作记录
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIWalletRecordBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\WalletRecordAPI';
}