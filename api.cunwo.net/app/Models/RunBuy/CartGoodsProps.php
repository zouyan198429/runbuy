<?php

namespace App\Models\RunBuy;

class CartGoodsProps extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'cart_goods_props';

    /**
     * 获取对应的购物车-一维
     */
    public function cart()
    {
        return $this->belongsTo('App\Models\RunBuy\Cart', 'cart_id', 'id');
    }

    /**
     * 获取对应的商品属性-一维
     */
    public function goodsProp()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopGoodsProps', 'goods_props_id', 'id');
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
