<?php

namespace App\Models\RunBuy;

class Prop extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'prop';

    /**
     * 获取属性的属性值-二维
     */
    public function propVals()
    {
        return $this->hasMany('App\Models\RunBuy\PropVal', 'prop_id', 'id');
    }

    /**
     * 获取属性的店铺商品属性-二维
     */
    public function goodsProps()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsProps', 'prop_id', 'id');
    }

    /**
     * 获取属性的店铺商品价格-二维
     */
    public function pricesProps()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsPrices', 'prop_id', 'id');
    }

    /**
     * 获取属性的购物车商品属性-二维
     */
    public function cartProps()
    {
        return $this->hasMany('App\Models\RunBuy\CartGoodsProps', 'prop_id', 'id');
    }

    /**
     * 获取属性的订单商品属性-二维
     */
    public function orderProps()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsProps', 'prop_id', 'id');
    }

    /**
     * 获取属性的订单商品属性-二维
     */
    public function orderDoingProps()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsPropsDoing', 'prop_id', 'id');
    }

    /**
     * 获取属性对应的主名称词--一维
     */
    public function name()
    {
        return $this->belongsTo('App\Models\RunBuy\Names', 'names_id', 'id');
    }


    /**
     * 获取店铺对应的城市分站--一维
     */
    public function city()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'city_site_id', 'id');
    }

    /**
     * 获取店铺对应的城市合伙人--一维
     */
    public function cityPartner()
    {
        return $this->belongsTo('App\Models\RunBuy\CityPartner', 'city_partner_id', 'id');
    }

    /**
     * 获取店铺对应的商家--一维
     */
    public function seller()
    {
        return $this->belongsTo('App\Models\RunBuy\Seller', 'seller_id', 'id');
    }

    /**
     * 获取对应的店铺--一维
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\RunBuy\Shop', 'shop_id', 'id');
    }

}
