

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>开启头部工具栏 - 数据表格</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  @include('city.layout_public.pagehead')
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">

    <link rel="stylesheet" type="text/css" href="{{asset('css/basic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/myOrder.css')}}">
</head>
<body>

<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> 订单统计</div>
<div class="mm">
    <div class="mmhead" id="mywork">
        <div class="tabbox" >
            @foreach ($count_types as $k=>$txt)
                <a href="javascript:void(0)"   data-count_type="{{ $k }}" class="count_types_click @if ($k == $defaultCountType) on @endif ">{{ $txt }}</a>
            @endforeach
        </div>

        <form onsubmit="return false;" class="form-horizontal" role="form" method="post" id="search_frm" action="#">
            <div class="msearch fr">
                <input type="hidden" name="city_site_id" value="{{ $city_site_id or 0 }}" />
                <input type="hidden" name="city_partner_id" value="{{ $city_partner_id or 0 }}" />
                <input type="hidden" name="send_staff_id" value="{{ $send_staff_id or 0 }}" />
                <input type="hidden" name="staff_id" value="{{ $staff_id or 0 }}" />
                <select style="width:80px; height:28px; display: none;" name="count_type" >
                    <option value="">全部</option>
                    @foreach ($count_types as $k=>$txt)
                        <option value="{{ $k }}"   @if ($k == $defaultCountType) selected @endif >{{ $txt }}</option>
                    @endforeach
                </select>
                <input type="text" id="yuyuetime" name="begin_date" class="begin_date" value="{{ $begin_date or '' }}"  placeholder="开始日期" style="width:100px;" />
                --
                <input type="text" id="yuyuetime" name="end_date" class="end_date" value="{{ $end_date or '' }}"  placeholder="结束日期" style="width:100px;" />
                <button class="btn btn-normal  search_frm ">搜索</button>
            </div>
        </form>
    </div>
    <div id="containerParent"></div>
    <p class="chart_title">{{--2018-05-01--- 2018-05-28(今天)--}}</p>
    <table class="table2">
        <thead>
        <tr>
            <th>日期</th>
            <th>订单数量</th>
            <th>跑腿费</th>
            <th>商品数量</th>
            <th>商品总价</th>
        </tr>
        </thead>
        <tbody id="dataList">
        {{--
        <tr>
            <td>2015-04-22</td>
            <td>233</td>
        </tr>
        --}}
        </tbody>
    </table>

</div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}

{{--这个js是引用echarts的插件的js--}}
{{--<script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/echarts.min.js"></script>--}}
<script type="text/javascript" src="{{asset('js/common/echarts.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/common/graph.js')}}"></script>

  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var COUNT_URL = "{{ url('api/city/order/ajax_count_orders') }}";// ajax订单统计 url
      var BAR_GRAPH_ID = "container";// 柱状图id
      var BEGIN_DATE = "{{ $begin_date or '' }}" ;//开始日期
      var END_DATE = "{{ $end_date or '' }}" ;//结束日期
  </script>
    <script src="{{ asset('js/city/lanmu/count_orders.js') }}"  type="text/javascript"></script>
</body>
</html>