<?php

namespace App\Models\RunBuy;

use Illuminate\Database\Eloquent\Model;

class CommonAddrHistory extends CommonAddr
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'common_addr_history';

    /**
     * 获取使用地址的订单-二维
     */
    public function orders()
    {
        return $this->hasMany('App\Models\RunBuy\Orders', 'addr_id_history', 'id');
    }

}
