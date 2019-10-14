<?php
// 商家钱包
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSellerWalletBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\SellerWallet';

}