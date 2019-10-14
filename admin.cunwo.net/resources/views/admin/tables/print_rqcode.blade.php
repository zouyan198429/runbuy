<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>打印桌位二维码</title>
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
        <p class="line">桌位二维码</p>
        <p class="line bootline">打印时间：<?php echo date('Y-m-d H:i:s',time());?></p>
        @foreach ($orderList as $orderInfo)
            <p class="line "></p>
            <p class="line">&nbsp;</p>
            <p class="qrcode">
                {{ $orderInfo["table_name"] }}，请微信扫码自助点餐<br/><br/>
                <img src="{{ $orderInfo["qrcode_url"] }}" style="width:160px;" />
                <br/><br/>
                {{ $orderInfo["table_name"] }}，请微信扫码自助点餐
            </p>
            <p class="line">&nbsp;</p>
            <p class="line bootline"></p>
        @endforeach
        {{--<p class="line">免费福利活动&nbsp1小时送货上门</p>--}}
        {{--<p class="line">家政保洁服务&nbsp免费快递代收发</p>--}}
        {{--<p class="line">手机充值服务&nbsp社区趣味活动</p>--}}
        {{--<p class="line">实惠社区服务社您身边的全能管家</p>--}}
        {{--<p class="line">&nbsp;</p>--}}
        {{--<p class="line">实惠客服投诉电话:&nbsp;400-6611-388</p>--}}
    </div>
</div>

</body>
</html>