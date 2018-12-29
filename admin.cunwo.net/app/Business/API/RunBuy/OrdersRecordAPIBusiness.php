<?php
// 订单操作记录
namespace App\Business\API\RunBuy;


class OrdersRecordAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\OrdersRecord';
    public static $table_name = 'orders_record';// 表名称
}