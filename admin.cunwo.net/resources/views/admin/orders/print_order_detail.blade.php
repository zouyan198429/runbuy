<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>打印订单</title>
    <style type="text/css" id="orderStyle">
        html, body, * {font-family: "微软雅黑"!important;}
        * {margin: 0; padding: 0;font-size:8pt;color:#000;line-height: 1.5;}
        /*.wrap {padding:5pt;}*/
        .line {clear: both;}

        .bootline {margin-bottom:10px;width: 100%;border-bottom: 1px dashed #000;}

        .desc {font-size: 10pt;font-weight: 400; text-align: center}
        .goods {margin: 10px 0;width: 100%;border-top:1px dashed #000;border-bottom: 1px dashed #000;}
        .goods th, .goods td {text-align: center;font-size:8pt;padding:2pt 0;}
        .goods th.left, .goods td.left {text-align: left;}
        .goods th {font-weight: 400;}
        .goods td {vertical-align: top;}
        .cart {margin: 10px 0;width: 100%;border-bottom: 1px dashed #000;}
        .cart th, .cart td {text-align: center;font-size:8pt;padding:2pt 0;}
        .cart th.left, .cart td.left {text-align: left;}
        .cart th {font-weight: 400;}
        .cart td {vertical-align: top;}
        .logo {padding-bottom: 10px; }
        .logoimg {display:inline-block;vertical-align:middle;height: 40px;margin-right:5px;}
        .logo span {display: inline-block;vertical-align: middle;font-size:10pt;font-weight: 400;}
        .qrcode {text-align: center;}
        .qrcode img {text-align: center; width: 90%}
    </style>
</head>
<body>
<div id="print" style="width:180px;">
    <div class="wrap" style="width:180px;">
        <h4 class="logo"><img class="logoimg" src="{{ asset('/static/images/logo.png') }}"><span>{{ $webName }}</span></h4>
        @foreach ($orderList as $orderInfo)
            <p class="line">订单号：{{ $orderInfo["order_no_format"] }}</p>
            <p class="line bootline">打印时间：<?php echo date('Y-m-d H:i:s',time());?></p>
            @if (true)
                <p class="line">取货方式:配送上门</p>
                <p class="line">下单时间:{{ $orderInfo["order_time_format"] }}</p>
                <p class="line">送货速度:{{ $orderInfo["second_num"] }}分钟</p>
                <p class="line bootline">预约时间:{{ $orderInfo["send_end_time_format"] }}</p>
                <p class="line">收货人:{{ $orderInfo["addr"]["real_name"] or '' }}</p>
                <p class="line">电话:{{ $orderInfo["addr"]["mobile"] or '' }} *** </p>
                <p class="line bootline">配送地址:{{ $orderInfo["addr"]["addr"] or '' }}***</p>
                @if ( false &&  isset($orderInfo["remarks"]) && (!empty($orderInfo->remarks) ))
                    <p class="line">【备注】{{ $orderInfo["remarks"] or '' }}</p>
                @endif
            @endif
            @foreach ($orderInfo['shopList'] as $shopInfo)
                <p class="line">&nbsp;</p>
                <p class="line">店铺：{{ $shopInfo["shop"]["shop_name"] or '' }}</p>
                <p class="line">地址：{{ $shopInfo["shop"]["addr"] or '' }}</p>
                <p class="line">联系电话：{{ $shopInfo["shop"]["tel"] or '' }}</p>
                <table class="goods">
                    <tr>
                        <th class="left">商品</th>
                        <th>数量</th>
                        <th style="width:40%">单价</th>
                        <th>金额</th>
                    </tr>
                    @foreach ($shopInfo['orders_goods'] as $goodInfo)
                    <tr>
                        <td class="left" colspan="4">
                                爆品直降－{{ $goodInfo["goods_name"] or '' }} (已退5)
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><span style="color: red;">×</span>{{ $goodInfo["amount"] or '' }}</td>
                        <td>¥{{ $goodInfo["price_format"] or '' }}</td>
                        <td>¥{{ $goodInfo["total_price_format"] or '' }}</td>
                    </tr>
                    @endforeach
                </table>

                <table class="cart">
                    <tr>
                        <td style="text-align: left;">商品总数:{{ $shopInfo["total_amount"] or '' }}</td>
                        <td style="text-align: right;">商品小计:¥{{ $shopInfo["total_price_format"] or '' }}元</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">配送费:</td>
                        <td style="text-align: right;">¥{{ $shopInfo["total_run_price_format"] or '' }}元</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">实惠现金<span style="color: red;">抵扣</span>: </td>
                        <td style="text-align: right;"><span style="color: red;">-</span>¥10.00元</td>
                    </tr>
                </table>
                <div class="row"><span style="float: left;color: red;">支付金额:</span><span style="float: right;color: red;">¥{{ $shopInfo["pay_run_amount_format"] or '' }}元(已退费¥{{ $shopInfo["refund_price_format"] or '' }}元()</span></div>
                <p class="line bootline">&nbsp;</p>
            @endforeach
        @endforeach
        <p class="line">免费福利活动&nbsp1小时送货上门</p>
        <p class="line">家政保洁服务&nbsp免费快递代收发</p>
        <p class="line">手机充值服务&nbsp社区趣味活动</p>
        <p class="line">实惠社区服务社您身边的全能管家</p>
        <p class="line">&nbsp;</p>
        <p class="line">实惠客服投诉电话:&nbsp;400-6611-388</p>
    </div>
</div>

</body>
</html>