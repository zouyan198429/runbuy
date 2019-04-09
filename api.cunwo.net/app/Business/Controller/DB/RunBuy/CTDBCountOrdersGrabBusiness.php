<?php
// 统计订单
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCountOrdersGrabBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\CountOrdersGrab';

}