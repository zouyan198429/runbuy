<?php

namespace App\Models\RunBuy;

class TablePerson extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'table_person';


    // 是否开启1未开启2已开启
    public $isOpenArr = [
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
        return $this->isOpenArr[$this->is_open] ?? '';
    }

    /**
     * 获取桌位人数分类的桌位-二维
     */
    public function tables()
    {
        return $this->hasMany('App\Models\RunBuy\Tables', 'table_person_id', 'id');
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
     * 获取对应的商家--一维
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

    /**
     * 获取对应的排号前缀--一维
     */
    public function numPrefix()
    {
        return $this->belongsTo('App\Models\RunBuy\NumPrefix', 'prefix_id', 'id');
    }
}
