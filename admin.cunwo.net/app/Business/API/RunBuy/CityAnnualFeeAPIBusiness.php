<?php
// 城市商家业务年收费标准
namespace App\Business\API\RunBuy;


class CityAnnualFeeAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\CityAnnualFee';
    public static $table_name = 'city_annual_fee';// 表名称
}