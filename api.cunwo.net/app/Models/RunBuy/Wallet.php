<?php

namespace App\Models\RunBuy;

class Wallet extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'wallet';

    // 是否冻结 0正常 1冻结
    public $isFrozenArr = [
        '0' => '正常',
        '1' => '冻结',
    ];

    // 表里没有的字段
    protected $appends = ['is_frozen_text'];

    /**
     * 获取冻结状态文字
     *
     * @return string
     */
    public function getIsFrozenTextAttribute()
    {
        return $this->isFrozenArr[$this->is_frozen] ?? '';
    }

    /**
     * 获取对应的人员--一维
     */
    public function staff()
    {
        return $this->belongsTo('App\Models\RunBuy\Staff', 'staff_id', 'id')->withDefault();
    }

}
