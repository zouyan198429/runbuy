<?php

namespace App\Models\RunBuy;

class OrdersDoing extends Orders
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'orders_doing';


    /**
     * 获取订单的操作记录-二维
     */
    public function ordersRecords()
    {
        return $this->hasMany('App\Models\RunBuy\OrdersRecordDoing', 'order_no', 'order_no');
    }

    /**
     * 获取订单的商品-二维
     */
    public function ordersGoods()
    {
        return $this->hasMany('App\Models\RunBuy\OrdersGoodsDoing', 'order_no', 'order_no');
    }
}
