<?php
// 城市商家业务年收费标准
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCityAnnualFeeHistoryBusiness extends CTDBCityAnnualFeeBusiness
{
    public static $model_name = 'RunBuy\CityAnnualFeeHistory';

}