<?php
// 统计订单
namespace App\Business\API\RunBuy;


class CountOrdersGrabAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\CountOrdersGrab';
    public static $table_name = 'count_orders_grab';// 表名称
}