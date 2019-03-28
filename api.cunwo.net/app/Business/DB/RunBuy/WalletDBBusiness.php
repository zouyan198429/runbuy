<?php
// 钱包
namespace App\Business\DB\RunBuy;

/**
 *
 */
class WalletDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Wallet';
    public static $table_name = 'wallet';// 表名称


    /**
     * 获得校验字串
     *
     * @param string  $company_id 企业id
     * @param string $id id
     * @param string $operate_staff_id 操作人id
     * @param string $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  string 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function getCheckKey($staff_id , $total_money, $frozen_money, $avail_money){
        $md5Arr = [
            $walletInfo->staff_id,
            $walletInfo->total_money,
            $walletInfo->frozen_money,
            $walletInfo->avail_money,
        ];
        $check_key = strtoupper(md5(implode('-', $md5Arr)));
        return $check_key;
    }
}
