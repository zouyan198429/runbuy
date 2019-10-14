

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
    <form onsubmit="return false;" class="form-horizontal" role="form" method="post" id="search_frm" action="#">
      <div  style="width:100%;">
        <div class="layui-tab layui-tab-brief">
          <div class="layui-tab-content">
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
          </div>
        </div>
        <div class="tabbox" style="display:block;">
          <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加桌位/包间</a>
          <a href="javascript:void(0);" class="on" onclick="otheraction.batchPrint(this)">打印二维码</a>
          <a href="javascript:void(0);" class="on" onclick="otheraction.downDrive(this)">下载网页打印机驱动</a>
        </div>


        <div class="msearch fr"  style="width:720px;">
        <input type="hidden" name="city_site_id" value="{{ $city_site_id or 0 }}" />
        <input type="hidden" name="city_partner_id" value="{{ $city_partner_id or 0 }}" />
        <input type="hidden" name="seller_id" value="{{ $seller_id or 0 }}" />
        <input type="hidden" name="shop_id" value="{{ $shop_id or 0 }}" />
        <select class="wmini" name="is_open" style="width: 130px;">
          <option value="">请选择是否开启</option>
          @foreach ($isOpen as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultIsOpen) && $defaultIsOpen == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        <select class="wmini" name="status" style="width: 130px; display: none;">
          <option value="">请选择使用状态</option>
          @foreach ($status as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultStatus) && $defaultStatus == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
        {{--<select class="wmini" name="prefix_id" style="width: 130px;">--}}
          {{--<option value="">请选择桌位人数分类</option>--}}
{{--          <option value="0"  @if(isset($defaultTablePersonId) && $defaultTablePersonId == 0) selected @endif >无分类</option>--}}
          {{--@foreach ($table_person_kv as $k=>$txt)--}}
            {{--<option value="{{ $k }}"  @if(isset($defaultTablePersonId) && $defaultTablePersonId == $k) selected @endif >{{ $txt }}</option>--}}
          {{--@endforeach--}}
        {{--</select>--}}
        <select style="width:80px; height:28px;" name="field">
          <option value="table_name">桌位号/包间名称</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
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
        <col width="80">
        <col width="120">
        <col width="120">
        <col width="150">
        <col width="">
        <col width="">
        <col width="100">
        <col width="150">
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
      <th>ID</th>
      <th>城市分站</th>
      <th>城市合伙人</th>
      <th>商家</th>
      <th>店铺</th>
      <th>桌位号/包间名称</th>
      <th>桌位分类</th>
      <th>图片</th>
      <th>二维码</th>
      <th>开启状态</th>
      <th>使用状态</th>
      <th>排序[降序]</th>
      <th>操作</th>
    </tr>
    </thead>
    <tbody id="data_list" class=" baguetteBoxOne gallery">
    </tbody>
  </table>
  <div class="mmfoot">
    <div class="mmfleft"></div>
    <div class="pagination">
    </div>
  </div>

</div>
<a href="javascript:void(0);" class="on" id="testBTN">测试</a>
  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}

<script src="{{asset('/static/js/LodopFuncs.js')}}"></script>
  @include('public.dynamic_list_foot')
<div style="display:none;">
  @include('public.scan_sound')
</div>

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/admin/tables/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/tables/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/tables/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "桌位/包间" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/tables/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/tables/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/tables/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/tables/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/tables/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/tables/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/tables/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

      var SATUS_COUNT_URL = "{{ url('api/admin/tables/ajax_status_count') }}";// ajax工单状态统计 url
      var NEED_PLAY_STATUS = "{{ $countPlayStatus }}";// 需要发声的状态，多个逗号,分隔

      var CREATE_QRCODE_URL = "{{ url('api/admin/tables/ajax_create_qrcode') }}";// ajax生成二维码

      var PRINT_QRCODE_URL = "{{ url('admin/tables/print/') }}";// 打印二维码
      var DOWN_QRCODE_URL = "{{ url('admin/tables/down/') }}";// 下载二维码
      var DOWN_DRIVE_URL = "{{ url('admin/down_drive') }}";// 下载网页打印机驱动

      var TEST_URL = "{{ url('api/test') }}";// 测试地址
      $(function(){


          $(document).on("click","#testBTN",function(){
              var obj = $(this);
              // 测试
              let dataParams = {'name' : '邹燕', 'age' : 25, 'sex': 1};
              // $sign = getSignByObj(dataParams, '123456789', '111222333', 1);
              // console.log('~~~~~~~~$sign~~~~~~~~~~~~~~~~', $sign);
              console.log('~~~~~~~~dataParams~~~~~~~~~~~~~~~~', dataParams);
              signTest(dataParams);
              console.log('~~~~~~~~API后~~~~~~~~~~~~~~~~', dataParams);
              return false;
          });

      });


      // 签名请求测试
      function signTest(data){
          // var data = {'id':id};
          console.log(TEST_URL);
          console.log(data);
          data['sign'] = getSignByObj(data, '123456789', '111222333', 1);
          var layer_index = layer.load();
          $.ajax({
              'type' : 'POST',
              'url' : TEST_URL,
              'data' : data,
              'dataType' : 'json',
              'success' : function(ret){
                  console.log(ret);
                  if(!ret.apistatus){//失败
                      //alert('失败');
                      err_alert(ret.errorMsg);
                  }else{//成功
                      // reset_list_self(true,false,true, 2);
                      layer_alert('操作成功',1,0)
                  }
                  layer.close(layer_index)//手动关闭
              }
          })
      }
  </script>
<script type="text/ecmascript" src="{{asset('static/js/sha1.js')}}"></script>
<script type="text/ecmascript" src="{{asset('static/js/md5/md5.js')}}"></script>
<script type="text/ecmascript" src="{{asset('static/js/sign.js')}}"></script>

<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}

  <script src="{{asset('js/common/list.js')}}"></script>
  <script src="{{ asset('js/admin/lanmu/tables.js') }}"  type="text/javascript"></script>
</body>
</html>