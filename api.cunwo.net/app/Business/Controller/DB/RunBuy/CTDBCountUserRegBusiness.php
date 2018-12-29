<?php
// 统计用户注册
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCountUserRegBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\CountUserReg';

}