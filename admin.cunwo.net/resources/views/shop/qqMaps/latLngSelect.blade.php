<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>开启头部工具栏 - 数据表格</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <style type="text/css">
    html,body,#container{
      width:100%;
      height:100%;
    }
    *{
      margin:0px;
      padding:0px;
    }
    body, button, input, select, textarea {
      font: 12px/16px Verdana, Helvetica, Arial, sans-serif;
    }
    p{
      width:603px;
      padding-top:3px;
      overflow:hidden;
    }
    /*#container{*/
      /*min-width:600px;*/
      /*min-height:767px;*/
    /*}*/
    .btn{
      width:142px;
    }
  </style>
  @include('shop.layout_public.pagehead')
</head>
<body>
<div style="position:absolute;top: 40px;left: 60px;z-index: 1000;background-color: #FFFFFF;padding: 5px 5px;border:2px solid #86ACF2;">
  纬度：<input id="Lat" name="Lat" readonly type="text" value="" style="width: 170px;">
  经度：<input id="Lng" name="Lng" readonly type="text" value="" style="width: 170px;">
  <input type="button" value=" 确 定 " onclick="otheraction.selected(this)" style="cursor:pointer;">
  <div style="height:30px;color: red; ">新标记：地图中［双击］；移动标记：点击标记并移动；</div>
</div>
  <div id="container"></div>
</body>
</html>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
@include('public.dynamic_list_foot')
<script type="text/javascript">
    var QQ_MAPS_KEY = "{{ $qqMapsKey or '' }}"; // 腾讯地图Key鉴权
    var LAT_VAL = "{{ $lat or '' }}";// 纬度
    var LNG_VAL = "{{ $lng or '' }}";// 经度
    var FRM = "{{ $frm or '0' }}";// 来源0非弹窗 1弹窗
</script>
<script src="{{asset('js/common/mapsQQSelect.js')}}"></script>