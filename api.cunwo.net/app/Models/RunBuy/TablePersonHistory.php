<?php

namespace App\Models\RunBuy;

class TablePersonHistory extends TablePerson
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'table_person_history';

    /**
     * 获取桌位人数分类历史的桌位-二维
     */
//    public function tables()
//    {
//        return $this->hasMany('App\Models\RunBuy\Tables', 'table_person_id_history', 'id');
//    }
}
