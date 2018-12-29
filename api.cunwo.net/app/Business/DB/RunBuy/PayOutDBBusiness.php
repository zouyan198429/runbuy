<?php
// 提现/退款(非订单)
namespace App\Business\DB\RunBuy;

/**
 *
 */
class PayOutDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\PayOut';
    public static $table_name = 'pay_out';// 表名称
}
