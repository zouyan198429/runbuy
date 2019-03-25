<?php

namespace App\Models\RunBuy;

class OrdersGoodsDoing extends OrdersGoods
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'orders_goods_doing';

    /**
     * 获取订单商品属性-二维
     */
    public function props()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsPropsDoing', 'orders_goods_id', 'id');
    }

}
