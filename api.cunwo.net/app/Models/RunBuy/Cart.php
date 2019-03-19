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
     * 获取店铺对应的商家--一维
     */
    public function shopSeller()
    {
        return $this->belongsTo('App\Models\RunBuy\Seller', 'seller_id', 'id');
    }

    /**
     * 获取店铺商品对应的店铺--一维
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\RunBuy\Shop', 'shop_id', 'id');
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
