

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>开启头部工具栏 - 数据表格</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  @include('shop.layout_public.pagehead')
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">

    <link rel="stylesheet" type="text/css" href="{{asset('css/basic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/myOrder.css')}}">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> 我的同事</div>--}}
<div class="mm">
  <div class="mmhead" id="mywork">

    @include('common.pageParams')
    <div class="tabbox" style="display:none;" >
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加用户</a>
    </div>

    <form onsubmit="return false;" class="form-horizontal" role="form" method="post" id="search_frm" action="#">

      <div class="msearch fr" style="width:100%;">
        <div class="layui-tab layui-tab-brief"  style="float:left;">
          <ul class="layui-tab-title">
            <li><a href="javascript:void(0);"  data-status="" class="status_click">全部</a></li>
            @foreach ($status as $k=>$txt)
              <li @if ($k == $defaultStatus) class="layui-this" @endif><a href="javascript:void(0)" data-status="{{ $k }}" class="status_click  ">
                {{ $txt }}
                @if(in_array($k,$countStatus))
                  <span class="layui-badge status_count_{{ $k }}" data-old_count="0">0</span>
                @endif
              </a></li>
            @endforeach
          </ul>
          <div class="layui-tab-content"></div>
        </div>
        <input type="hidden" name="city_site_id" value="{{ $city_site_id or 0 }}" />
        <input type="hidden" name="city_partner_id" value="{{ $city_partner_id or 0 }}" />
        <input type="hidden" name="seller_id" value="{{ $seller_id or 0 }}" />
        <input type="hidden" name="shop_id" value="{{ $shop_id or 0 }}" />
        <select style="width:60px; height:28px; display: none;" name="status" >
          <option value="">全部</option>
          @foreach ($status as $k=>$txt)
            <option value="{{ $k }}"   @if ($k == $defaultStatus) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wnormal" name="province_id" style="width: 80px;display: none;">
          <option value="">请选择省</option>
          @foreach ($province_kv as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultProvince) && $defaultProvince == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wnormal" name="city_id" style="width: 80px;display: none;">
          <option value="">请选择市</option>
        </select>
        <select class="wnormal" name="area_id" style="width: 80px;display: none;">
          <option value="">请选择区县</option>
        </select>
        {{--<select class="wmini" name="province_id" style="width: 55px;">--}}
          {{--<option value="">全部</option>--}}
          {{--@foreach ($adminType as $k=>$txt)--}}
            {{--<option value="{{ $k }}"  @if(isset($defaultAdminType) && $defaultAdminType == $k) selected @endif >{{ $txt }}</option>--}}
          {{--@endforeach--}}
        {{--</select>--}}
        <select style="width:80px; height:28px;" name="field">
          <option value="order_no">订单号</option>
          <option value="parent_order_no">父订单号</option>
          {{--<option value="admin_username">用户名</option>--}}
          {{--<option value="real_name">真实姓名</option>--}}
          {{--<option value="tel">电话</option>--}}
          {{--<option value="mobile">手机</option>--}}
          {{--<option value="qq_number">QQ\email\微信</option>--}}
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
  {{--
  <div class="table-header">
    { {--<button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>--} }
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入城市</button>
    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{ {--导入file对象--} }
  </div>
--}}

<!--5. 订单表格表头-order_caption-->
    <div  id="dynamic-table">
        <div class="order_caption clearfix"  style="margin-top: 50px;">
        <span class="wd1 order_time sub_list">
            {{--<i>近期三个月订单</i>--}}
            {{--<ul>--}}
            {{--<li class="selected">近三个月订单</li>--}}
            {{--<li>今年类订单</li>--}}
            {{--<li>2017年订单</li>--}}
            {{--<li>2016年订单</li>--}}
            {{--<li>2015年订单</li>--}}
            {{--<li>2015年订单以前</li>--}}
            {{--</ul>--}}
        </span>
            <span class="wd2">订单详情</span>
            <span class="wd3">单价</span>
            <span class="wd4">数量</span>
            <span class="wd5">总金额</span>
            <span class="wd6 all_status sub_list">
            <i>状态</i>
                {{--<i>全部状态</i>--}}
                {{--<ul>--}}
                {{--<li class="selected">全部状态</li>--}}
                {{--<li>等待付款</li>--}}
                {{--<li>已完成</li>--}}
                {{--<li>已取消</li>--}}
                {{--</ul>--}}
        </span>
            <span class="wd7">操作</span>
        </div>


        <div class="order_list baguetteBoxOne gallery" id="data_list" >

        </div>

    </div>

    {{--<table lay-even class="layui-table "  lay-size="lg"  id="dynamic-table" style="display: block;">--}}
    {{--<thead>--}}
    {{--<tr>--}}
      {{--<th>--}}

          {{--<label class="pos-rel">--}}
          {{--<input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>--}}
          {{--<span class="lbl">全选</span>--}}
        {{--</label>--}}
      {{--</th>--}}
      {{--<th>微信 unionid</th>--}}
      {{--<th>小程序 openid</th>--}}
      {{--<th>服务号 openid</th>--}}
      {{--<th>昵称</th>--}}
      {{--<th>性别</th>--}}
      {{--<th>国家</th>--}}
      {{--<th>省份</th>--}}
      {{--<th>城市</th>--}}
      {{--<th>头像</th>--}}
      {{--<th>是否超级帐户<hr/>状态</th>--}}
      {{--<th>最近登陆</th>--}}
      {{--<th style="width: 150px;display: none;">操作</th>--}}
    {{--</tr>--}}
    {{--</thead>--}}
    {{--<tbody id="data_list" class=" baguetteBoxOne gallery">--}}
      {{--<tr>--}}
        {{--<td>--}}

        {{--</td>--}}
        {{--<td>--}}
        {{--</td>--}}
      {{--</tr>--}}
    {{--</tbody>--}}
  {{--</table>--}}
  <!--6. 订单列表-order_list-->
  <div class="mmfoot">
    <div class="mmfleft"></div>
    <div class="pagination">
    </div>
  </div>

</div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')

<div style="display:none;">
  @include('public.scan_sound')
</div>
  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/shop/order/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('shop/order/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('shop/order/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "用户" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('shop/order/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('shop/order/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/shop/order/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/shop/order/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('shop/order/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('shop/order/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/shop/order/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

      var PROVINCE_CHILD_URL  = "{{url('api/shop/city/ajax_get_child')}}";// 获得地区子区域信息
      var CITY_CHILD_URL  = "{{url('api/shop/city/ajax_get_child')}}";// 获得地区子区域信息

      const PROVINCE_ID = "{{ $info['province_id'] or -1}}";// 省默认值
      const CITY_ID = "{{ $info['city_id'] or -1 }}";// 市默认值
      const AREA_ID = "{{ $info['area_id'] or -1 }}";// 区默认值

      var SATUS_COUNT_URL = "{{ url('api/shop/order/ajax_status_count') }}";// ajax工单状态统计 url
      var NEED_PLAY_STATUS = "{{ $countPlayStatus }}";// 需要发声的状态，多个逗号,分隔

      var CANCEL_ORDER_URL = "{{ url('api/shop/order/refundOrder') }}";// 取消订单

  </script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}

<script src="{{asset('js/common/list.js')}}"></script>
<script src="{{ asset('js/shop/lanmu/orders.js') }}"  type="text/javascript"></script>
</body>
</html>