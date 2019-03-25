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

    // 状态1已关闭2待确认4成功8失败
    public $statusArr = [
        '1' => '已关闭',
        '2' => '待确认',
        '4' => '成功',
        '8' => '失败',
    ];

    // 支付方式1余额支付2微信支付
    public $payTypeArr = [
        '1' => '余额支付',
        '2' => '微信支付',
    ];

    // 表里没有的字段
    protected $appends = ['type_text', 'status_text', 'pay_type_text'];

    /**
     * 获取操作类型文字
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        return $this->typeArr[$this->type] ?? '';
    }

    /**
     * 获取状态文字
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->statusArr[$this->status] ?? '';
    }

    /**
     * 获取支付方式文字
     *
     * @return string
     */
    public function getPayTypeTextAttribute()
    {
        return $this->payTypeArr[$this->pay_type] ?? '';
    }

    /**
     * 获取钱包操作记录的用户--一维
     */
    public function staff()
    {
        return $this->belongsTo('App\Models\RunBuy\Staff', 'staff_id', 'id');
    }

    /**
     * 获取钱包操作记录的订单--一维
     */
    public function order()
    {
        return $this->belongsTo('App\Models\RunBuy\Orders', 'pay_order_no', 'order_no');
    }

}
