<?php
// 商家钱包操作记录
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSellerWalletRecordBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\SellerWalletRecord';

}