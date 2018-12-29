<?php
// 提现/退款(非订单)
namespace App\Business\API\RunBuy;


class PayOutAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\PayOut';
    public static $table_name = 'pay_out';// 表名称
}