<?php
// 店铺每日结算主订单
namespace App\Business\DB\RunBuy;

/**
 *
 */
class CountShopDayOrdersDoingDBBusiness extends CountShopDayOrdersDBBusiness
{
    public static $model_name = 'RunBuy\CountShopDayOrdersDoing';
    public static $table_name = 'count_shop_day_orders_doing';// 表名称
}
