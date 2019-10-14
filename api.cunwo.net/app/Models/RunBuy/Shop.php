<?php

namespace App\Models\RunBuy;

class Shop extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'shop';

    // 状态0待审核1审核通过2审核未通过4冻结(禁用)
    public $statusArr = [
        '0' => '待审核',
        '1' => '审核通过',
        '2' => '审核未通过',
        '4' => '冻结',
    ];

    // status_business 经营状态  1营业中 2 歇业中 4 停业[店铺人工操作 8  关业[店铺平台操作]
    public $statusBusinessArr = [
        '1' => '营业中',
        '2' => '歇业中',
        '4' => '息业中',// '停业中',
        '8' => '关业中',
    ];

    // 表里没有的字段
    protected $appends = ['status_text', 'status_business_text'];

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
     * 获取状态文字
     *
     * @return string
     */
    public function getStatusBusinessTextAttribute()
    {
        return $this->statusBusinessArr[$this->status_business] ?? '';
    }

    // 多对多
    /**
     * 店铺的标签[通过中间表 shop_label 多对多]
     */
    public function labels()
    {
        // return $this->belongsToMany('App\Models\test\Role')->withPivot('notice', 'id')->withTimestamps();
        // return $this->belongsToMany('App\Models\test\Role', 'user_roles');// 重写-关联关系连接表的表名
        // 自定义该表中字段的列名;第三个参数是你定义关联关系模型的外键名称，第四个参数你要连接到的模型的外键名称
        return $this->belongsToMany(
            'App\Models\RunBuy\Labels'
            , 'shop_label'
            , 'shop_id'
            , 'label_id'
        )->withPivot('id', 'operate_staff_id', 'operate_staff_id_history')->withTimestamps();
    }


    /**
     * 获取店铺的人员-二维
     */
    public function shopStaffs()
    {
        return $this->hasMany('App\Models\RunBuy\Staff', 'shop_id', 'id');
    }

    /**
     * 获取店铺的营业时间-二维
     */
    public function openTimes()
    {
        return $this->hasMany('App\Models\RunBuy\ShopOpenTime', 'shop_id', 'id');
    }

    /**
     * 获取店铺的店铺商品-二维
     */
    public function shopGoods()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoods', 'shop_id', 'id');
    }

    /**
     * 获取店铺的店铺商品分类-二维
     */
    public function goodsTypes()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoodsType', 'shop_id', 'id');
    }

    /**
     * 获取店铺的桌位分类(按人数)-二维
     */
    public function tablePersons()
    {
        return $this->hasMany('App\Models\RunBuy\TablePerson', 'shop_id', 'id');
    }

    /**
     * 获取店铺的桌位-二维
     */
    public function tables()
    {
        return $this->hasMany('App\Models\RunBuy\Tables', 'shop_id', 'id');
    }

    /**
     * 获取店铺对应的城市分站--一维
     */
    public function shopCity()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'city_site_id', 'id');
    }

    /**
     * 获取店铺对应的城市合伙人--一维
     */
    public function shopCityPartner()
    {
        return $this->belongsTo('App\Models\RunBuy\CityPartner', 'city_partner_id', 'id');
    }

    /**
     * 获取店铺对应的商家--一维
     */
    public function shopSeller()
    {
        return $this->belongsTo('App\Models\RunBuy\Seller', 'seller_id', 'id');
    }

    /**
     * 获取店铺对应的店铺分类--一维
     */
    public function shopType()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopType', 'shop_type_id', 'id');
    }
}
