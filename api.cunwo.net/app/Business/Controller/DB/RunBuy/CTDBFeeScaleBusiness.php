<?php
// 收费标准
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBFeeScaleBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\FeeScale';

}