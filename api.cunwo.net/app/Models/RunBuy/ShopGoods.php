<?php

namespace App\Models\RunBuy;

class ShopGoods extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'shop_goods';

    // 热销1非热销2热销
    public $isHotArr = [
        '1' => '非热销',
        '2' => '热销',
    ];

    // 是否上架1上架2下架
    public $isSaleArr = [
        '1' => '上架',
        '2' => '下架',
    ];

    // 表里没有的字段
    protected $appends = ['is_hot_text', 'is_sale_text'];

    /**
     * 获取热销文字
     *
     * @return string
     */
    public function getIsHotTextAttribute()
    {
        return $this->isHotArr[$this->is_hot] ?? '';
    }

    /**
     * 获取是否上架文字
     *
     * @return string
     */
    public function getIsSaleTextAttribute()
    {
        return $this->isSaleArr[$this->is_sale] ?? '';
    }

    /**
     * 获取商品的店铺商品属性-二维
     */
    public function props()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsProps', 'goods_id', 'id');
    }

    /**
     * 获取商品的店铺商品价格属性-二维
     */
    public function priceProps()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsPrices', 'goods_id', 'id');
    }

    /**
     * 获取商品的购物车-二维
     */
    public function carts()
    {
        return $this->hasMany('App\Models\RunBuy\Cart', 'goods_id', 'id');
    }

    /**
     * 获取商品的订单商品-二维
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\RunBuy\OrdersGoods', 'goods_id', 'id');
    }

    /**
     * 获取商品的订单商品-二维
     */
    public function orderDoingGoods()
    {
        return $this->hasMany('App\Models\RunBuy\OrdersGoodsDoing', 'goods_id', 'id');
    }

    /**
     * 获取店铺商品对应的城市分站--一维
     */
    public function city()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'city_site_id', 'id');
    }

    /**
     * 获取店铺商品对应的城市合伙人--一维
     */
    public function cityPartner()
    {
        return $this->belongsTo('App\Models\RunBuy\CityPartner', 'city_partner_id', 'id');
    }

    /**
     * 获取店铺商品对应的商家--一维
     */
    public function seller()
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
     * 获取店铺商品对应的分类--一维
     */
    public function type()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopGoodsType', 'type_id', 'id');
    }

}
