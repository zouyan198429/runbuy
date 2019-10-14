<?php
// 城市商家业务年收费标准
namespace App\Business\DB\RunBuy;

/**
 *
 */
class CityAnnualFeeHistoryDBBusiness extends CityAnnualFeeDBBusiness
{
    public static $model_name = 'RunBuy\CityAnnualFeeHistory';
    public static $table_name = 'city_annual_fee_history';// 表名称
}
