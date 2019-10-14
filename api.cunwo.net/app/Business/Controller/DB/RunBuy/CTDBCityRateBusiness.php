<?php
// 城市商家业务收费标准
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCityRateBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\CityRate';

}