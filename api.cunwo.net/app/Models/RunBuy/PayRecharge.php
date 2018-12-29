<?php

namespace App\Models\RunBuy;

class PayRecharge extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'pay_recharge';

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

    // 操作类型1用户操作2平台操作
    public $operateTypeArr = [
        '1' => '用户操作',
        '2' => '平台操作',
    ];

    // 冻结状态0不用冻结1已冻结2已解冻
    public $frozenStatusArr = [
        '0' => '不用冻结',
        '1' => '已冻结',
        '2' => '已解冻',
    ];

    // 状态1等审核2成功4失败
    public $statusArr = [
        '1' => '待审核',
        '2' => '成功',
        '4' => '失败',
    ];

    // 表里没有的字段
    protected $appends = ['ower_type_text', 'operate_type_text', 'frozen_status_text', 'status_text'];

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
     * 获取操作类型文字
     *
     * @return string
     */
    public function getOperateTypeTextAttribute()
    {
        return $this->operateTypeArr[$this->operate_type] ?? '';
    }

    /**
     * 获取冻结状态文字
     *
     * @return string
     */
    public function getFrozenStatusTextAttribute()
    {
        return $this->frozenStatusArr[$this->frozen_status] ?? '';
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

}
