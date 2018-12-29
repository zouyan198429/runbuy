<?php
// 统计订单
namespace App\Business\API\RunBuy;


class CountOrdersAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\CountOrders';
    public static $table_name = 'count_orders';// 表名称
}