<?php
// 送货速度[一级分类]
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSpeedTimeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\SpeedTime';
}