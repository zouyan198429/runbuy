<?php

namespace App\Models\RunBuy;

use Illuminate\Database\Eloquent\Model;

class CommonAddr extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'common_addr';

    // 性别0未知1男2女
    public $sexArr = [
        '0' => '未知',
        '1' => '男',
        '2' => '女',
    ];

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

    // 是否默认地址1非默认2默认
    public $isDefaultArr = [
        '1' => '非默认',
        '2' => '默认',
    ];

    // 表里没有的字段
    protected $appends = ['ower_type_text', 'sex_text', 'is_default_text'];

    /**
     * 获取性别文字
     *
     * @return string
     */
    public function getSexTextAttribute()
    {
        return $this->sexArr[$this->sex] ?? '';
    }

    /**
     * 获取的类型文字
     *
     * @return string
     */
    public function getOwerTypeTextAttribute()
    {
        return $this->owerTypeArr[$this->ower_type] ?? '';
    }

    /**
     * 获取的是否默认地址文字
     *
     * @return string
     */
    public function getIsDefaultTextAttribute()
    {
        return $this->isDefaultArr[$this->is_default] ?? '';
    }

    /**
     * 获取地址对应的人员--一维
     */
    public function staff()
    {
        return $this->belongsTo('App\Models\RunBuy\Staff', 'ower_id', 'id');
    }
}
