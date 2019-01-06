<?php

namespace App\Models\RunBuy;

class ShopGoodsProps extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'shop_goods_props';

    // 是否多选属性[下单时]0不是1是
    public $isMultiArr = [
        '0' => '单选',
        '1' => '多选',
    ];

    // 是否必选[下单时]0不是1是
    public $isMustArr = [
        '0' => '可填',
        '1' => '必填',
    ];

    // 表里没有的字段
    protected $appends = ['is_multi_text' , 'is_must_text'];

    /**
     * 多选状态文字
     *
     * @return string
     */
    public function getIsMultiTextAttribute()
    {
        return $this->isMultiArr[$this->is_multi] ?? '';
    }

    /**
     * 获取必选文字
     *
     * @return string
     */
    public function getIsMustTextAttribute()
    {
        return $this->isMustArr[$this->is_must] ?? '';
    }

    /**
     * 获取店铺商品属性的购物车属性-二维
     */
    public function cartsProps()
    {
        return $this->hasMany('App\Models\RunBuy\CartGoodsProps', 'goods_props_id', 'id');
    }

    /**
     * 获取店铺商品属性的订单商品-二维
     */
    public function orderGoodsProps()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsProps', 'goods_props_id', 'id');
    }

    /**
     * 获取店铺商品属性的订单商品-二维
     */
    public function orderGoodsPropsDoing()
    {
        return $this->hasMany('App\Models\RunBuy\OrderGoodsPropsDoing', 'goods_props_id', 'id');
    }


    /**
     * 获取属性对应的主名称词--一维
     */
    public function propName()
    {
        return $this->belongsTo('App\Models\RunBuy\Names', 'prop_names_id', 'id');
    }

    /**
     * 获取属性值对应的主名称词--一维
     */
    public function propValName()
    {
        return $this->belongsTo('App\Models\RunBuy\Names', 'prop_val_names_id', 'id');
    }

    /**
     * 获取属性id对应的属性--一维
     */
    public function prop()
    {
        return $this->belongsTo('App\Models\RunBuy\Prop', 'prop_id', 'id');
    }

    /**
     * 获取属性值id对应的属性值--一维
     */
    public function propVal()
    {
        return $this->belongsTo('App\Models\RunBuy\PropVal', 'prop_val_id', 'id');
    }

}
