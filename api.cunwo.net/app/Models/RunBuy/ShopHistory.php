<?php

namespace App\Models\RunBuy;

class ShopHistory extends Shop
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'shop_history';

    /**
     * 获取店铺的人员-二维
     */
    public function shopStaffs()
    {
        return $this->hasMany('App\Models\RunBuy\Staff', 'shop_id', 'shop_id');
    }

    /**
     * 获取商家的店铺商品-二维
     */
    public function shopGoods()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoods', 'shop_id', 'shop_id');
    }
}
