<?php
// 钱包操作记录
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBWalletRecordBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\WalletRecord';
}