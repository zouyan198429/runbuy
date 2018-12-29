<?php
// 商家
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSellerBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\Seller';
}