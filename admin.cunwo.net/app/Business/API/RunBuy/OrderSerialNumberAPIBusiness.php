<?php
// 下单号和付款单号
namespace App\Business\API\RunBuy;


class OrderSerialNumberAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\OrderSerialNumber';
    public static $table_name = 'order_serial_number';// 表名称
}