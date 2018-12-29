<?php

namespace App\Models\RunBuy;

class SellerHistory extends Seller
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'seller_history';

    /**
     * 获取商家的店铺-二维
     */

    public function sellerShops()
    {
        return $this->hasMany('App\Models\RunBuy\Shop', 'seller_id', 'seller_id');
    }

    /**
     * 获取商家的人员-二维
     */

    public function sellerStaffs()
    {
        return $this->hasMany('App\Models\RunBuy\Staff', 'seller_id', 'seller_id');
    }

    /**
     * 获取商家的店铺商品-二维
     */
    public function sellerShopGoods()
    {
        return $this->hasMany('App\Models\RunBuy\ShopGoods', 'seller_id', 'seller_id');
    }

}
