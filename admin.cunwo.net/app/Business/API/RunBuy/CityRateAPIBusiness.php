<?php
// 城市商家业务收费标准
namespace App\Business\API\RunBuy;


class CityRateAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\CityRate';
    public static $table_name = 'city_rate';// 表名称
}