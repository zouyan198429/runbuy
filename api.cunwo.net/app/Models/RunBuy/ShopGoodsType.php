<?php

namespace App\Models\RunBuy;

class ShopGoodsType extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'shop_goods_type';

    /**
     * 获取店铺商品分的商品-二维
     */
    public function shopTypeGoods()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoods', 'type_id', 'id');
    }

    /**
     * 获取商品分类对应的商家--一维
     */
    public function seller()
    {
        return $this->belongsTo('App\Models\RunBuy\Seller', 'seller_id', 'id');
    }

    /**
     * 获取商品分类对应的店铺--一维
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\RunBuy\Shop', 'shop_id', 'id');
    }
}
