<?php
// 提现/退款(非订单)
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPayOutBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\PayOut';

}