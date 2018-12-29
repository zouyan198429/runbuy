<?php

namespace App\Models\RunBuy;

class Seller extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'seller';

    // 状态0待审核1审核通过2审核未通过4冻结(禁用)
    public $statusArr = [
        '0' => '待审核',
        '1' => '审核通过',
        '2' => '审核未通过',
        '4' => '冻结',
    ];

    // 表里没有的字段
    protected $appends = ['status_text'];

    /**
     * 获取状态文字
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->statusArr[$this->status] ?? '';
    }

    /**
     * 获取商家的店铺-二维
     */
    public function sellerShops()
    {
        return $this->hasMany('App\Models\RunBuy\Shop', 'seller_id', 'id');
    }


    /**
     * 获取商家的人员-二维
     */
    public function sellerStaffs()
    {
        return $this->hasMany('App\Models\RunBuy\Staff', 'seller_id', 'id');
    }

    /**
     * 获取商家的店铺商品-二维
     */
    public function sellerShopGoods()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoods', 'seller_id', 'id');
    }

    /**
     * 获取商家的店铺商品分类-二维
     */
    public function shopGoodsTypes()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsType', 'seller_id', 'id');
    }

    /**
     * 获取城商家对应的城市分站--一维
     */
    public function sellerCity()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'city_site_id', 'id');
    }

    /**
     * 获取城商家对应的城市合伙人--一维
     */
    public function sellerCityPartner()
    {
        return $this->belongsTo('App\Models\RunBuy\CityPartner', 'city_partner_id', 'id');
    }
}
