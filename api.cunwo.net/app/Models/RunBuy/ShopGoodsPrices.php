<?php

namespace App\Models\RunBuy;

class ShopGoodsPrices extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'shop_goods_prices';

    /**
     * 获取店铺商品价格的购物车-二维
     */
    public function carts()
    {
        return $this->hasMany('App\Models\RunBuy\Cart', 'prop_price_id', 'id');
    }

    /**
     * 获取店铺商品价格的订单商品-二维
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\RunBuy\OrdersGoods', 'prop_price_id', 'id');
    }

    /**
     * 获取店铺商品价格的订单商品-二维
     */
    public function orderGoodsDoing()
    {
        return $this->hasMany('App\Models\RunBuy\OrdersGoodsDoing', 'prop_price_id', 'id');
    }

    /**
     * 获取属性对应的主名称词--一维
     */
    public function propName()
    {
        return $this->belongsTo('App\Models\RunBuy\Names', 'prop_names_id', 'id');
    }

    /**
     * 获取属性值对应的主名称词--一维
     */
    public function propValName()
    {
        return $this->belongsTo('App\Models\RunBuy\Names', 'prop_val_names_id', 'id');
    }

    /**
     * 获取属性id对应的属性--一维
     */
    public function prop()
    {
        return $this->belongsTo('App\Models\RunBuy\Prop', 'prop_id', 'id');
    }

    /**
     * 获取属性值id对应的属性值--一维
     */
    public function propVal()
    {
        return $this->belongsTo('App\Models\RunBuy\PropVal', 'prop_val_id', 'id');
    }
}
