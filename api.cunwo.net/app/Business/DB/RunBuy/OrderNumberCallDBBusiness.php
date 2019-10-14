<?php
// 下单号和付款单号叫号
namespace App\Business\DB\RunBuy;

/**
 *
 */
class OrderNumberCallDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\OrderNumberCall';
    public static $table_name = 'order_number_call';// 表名称
}
