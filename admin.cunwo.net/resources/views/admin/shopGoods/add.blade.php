

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate or '' }}商品</div>--}}
<div class="mm">
    <div class="alert alert-warning alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <p>一次最多上传5张图片。</p>
    </div>
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] or 0 }}"/>
        <table class="table1">
            <tr>
                <th>商品图片</th>
                <td>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    grid
                                @endslot
                                @slot('upload_url')
                                    {{ url('api/admin/upload') }}
                                @endslot
                            @endcomponent
                            {{--
                            <input type="file" class="form-control" value="">
                            --}}
                        </div>
                    </div>

                </td>
            </tr>
            <tr>
                <th>所属店铺<span class="must">*</span></th>
                <td>
                    <input type="hidden" name="seller_id"  value="{{ $info['seller_id'] or '' }}" />
                    <span class="shop_name">{{ $info['shop_name'] or '' }}</span>
                    <input type="hidden" name="shop_id"  value="{{ $info['shop_id'] or '' }}" />
                    <input type="hidden" name="shop_id_history"  value="{{ $info['shop_id_history'] or '' }}" />
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectShop(this)">选择所属店铺</button>

                    <button type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-pencil bigger-60 update_shop" @if(isset($info['now_shop_state']) && in_array($info['now_shop_state'],[0,1])) style="display: none;"  @endif  onclick="otheraction.updateShop(this)">更新[当前所属店铺已更新]</button>

                </td>
            </tr>
            <tr>
                <th>分类</th>
                <td>
                    <select class="wnormal" name="type_id" style="width: 100px;">
                        <option value="">请选择分类</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>商品名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="goods_name" value="{{ $info['goods_name'] or '' }}" placeholder="请输入商品名称"/>
                </td>
            </tr>
            <tr>
                <th>是否热销<span class="must">*</span></th>
                <td>
                    @foreach ($isHot as $k=>$txt)
                        <label><input type="radio"  name="is_hot"  value="{{ $k }}"  @if(isset($defaultIsHot) && $defaultIsHot == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>是否上架<span class="must">*</span></th>
                <td>
                    @foreach ($isSale as $k=>$txt)
                        <label><input type="radio"  name="is_sale"  value="{{ $k }}"  @if(isset($defaultIsSale) && $defaultIsSale == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>属性<span class="must">*</span></th>
                <td>
                    共 <span class="prop_num">{{ $subject_num or '0' }}</span> 个属性
                    <button  type="button" class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectProp(this)">选择属性</button>
                    <button  type="button" class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.addSelectProp(this)">新加属性</button>
                </td>
            </tr>
            <tr>
                <td colspan="2"  class="prop_td">
                    <div class="table-header">
                        <button  type="button" class="btn btn-danger  btn-xs ace-icon fa fa-trash-o bigger-60"  onclick="otheraction.batchDel(this, '.prop_td', 'tr')">批量删除</button>
                    </div>
                    <table class=" table2"  >
                        <thead>
                        <tr>
                            <th style="width: 90px;">
                                <label class="pos-rel">
                                    <input type="checkbox" class="ace check_all" value="" onclick="otheraction.seledAll(this,'.table2')">
                                    <span class="lbl">全选</span>
                                </label>
                            </th>
                            <th>属性</th>
                            <th>属性值</th>
                            <th>价格属性</th>
                            <th>是否必填</th>
                            <th>是否多选</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="data_list prop_list">
                        </tbody>

                    </table>

                </td>
            </tr>
            <tr>
                <th>价格<span class="must">*</span></th>
                <td>
                    <input type="hidden" name="price_type"  value="{{ $info['price_type'] or '' }}" />
                    <table class="price_prop_table table2" style="width:350px;display:none;">
                        <thead>
                        <tr>
                            <th>属性值</th>
                            <th>价格</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="data_list_price">
                        </tbody>
                    </table>

                    <input type="text" class="inp wnormal" style="display: none;"  name="price" value="{{ $info['price'] or '' }}" placeholder="请输入价格"  onkeyup="numxs(this) " onafterpaste="numxs(this)" />
                </td>
            </tr>
            <tr>
                <th>排序[降序]<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sort_num" value="{{ $info['sort_num'] or '' }}" placeholder="请输入排序" onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>介绍</th>
                <td>
                    <textarea type="text" class="inptext wlong" name="intro"  placeholder="请输入介绍"  >{{ $info['intro'] or '' }}</textarea>
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/admin/shopGoods/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/shopGoods')}}";//保存成功后跳转到的地址

    var SELECT_SHOP_URL = "{{ url('admin/shop/select') }}";// 选择店铺地址
    var AJAX_SHOP_SELECTED_URL = "{{ url('api/admin/shop/ajax_selected') }}";// ajax选中店铺地址




    var GOODS_TYPE_KV_URL  = "{{url('api/admin/shopGoodsType/ajax_get_kv')}}";// 获得商品分类信息;根据店铺id，获得店铺分类信息

    const SELLER_ID = "{{ $info['seller_id'] or -1}}";// 商家默认值

    const SHOP_ID = "{{ $info['shop_id'] or -1}}";// 店铺默认值
    const TYPE_ID = "{{ $info['type_id'] or -1}}";// 分类默认值

    var ID_VAL = "{{ $info['id'] or 0 }}";// 当前id值
    var AJAX_PROP_URL = "{{ url('api/admin/shopGoods/ajax_get_prop') }}";// ajax初始化属性地址
    var AJAX_UPDATE_PROP_URL = "{{ url('api/admin/shopGoods/ajax_add_prop') }}";// ajax更新属性地址
    var AJAX_PROP_ADD_URL = "{{ url('api/admin/shopGoods/ajax_add_prop') }}";// ajax添加属性地址

    var ADD_PROP_URL = "{{ url('admin/prop/add/0') }}";// 新加属性地址
    var SELECT_PROP_URL = "{{ url('admin/prop/select') }}";// 选择属性地址
    var AJAX_UPDATE_PROP_URL = "{{ url('api/admin/prop/ajax_selected_multi') }}";// ajax更新参考人员地址
    var AJAX_PROP_SELECTED_URL = "{{ url('api/admin/prop/ajax_selected') }}";// ajax选中属性地址-- 单选
    var AJAX_PROP_SELECTED_MULTI_URL = "{{ url('api/admin/prop/ajax_selected_multi') }}";// ajax选中属性地址-- 复选

    var DYNAMIC_BAIDU_TEMPLATE = "baidu_template_data_list";//百度模板id
    var DYNAMIC_TABLE_BODY = "data_list";//数据列表class

    // 价格模板
    var DYNAMIC_PRICE_BAIDU_TEMPLATE = "baidu_template_price_data_list";//百度模板id
    var DYNAMIC_PRICE_TABLE_BODY = "data_list_price";//数据列表class


    // 上传图片变量
    var FILE_UPLOAD_URL = "{{ url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    var PIC_DEL_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除图片url
    var MULTIPART_PARAMS = {pro_unit_id:'0'};// 附加参数	函数或对象，默认 {}
    var LIMIT_FILES_COUNT = 5;//   限制文件上传数目	false（默认）或数字
    var MULTI_SELECTION = true//  是否可用一次选取多个文件	默认 true false
    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。
    var SELF_UPLOAD = true;//  是否自己触发上传 TRUE/1自己触发上传方法 FALSE/0控制上传按钮
    var FILE_UPLOAD_METHOD = 'initPic()';// 单个上传成功后执行方法 格式 aaa();  或  空白-没有
    var FILE_UPLOAD_COMPLETE = '';  // 所有上传成功后执行方法 格式 aaa();  或  空白-没有
    var FILE_RESIZE = {quuality: 40};
    // resize:{// 图片修改设置 使用一个对象来设置如果在上传图片之前对图片进行修改。该对象可以包含如下属性的一项或全部：
    //     // width: 128,// 图片压缩后的宽度，如果不指定此属性则保持图片的原始宽度；
    //     // height: 128,// 图片压缩后的高度，如果不指定此属性则保持图片的原始高度；
    //     // crop: true,// 是否对图片进行裁剪；
    //     quuality: 50,// 图片压缩质量，可取值为 0~100，数值越大，图片质量越高，压缩比例越小，文件体积也越大，默认为 90，只对 .jpg 图片有效；
    //     // preserve_headers: false // 是否保留图片的元数据，默认为 true 保留，如果为 false 不保留。
    // },
    var RESOURCE_LIST = @json($info['resource_list'] ?? []) ;
    var PIC_LIST_JSON =  {'data_list': RESOURCE_LIST };// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/lanmu/shopGoods_edit.js') }}"  type="text/javascript"></script>
@component('component.upfileincludejs')
@endcomponent
</body>
</html>