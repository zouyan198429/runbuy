<?php
// 支付配置
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPayConfigBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\PayConfig';

}