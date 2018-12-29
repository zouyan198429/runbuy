<?php
// 支付订单
namespace App\Business\DB\RunBuy;

/**
 *
 */
class PayOrderDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\PayOrder';
    public static $table_name = 'pay_order';// 表名称
}
