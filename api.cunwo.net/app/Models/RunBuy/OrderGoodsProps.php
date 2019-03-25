<?php

namespace App\Models\RunBuy;

class OrderGoodsProps extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order_goods_props';

    /**
     * 获取订单商品--一维
     */
    public function orderGoods()
    {
        return $this->belongsTo('App\Models\RunBuy\OrdersGoods', 'orders_goods_id', 'id');
    }

    /**
     * 获取对应的商品属性-一维
     */
//    public function goodsProp()
//    {
//        return $this->belongsTo('App\Models\RunBuy\ShopGoodsProps', 'goods_props_id', 'id');
//    }

    /**
     * 获取对应的商品属性-一维
     */
    public function goodsPropHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopGoodsPropsHistory', 'goods_props_id_history', 'id');
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
//    public function prop()
//    {
//        return $this->belongsTo('App\Models\RunBuy\Prop', 'prop_id', 'id');
//    }


    /**
     * 获取属性值id对应的属性值--一维
     */
//    public function propVal()
//    {
//        return $this->belongsTo('App\Models\RunBuy\PropVal', 'prop_val_id', 'id');
//    }

}
