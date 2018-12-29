<?php

namespace App\Models\RunBuy;

class OrdersRecord extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'orders_record';

    // 操作类型1用户操作2商家操作4跑腿人员操作8系统操作16其它操作
    public $typeArr = [
        '1' => '用户操作',
        '2' => '商家操作',
        '4' => '跑腿人员操作',
        '8' => '系统操作',
        '16' => '其它操作',
    ];

    // 表里没有的字段
    protected $appends = ['type_text'];

    /**
     * 获取操作类型文字
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        return $this->typeArr[$this->type] ?? '';
    }
}
