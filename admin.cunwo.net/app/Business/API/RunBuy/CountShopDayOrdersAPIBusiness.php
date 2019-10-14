<?php
// 店铺每日结算主订单
namespace App\Business\API\RunBuy;


class CountShopDayOrdersAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\CountShopDayOrders';
    public static $table_name = 'count_shop_day_orders';// 表名称
}