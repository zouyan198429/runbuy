<?php
// 店铺每日结算主订单
namespace App\Business\DB\RunBuy;

/**
 *
 */
class CountShopDayOrdersDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\CountShopDayOrders';
    public static $table_name = 'count_shop_day_orders';// 表名称
}
