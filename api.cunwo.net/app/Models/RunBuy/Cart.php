<?php

namespace App\Models\RunBuy;

class Cart extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'cart';

    /**
     * 获取购物车商品属性-二维
     */
    public function props()
    {
        return $this->hasMany('App\Models\RunBuy\CartGoodsProps', 'cart_id', 'id');
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
