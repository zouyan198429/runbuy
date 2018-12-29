<?php

namespace App\Models\RunBuy;

class CityPartnerHistory extends CityPartner
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'city_partner_history';

    /**
     * 获取城市合伙人的店铺-二维
     */

    public function cityPartnerShops()
    {
        return $this->hasMany('App\Models\RunBuy\Shop', 'city_partner_id', 'city_partner_id');
    }

    /**
     * 获取城市合伙人的人员-二维
     */

    public function cityPartnerStaffs()
    {
        return $this->hasMany('App\Models\RunBuy\Staff', 'city_partner_id', 'city_partner_id');
    }

    /**
     * 获取城市合伙人的店铺商品-二维
     */
    public function cityPartnerShopGoods()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoods', 'city_partner_id', 'city_partner_id');
    }
}
