<?php

namespace App\Models\RunBuy;

class OrderGoodsPropsDoing extends OrderGoodsProps
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order_goods_props_doing';

    /**
     * 获取订单商品--一维
     */
    public function orderGoods()
    {
        return $this->belongsTo('App\Models\RunBuy\OrdersGoodsDoing', 'orders_goods_id', 'id');
    }
}
