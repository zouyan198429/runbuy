<?php

namespace App\Models\RunBuy;

class ShopOpenTime extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'shop_open_time';

    // 是否开启1未开启2已开启
    public $isopenArr = [
        '1' => '未开启',
        '2' => '已开启',
    ];

    // 表里没有的字段
    protected $appends = ['is_open_text'];

    /**
     * 获取是否开启文字
     *
     * @return string
     */
    public function getIsOpenTextAttribute()
    {
        return $this->isopenArr[$this->is_open] ?? '';
    }


    /**
     * 获取对应的城市分站--一维
     */
    public function city()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'city_site_id', 'id');
    }

    /**
     * 获取对应的城市合伙人--一维
     */
    public function cityPartner()
    {
        return $this->belongsTo('App\Models\RunBuy\CityPartner', 'city_partner_id', 'id');
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
