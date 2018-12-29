<?php
// 支付方式[一级分类]
namespace App\Business\API\RunBuy;


class PayTypeAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\PayType';
    public static $table_name = 'pay_type';// 表名称
}