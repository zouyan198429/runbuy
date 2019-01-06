<?php

namespace App\Models\RunBuy;

class OrdersGoods extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'orders_goods';

    /**
     * 获取订单商品属性-二维
     */
    public function props()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsProps', 'orders_goods_id', 'id');
    }

    /**
     * 获取对应的店铺商品--一维
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopGoods', 'goods_id', 'id');
    }

    /**
     * 获取对应的店铺商品价格--一维
     */
    public function goodsPrice()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopGoodsPrices', 'prop_price_id', 'id');
    }

}
