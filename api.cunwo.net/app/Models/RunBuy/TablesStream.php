<?php

namespace App\Models\RunBuy;

class TablesStream extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'tables_stream';

    /**
     * 获取对应的桌位--一维
     */
    public function table()
    {
        return $this->belongsTo('App\Models\RunBuy\Tables', 'table_id', 'id');
    }

}
