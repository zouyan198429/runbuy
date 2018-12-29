<?php
// 订单操作记录
namespace App\Business\DB\RunBuy;

/**
 *
 */
class OrdersRecordDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\OrdersRecord';
    public static $table_name = 'orders_record';// 表名称
}
