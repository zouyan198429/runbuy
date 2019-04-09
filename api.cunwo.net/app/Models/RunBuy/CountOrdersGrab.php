<?php

namespace App\Models\RunBuy;

class CountOrdersGrab extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'count_orders_grab';

    // balance_status 结算状态1待结算2已结算3作废
    public $balanceStatusArr = [
        '1' => '待结算',
        '2' => '已结算',
        '3' => '已作废',
    ];

    // 表里没有的字段
    protected $appends = ['balance_status_text'];

    /**
     * 获取订单类型文字
     *
     * @return string
     */
    public function getBalanceStatusTextAttribute()
    {
        return $this->balanceStatusArr[$this->balance_status] ?? '';
    }


}
