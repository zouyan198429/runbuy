<?php
// 下单号和付款单号--当前的记录
namespace App\Business\API\RunBuy;


class OrderSerialNumberDoingAPIBusiness extends OrderSerialNumberAPIBusiness
{
    public static $model_name = 'RunBuy\OrderSerialNumberDoing';
    public static $table_name = 'order_serial_number_doing';// 表名称
}