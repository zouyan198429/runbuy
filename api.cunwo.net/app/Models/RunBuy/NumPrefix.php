<?php

namespace App\Models\RunBuy;

class NumPrefix extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'num_prefix';

    /**
     * 获取排号前缀的桌位人数分类-二维
     */
    public function tablePerson()
    {
        return $this->hasMany('App\Models\RunBuy\TablePerson', 'prefix_id', 'id');
    }

}
