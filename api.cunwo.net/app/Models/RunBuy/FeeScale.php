<?php

namespace App\Models\RunBuy;

class FeeScale extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'fee_scale';

    /**
     * 获取对应的城市分站--一维
     */
    public function city()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'city_site_id', 'id')->withDefault();
    }
}
