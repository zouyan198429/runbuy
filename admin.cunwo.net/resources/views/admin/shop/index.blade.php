

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>开启头部工具栏 - 数据表格</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  @include('admin.layout_public.pagehead')
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> 我的同事</div>--}}
<div class="mm">
  <div class="mmhead" id="mywork">

    @include('common.pageParams')
    <div class="tabbox" >
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加店铺</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr" style="width:700px;">
        <input type="hidden" name="city_site_id" value="{{ $city_site_id or 0 }}" />
        <input type="hidden" name="city_partner_id" value="{{ $city_partner_id or 0 }}" />
        <input type="hidden" name="seller_id" value="{{ $seller_id or 0 }}" />
        <select class="wmini" name="status" style="width: 55px;">
          <option value="">请选择状态</option>
          @foreach ($status as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultStatus) && $defaultStatus == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wmini" name="status_business" style="width: 55px;">
          <option value="">请选择营业状态</option>
          @foreach ($statusBusiness as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultStatusBusiness) && $defaultStatusBusiness == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wmini" name="shop_type_id" style="width: 55px;">
          <option value="">请选择分类</option>
          @foreach ($type_kv as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultType) && $defaultType == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wmini" name="label_id" style="width: 55px;">
          <option value="">请选择标签</option>
          @foreach ($labels_kv as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultLabel) && $defaultLabel == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wnormal" name="province_id" style="width: 80px;">
          <option value="">请选择省</option>
          @foreach ($province_kv as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultProvince) && $defaultProvince == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wnormal" name="city_id" style="width: 80px;">
          <option value="">请选择市</option>
        </select>
        <select class="wnormal" name="area_id" style="width: 80px;">
          <option value="">请选择区县</option>
        </select>

        <select style="width:80px; height:28px;" name="field">
          <option value="shop_name">店铺名称</option>
          <option value="linkman">联系人</option>
          <option value="mobile">手机</option>
          <option value="tel">电话</option>
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
  <table lay-even class="layui-table"  lay-size="lg"  id="dynamic-table"  class="table2">
    <colgroup>
        <col width="30">
        <col width="">
        <col width="">  
        <col width="">  
        <col width="">  
        <col width="">  
        <col width="">  
        <col width="">  
        <col width="200">
        <col>
    </colgroup> 
    <thead>
    <tr>
      <th>
        <label class="pos-rel">
          <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
          <!-- <span class="lbl">全选</span> -->
        </label>
      </th>
      <th>城市分站|城市合伙人|商家</th>
      <th>店铺名称</th>
      <th>图片</th>
      <th>联系人</th>
      <th>手机|电话</th>
      <!-- <th>所在地址</th> -->
      <th>分类|标签</th>
      <th>总销量|月销量|更新时间</th>
      <!-- <th>营业时间</th> -->
      <th>综合排序|人均|审核状态|营业状态</th>
      {{--<th>介绍</th>--}}
      <th>操作</th>
    </tr>
    </thead>
    <tbody id="data_list" class=" baguetteBoxOne gallery" >
    </tbody>
  </table>
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

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/admin/shop/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/shop/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/shop/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "店铺" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/shop/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/shop/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/shop/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/shop/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/shop/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/shop/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/shop/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

      var PROVINCE_CHILD_URL  = "{{url('api/admin/city/ajax_get_child')}}";// 获得地区子区域信息
      var CITY_CHILD_URL  = "{{url('api/admin/city/ajax_get_child')}}";// 获得地区子区域信息

      const PROVINCE_ID = "{{ $info['province_id'] or -1}}";// 省默认值
      const CITY_ID = "{{ $info['city_id'] or -1 }}";// 市默认值
      const AREA_ID = "{{ $info['area_id'] or -1 }}";// 区默认值

      var STAFF_SHOP_LIST_URL = "{{ url('admin/staffShop') }}"; //帐号管理
      var GOODS_TYPE_LIST_URL = "{{ url('admin/shopGoodsType') }}"; // 商品分类管理
      var PROP_LIST_URL = "{{ url('admin/prop') }}"; // 商品属性管理
      var GOODS_LIST_URL = "{{ url('admin/shopGoods') }}"; // 商品管理
      var ORDERS_LIST_URL = "{{ url('admin/order') }}"; // 订单管理
      var OPEN_TIME_LIST_URL = "{{ url('admin/shopOpenTime') }}"; // 营业时间管理

      var SAVE_URL_CLOSE = "{{ url('api/admin/shop/ajax_save_close') }}";// ajax保存记录-息业
      var SAVE_URL_OPEN = "{{ url('api/admin/shop/ajax_save_open') }}";// ajax保存记录-开业
  </script>

<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}

<script src="{{asset('js/common/list.js')}}"></script>
  <script src="{{ asset('js/admin/lanmu/shop.js') }}"  type="text/javascript"></script>
</body>
</html>