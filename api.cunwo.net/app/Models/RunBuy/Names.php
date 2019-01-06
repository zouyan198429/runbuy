<?php

namespace App\Models\RunBuy;

class Names extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'names';

    /**
     * 获取名称词的属性-二维
     */
    public function prop()
    {
        return $this->hasMany('App\Models\RunBuy\Prop', 'names_id', 'id');
    }

    /**
     * 获取名称词的属性值-二维
     */
    public function propVal()
    {
        return $this->hasMany('App\Models\RunBuy\PropVal', 'names_id', 'id');
    }

    /**
     * 获取属性名称词的店铺商品属性-二维
     */
    public function propProps()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsProps', 'prop_names_id', 'id');
    }


    /**
     * 获取属性名称词的店铺商品属性-二维
     */
    public function propValProps()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsProps', 'prop_val_names_id', 'id');
    }

    /**
     * 获取属性名称词的店铺商品价格-二维
     */
    public function propPrices()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsPrices', 'prop_names_id', 'id');
    }

    /**
     * 获取属性值名称词的店铺商品价格-二维
     */
    public function propValPrices()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsPrices', 'prop_val_names_id', 'id');
    }

    /**
     * 获取属性名称词的购物车商品属性-二维
     */
    public function propCars()
    {
        return $this->hasMany('App\Models\RunBuy\CartGoodsProps', 'prop_names_id', 'id');
    }

    /**
     * 获取属性值名称词的购物车商品属性-二维
     */
    public function propValCars()
    {
        return $this->hasMany('App\Models\RunBuy\CartGoodsProps', 'prop_val_names_id', 'id');
    }

    /**
     * 获取属性名称词的订单商品属性-二维
     */
    public function propOrders()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsProps', 'prop_names_id', 'id');
    }

    /**
     * 获取属性值名称词的订单商品属性-二维
     */
    public function propValOrders()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsProps', 'prop_val_names_id', 'id');
    }

    /**
     * 获取属性名称词的订单商品属性-二维
     */
    public function propDoingOrders()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsPropsDoing', 'prop_names_id', 'id');
    }

    /**
     * 获取属性值名称词的订单商品属性-二维
     */
    public function propValDoingOrders()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsPropsDoing', 'prop_val_names_id', 'id');
    }

}
