<?php
// 钱包
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBWalletBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\Wallet';
}