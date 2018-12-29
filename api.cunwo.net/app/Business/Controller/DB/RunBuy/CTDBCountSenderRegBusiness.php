<?php
// 统计跑腿注册
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCountSenderRegBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\CountSenderReg';

}