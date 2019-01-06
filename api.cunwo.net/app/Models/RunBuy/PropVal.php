<?php

namespace App\Models\RunBuy;

class PropVal extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'prop_val';

    /**
     * 获取属性值的店铺商品属性-二维
     */
    public function goodsProps()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsProps', 'prop_val_id', 'id');
    }

    /**
     * 获取属性的店铺商品价格-二维
     */
    public function pricesProps()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsPrices', 'prop_val_id', 'id');
    }

    /**
     * 获取属性的购物车商品属性-二维
     */
    public function cartProps()
    {
        return $this->hasMany('App\Models\RunBuy\CartGoodsProps', 'prop_val_id', 'id');
    }

    /**
     * 获取属性的订单商品属性-二维
     */
    public function orderProps()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsProps', 'prop_val_id', 'id');
    }

    /**
     * 获取属性的订单商品属性-二维
     */
    public function orderDoingProps()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsPropsDoing', 'prop_val_id', 'id');
    }

    /**
     * 获取属性id对应的属性--一维
     */
    public function prop()
    {
        return $this->belongsTo('App\Models\RunBuy\Prop', 'prop_id', 'id');
    }

    /**
     * 获取属性值对应的主名称词--一维
     */
    public function name()
    {
        return $this->belongsTo('App\Models\RunBuy\Names', 'names_id', 'id');
    }
}
