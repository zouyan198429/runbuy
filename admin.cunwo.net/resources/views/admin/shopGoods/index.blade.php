

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
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加商品</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr" style="width:700px;">
        <input type="hidden" name="city_site_id" value="{{ $city_site_id or 0 }}" />
        <input type="hidden" name="city_partner_id" value="{{ $city_partner_id or 0 }}" />
        <input type="hidden" name="seller_id" value="{{ $seller_id or 0 }}" />
        <input type="hidden" name="shop_id" value="{{ $shop_id or 0 }}" />
        <select class="wmini" name="is_hot" style="width: 55px;">
          <option value="">请选择是否热销</option>
          @foreach ($isHot as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultIsHot) && $defaultIsHot == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wmini" name="is_sale" style="width: 55px;">
          <option value="">请选择是否上架</option>
          @foreach ($isSale as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultIsSale) && $defaultIsSale == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select style="width:80px; height:28px;" name="field">
          <option value="shop_name">店铺名称</option>
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
    <thead>
    <tr>
      <th>
        <label class="pos-rel">
          <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
          <span class="lbl">全选</span>
        </label>
      </th>
      <th>ID</th>
      <th>城市分站<hr/>城市合伙人</th>
      <th>商家<hr/>店铺</th>
      <th>商品名称</th>
      <th>分类<hr/>价格</th>
      <th>排序[降序]</th>
      <th>总销量<hr/>月销量</th>
      <th>是否热销<hr/>是否上架</th>
      <th>介绍</th>
      <th>操作</th>
    </tr>
    </thead>
    <tbody id="data_list">
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
      var AJAX_URL = "{{ url('api/admin/shopGoods/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/shopGoods/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/shopGoods/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "商品" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/shopGoods/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/shopGoods/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/shopGoods/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/shopGoods/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/shopGoods/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/shopGoods/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/shopGoods/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
  </script>
  <script src="{{asset('js/common/list.js')}}"></script>
  <script src="{{ asset('js/admin/lanmu/shopGoods.js') }}"  type="text/javascript"></script>
</body>
</html>