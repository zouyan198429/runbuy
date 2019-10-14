<?php
// 城市商家业务收费标准历史
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCityRateHistoryBusiness extends CTDBCityRateBusiness
{
    public static $model_name = 'RunBuy\CityRateHistory';

}