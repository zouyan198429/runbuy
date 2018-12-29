<?php

namespace App\Models\RunBuy;

class PayConfig extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'pay_config';

    // 拥有者类型1平台2城市分站4城市代理8商家16店铺32快跑人员64用户
    public $owerTypeArr = [
        '1' => '平台',
        '2' => '城市分站',
        '4' => '城市代理',
        '8' => '商家',
        '16' => '店铺',
        '32' => '快跑人员',
        '64' => '用户',
    ];

    // 状态1待审核2正常4未通过8过期
    public $statusArr = [
        '1' => '待审核',
        '2' => '正常',
        '3' => '未通过',
        '8' => '过期',
    ];

    // 是否冻结 0正常 1冻结
    public $isFrozenArr = [
        '0' => '正常',
        '1' => '冻结',
    ];

    // 表里没有的字段
    protected $appends = ['ower_type_text', 'status_text', 'is_frozen_text'];

    /**
     * 获取拥有者类型文字
     *
     * @return string
     */
    public function getOwerTypeTextAttribute()
    {
        return $this->owerTypeArr[$this->ower_type] ?? '';
    }

    /**
     * 获取状态文字
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->statusArr[$this->status] ?? '';
    }

    /**
     * 获取是否冻结文字
     *
     * @return string
     */
    public function getIsFrozenTextAttribute()
    {
        return $this->isFrozenArr[$this->is_frozen] ?? '';
    }
}
