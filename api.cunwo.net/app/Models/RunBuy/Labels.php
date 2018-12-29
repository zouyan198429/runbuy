<?php

namespace App\Models\RunBuy;

class Labels extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'labels';

    // 多对多
    /**
     * 标签的店铺[通过中间表 shop_label 多对多]
     */
    public function shops()
    {
        // return $this->belongsToMany('App\Models\test\Role')->withPivot('notice', 'id')->withTimestamps();
        // return $this->belongsToMany('App\Models\test\Role', 'user_roles');// 重写-关联关系连接表的表名
        // 自定义该表中字段的列名;第三个参数是你定义关联关系模型的外键名称，第四个参数你要连接到的模型的外键名称
        return $this->belongsToMany(
            'App\Models\RunBuy\Shop'
            , 'shop_label'
            , 'label_id'
            , 'shop_id'
        )->withPivot('id', 'operate_staff_id', 'operate_staff_id_history')->withTimestamps();
    }

}
