<?php
// 订单操作记录
namespace App\Business\API\RunBuy;


class OrdersRecordDoingAPIBusiness extends OrdersRecordAPIBusiness
{
    public static $model_name = 'RunBuy\OrdersRecordDoing';
    public static $table_name = 'orders_record_doing';// 表名称
}