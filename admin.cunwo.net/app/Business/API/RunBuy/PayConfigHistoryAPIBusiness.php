<?php
// 支付配置历史
namespace App\Business\API\RunBuy;


class PayConfigHistoryAPIBusiness extends PayConfigAPIBusiness
{
    public static $model_name = 'RunBuy\PayConfigHistory';
    public static $table_name = 'pay_config_history';// 表名称
}