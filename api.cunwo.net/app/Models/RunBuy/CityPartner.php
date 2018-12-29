<?php

namespace App\Models\RunBuy;

class CityPartner extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'city_partner';

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
     * 获取城市合伙人的商家-二维
     */

    public function cityPartnerSellers()
    {
        return $this->hasMany('App\Models\RunBuy\Seller', 'city_partner_id', 'id');
    }

    /**
     * 获取城市合伙人的店铺-二维
     */

    public function cityPartnerShops()
    {
        return $this->hasMany('App\Models\RunBuy\Shop', 'city_partner_id', 'id');
    }


    /**
     * 获取城市合伙人的人员-二维
     */

    public function cityPartnerStaffs()
    {
        return $this->hasMany('App\Models\RunBuy\Staff', 'city_partner_id', 'id');
    }

    /**
     * 获取城市合伙人对应的城市分站--一维
     */
    public function cityPartnerCity()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'city_site_id', 'id');
    }

    /**
     * 获取城市合伙人的店铺商品-二维
     */
    public function cityPartnerShopGoods()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoods', 'city_partner_id', 'id');
    }
}
