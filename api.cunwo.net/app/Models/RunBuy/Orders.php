<?php

namespace App\Models\RunBuy;

class Orders extends BasePublicModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'orders';

    // 订单类型1普通订单/父订单4子订单
    public $orderTypeArr = [
        '1' => '普通订单/父订单',
        '4' => '子订单',
    ];

    // 是否有子订单0无1有
    public $hasSonOrderArr = [
        '0' => '无',
        '1' => '有',
    ];

    // 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
    public $statusArr = [
        '1' => '待支付',
        '2' => '待接单',
        '4' => '已取货配送中',
        '8' => '订单完成',
        '16' => '系统取消',
        '32' => '用户取消',
        '64' => '完成',
    ];

    // 支付方式1余额支付2在线支付
    public $payTypeArr = [
        '1' => '余额支付',
        '2' => '在线支付',
    ];

    // 是否支付跑腿费0未支付1已支付
    public $payRunPriceArr = [
        '0' => '未支付',
        '1' => '已支付',
    ];

    // 是否退费0未退费1已退费2待退费
    public $hasRefundArr = [
        '0' => '未退费',
        '1' => '已退费',
        '2' => '待退费',
    ];

    // 表里没有的字段
    protected $appends = ['order_type_text', 'has_son_order_text', 'status_text', 'pay_type_text', 'pay_run_price_text', 'has_refund_text'];

    /**
     * 获取订单类型文字
     *
     * @return string
     */
    public function getOrderTypeTextAttribute()
    {
        return $this->orderTypeArr[$this->order_type] ?? '';
    }

    /**
     * 获取子订单文字
     *
     * @return string
     */
    public function getHasSonOrderTextAttribute()
    {
        return $this->hasSonOrderArr[$this->has_son_order] ?? '';
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
     * 获取支付方式文字
     *
     * @return string
     */
    public function getPayTypeTextAttribute()
    {
        return $this->payTypeArr[$this->pay_type] ?? '';
    }

    /**
     * 获取是否支付跑腿费文字
     *
     * @return string
     */
    public function getPayRunPriceTextAttribute()
    {
        return $this->payRunPriceArr[$this->pay_run_price] ?? '';
    }

    /**
     * 获取是否退费文字
     *
     * @return string
     */
    public function getHasRefundTextAttribute()
    {
        return $this->hasRefundArr[$this->has_refund] ?? '';
    }

    /**
     * 获取订单的操作记录-二维
     */
    public function ordersRecords()
    {
        return $this->hasMany('App\Models\RunBuy\OrdersRecord', 'order_no', 'order_no');
    }

    /**
     * 获取订单的商品-二维
     */
    public function ordersGoods()
    {
        return $this->hasMany('App\Models\RunBuy\OrdersGoods', 'order_no', 'order_no');
    }

    /**
     * 获取订单的钱包操作记录-二维
     */
    public function walletRecord()
    {
        return $this->hasMany('App\Models\RunBuy\WalletRecord', 'pay_order_no', 'order_no');
    }


    /**
     * 获取订单的会员历史--一维
     */
    public function staffHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\StaffHistory', 'staff_id_history', 'id');
    }

    /**
     * 获取订单的城市分站历史--一维
     */
    public function cityHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\CityHistory', 'city_site_id_history', 'id');
    }

    /**
     * 获取订单的城市合伙人历史--一维
     */
    public function partnerHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\CityPartnerHistory', 'city_partner_id_history', 'id');
    }

    /**
     * 获取订单的商家历史--一维
     */
    public function sellerHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\SellerHistory', 'seller_id_history', 'id');
    }

    /**
     * 获取订单对应的店铺历史--一维
     */
    public function shopHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\ShopHistory', 'shop_id_history', 'id');
    }


    /**
     * 获取订单对应的收货地址历史--一维
     */
    public function addrHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\CommonAddrHistory', 'addr_id_history', 'id');
    }


}
