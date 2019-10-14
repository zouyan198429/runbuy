<?php

namespace App\Models\RunBuy;

class Tables extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'tables';


    // 是否开启1未开启2已开启
    public $isOpenArr = [
        '1' => '未开启',
        '2' => '已开启',
    ];
    // 状态1待占桌2已占桌4确认占桌
    public $statusArr = [
        '1' => '待占桌',
        '2' => '已占桌',
        '4' => '确认占桌',
    ];
    // 是否已生成二维码1未生成2已生成
    public $hasQrcodeArr = [
        '1' => '未生成',
        '2' => '已生成',
    ];

    // 表里没有的字段
    protected $appends = ['is_open_text', 'status_text', 'has_qrcode_text'];

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
     * 获取状态文字
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->statusArr[$this->status] ?? '';
    }

    /**
     * 获取是否已生成二维码文字
     *
     * @return string
     */
    public function getHasQrcodeTextAttribute()
    {
        return $this->hasQrcodeArr[$this->has_qrcode] ?? '';
    }

    /**
     * 获取桌位的用餐流-二维
     */
    public function tablesStream()
    {
        return $this->hasMany('App\Models\RunBuy\TablesStream', 'table_id', 'id');
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
     * 获取对应的店铺--一维
     */
    public function shopHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopHistory', 'shop_id', 'id');
    }

    /**
     * 获取对应的桌位人数分类--一维
     */
    public function tablePerson()
    {
        return $this->belongsTo('App\Models\RunBuy\TablePerson', 'table_person_id', 'id');
    }

    /**
     * 获取对应的桌位人数分类--一维
     */
    public function tablePersonHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\TablePersonHistory', 'table_person_id_history', 'id');
    }
}
