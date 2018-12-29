<?php
// 支付配置历史
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPayConfigHistoryBusiness extends CTDBPayConfigBusiness
{
    public static $model_name = 'RunBuy\PayConfigHistory';

}