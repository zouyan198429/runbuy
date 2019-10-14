<?php
// 下单号和付款单号叫号
namespace App\Business\API\RunBuy;


class OrderNumberCallDoingAPIBusiness extends OrderNumberCallAPIBusiness
{
    public static $model_name = 'RunBuy\OrderNumberCallDoing';
    public static $table_name = 'order_number_call_doing';// 表名称
}