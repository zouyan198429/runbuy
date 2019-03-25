<?php

namespace App\Models\RunBuy;

class OrdersGoods extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'orders_goods';

    /**
     * 获取订单商品属性-二维
     */
    public function props()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsProps', 'orders_goods_id', 'id');
    }

    /**
     * 获取对应的店铺商品--一维
     */
//    public function goods()
//    {
//        return $this->belongsTo('App\Models\RunBuy\ShopGoods', 'goods_id', 'id');
//    }

    /**
     * 获取对应的店铺商品历史--一维
     */
    public function goodsHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopGoodsHistory', 'goods_id_history', 'id');
    }

    /**
     * 获取对应的店铺商品价格--一维
     */
//    public function goodsPrice()
//    {
//        return $this->belongsTo('App\Models\RunBuy\ShopGoodsPrices', 'prop_price_id', 'id');
//    }

    /**
     * 获取对应的店铺商品价格--一维
     */
    public function goodsPriceHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopGoodsPricesHistory', 'prop_price_id_history', 'id');
    }

    /**
     * 获取对应的资源--一维
     */
    public function resourcesHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\ResourceHistory', 'resource_id_history', 'id')->withDefault();
    }

    /**
     * 获取订单的会员历史--一维
     */
    public function staffHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\StaffHistory', 'staff_id_history', 'id');
    }

    /**
     * 获取订单的城市分站历史--一维
     */
    public function cityHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\CityHistory', 'city_site_id_history', 'id');
    }

    /**
     * 获取订单的城市合伙人历史--一维
     */
    public function partnerHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\CityPartnerHistory', 'city_partner_id_history', 'id');
    }

    /**
     * 获取订单的商家历史--一维
     */
    public function sellerHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\SellerHistory', 'seller_id_history', 'id');
    }

    /**
     * 获取订单对应的店铺历史--一维
     */
    public function shopHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopHistory', 'shop_id_history', 'id');
    }
}
