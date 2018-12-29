<?php

namespace App\Models\RunBuy;

class Orders extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'orders';

    // 订单类型1普通订单/父订单4子订单
    public $orderTypeArr = [
        '1' => '普通订单/父订单',
        '4' => '子订单',
    ];

    // 是否有子订单0无1有
    public $hasSonOrderArr = [
        '0' => '无',
        '1' => '有',
    ];

    // 状态1待支付2等待接单4取货或配送中8订单完成16作废
    public $statusArr = [
        '1' => '待支付',
        '2' => '待接单',
        '4' => '已取货配送中',
        '8' => '订单完成',
        '16' => '作废',
    ];

    // 支付方式1余额支付2在线支付
    public $payTypeArr = [
        '1' => '余额支付',
        '2' => '在线支付',
    ];

    // 是否支付跑腿费0未支付1已支付
    public $payRunPriceArr = [
        '0' => '未支付',
        '1' => '已支付',
    ];

    // 是否退费0未退费1已退费
    public $hasRefundArr = [
        '0' => '未退费',
        '1' => '已退费',
    ];

    // 表里没有的字段
    protected $appends = ['order_type_text', 'has_son_order_text', 'status_text', 'pay_type_text', 'pay_run_price_text', 'has_refund_text'];

    /**
     * 获取订单类型文字
     *
     * @return string
     */
    public function getOrderTypeTextAttribute()
    {
        return $this->orderTypeArr[$this->order_type] ?? '';
    }

    /**
     * 获取子订单文字
     *
     * @return string
     */
    public function getHasSonOrderTextAttribute()
    {
        return $this->hasSonOrderArr[$this->has_son_order] ?? '';
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
     * 获取是否支付跑腿费文字
     *
     * @return string
     */
    public function getPayRunPriceTextAttribute()
    {
        return $this->payRunPriceArr[$this->pay_run_price] ?? '';
    }

    /**
     * 获取是否退费文字
     *
     * @return string
     */
    public function getHasRefundTextAttribute()
    {
        return $this->hasRefundArr[$this->has_refund] ?? '';
    }
}
