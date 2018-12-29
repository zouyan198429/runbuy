<?php

namespace App\Models\RunBuy;

class ShopType extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'shop_type';

    /**
     * 获取店铺分类的店铺分类-二维
     */
    public function typeShops()
    {
        return $this->hasMany('App\Models\RunBuy\Shop', 'shop_type_id', 'id');
    }
}
