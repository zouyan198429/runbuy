<?php
// 统计订单
namespace App\Business\DB\RunBuy;

/**
 *
 */
class CountOrdersDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\CountOrders';
    public static $table_name = 'count_orders';// 表名称
}
