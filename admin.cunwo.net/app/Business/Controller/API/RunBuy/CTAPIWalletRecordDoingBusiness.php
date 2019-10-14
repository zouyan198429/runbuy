<?php
// 钱包操作记录
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIWalletRecordDoingBusiness extends CTAPIWalletRecordBusiness
{
    public static $model_name = 'API\RunBuy\WalletRecordDoingAPI';
    public static $table_name = 'wallet_record_doing';// 表名称
}