<?php
// 支付订单
namespace App\Business\API\RunBuy;


class PayOrderAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\PayOrder';
    public static $table_name = 'pay_order';// 表名称
}