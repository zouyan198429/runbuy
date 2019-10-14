<?php
// 城市商家业务收费标准历史
namespace App\Business\API\RunBuy;


class CityRateHistoryAPIBusiness extends CityRateAPIBusiness
{
    public static $model_name = 'RunBuy\CityRateHistory';
    public static $table_name = 'city_rate_history';// 表名称
}