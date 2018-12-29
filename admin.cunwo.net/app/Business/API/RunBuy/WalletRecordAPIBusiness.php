<?php
// 钱包操作记录
namespace App\Business\API\RunBuy;


class WalletRecordAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\WalletRecord';
    public static $table_name = 'wallet_record';// 表名称
}