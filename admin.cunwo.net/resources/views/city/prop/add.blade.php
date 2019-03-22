

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
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate or '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] or 0 }}"/>
        <table class="table1">
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
                <th>属性名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="main_name" value="{{ $info['main_name'] or '' }}" placeholder="请输入名称" />
                </td>
            </tr>
            <tr>
                <th>属性值<span class="must"></span></th>
                <td >
                    <table  class="layui-table table2 prop_table"  style="width: 565px;" >
                        <thead>
                        <tr>
                            <th style="width: 250px;">属性值</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="pvbody data_list_pv" >
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>
                                <input type="text" class="inp wnormal"  name="propname" value=""/>
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-mini btn-info  fa fa-plus-circle" onclick="otheraction.addPVName(this)">添加属性值</a>
                            </td>
                        </tr>
                        </tfoot>

                    </table>
                </td>
            </tr>
            <tr  style="display: none;">
                <th>属性值<span class="must">*</span></th>
                <td>
                    <textarea type="text" class="inptext wlong" name="prop_vals"  placeholder="请输入属性值"  ></textarea>
                    {{--{{ $info['prop_vals'] or '' }}--}}
                    <br/>多个属性值，则每行一个属性值。
                </td>
            </tr>
            <tr>
                <th>排序[降序]<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sort_num" value="{{ $info['sort_num'] or '' }}" placeholder="请输入排序"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
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
    var SAVE_URL = "{{ url('api/city/prop/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('city/prop')}}";//保存成功后跳转到的地址

    {{--var SELECT_SELLER_URL = "{{ url('city/seller/select') }}";// 选择商家地址--}}
    {{--var AJAX_SELLER_SELECTED_URL = "{{ url('api/city/seller/ajax_selected') }}";// ajax选中商家地址--}}

    var SELECT_SHOP_URL = "{{ url('city/shop/select') }}";// 选择店铺地址
    var AJAX_SHOP_SELECTED_URL = "{{ url('api/city/shop/ajax_selected') }}";// ajax选中店铺地址


    var FRM = "{{ $frm or 0 }}";

    // 属性模板
    var DYNAMIC_PROPVAL_BAIDU_TEMPLATE = "baidu_template_pv_data_list";//百度模板id
    var DYNAMIC_PROPVAL_TABLE_BODY = "data_list_pv";//数据列表class

    var PROPVAL_LIST = @json($info['propVals'] ?? []) ;
    var PV_LIST_JSON =  {'data_list': PROPVAL_LIST };// 属性值 数据列表json对象格式  {‘data_list’:[{'id':57,'main_name':'小量'},...]}

    var JUDGE_PV_USED_URL = "{{url('api/city/prop/ajax_pv_used')}}";// 查询属性值id是否有商品正在使用
</script>
<script src="{{ asset('/js/city/lanmu/prop_edit.js') }}"  type="text/javascript"></script>
</body>
</html>