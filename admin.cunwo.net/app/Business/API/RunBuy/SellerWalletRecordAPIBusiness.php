<?php
// 商家钱包操作记录
namespace App\Business\API\RunBuy;


class SellerWalletRecordAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\SellerWalletRecord';
    public static $table_name = 'seller_wallet_record';// 表名称
}