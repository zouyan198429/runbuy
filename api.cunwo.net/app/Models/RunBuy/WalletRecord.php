<?php

namespace App\Models\RunBuy;

class WalletRecord extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'wallet_record';

    // 操作类型1充值2提现4付款8退款16冻结32解冻
    public $typeArr = [
        '1' => '充值',
        '2' => '提现',
        '4' => '付款',
        '8' => '退款',
        '16' => '冻结',
        '32' => '解冻',
    ];


    // 表里没有的字段
    protected $appends = ['type_text'];

    /**
     * 获取操作类型文字
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        return $this->typeArr[$this->type] ?? '';
    }
}
