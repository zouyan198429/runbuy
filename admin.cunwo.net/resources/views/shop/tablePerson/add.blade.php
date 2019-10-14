

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
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate or '' }}桌位/包间分类(按人数)</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] or 0 }}"/>
        <table class="table1">
            <tr>
                <th>所属店铺<span class="must">*</span></th>
                <td>
                    <span class="shop_name">{{ $info['shop_name'] or '' }}</span>
                    <input type="hidden" name="shop_id"  value="{{ $info['shop_id'] or '' }}" />
                    <input type="hidden" name="shop_id_history"  value="{{ $info['shop_id_history'] or '' }}" />
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectShop(this)">选择所属店铺</button>

                    <button type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-pencil bigger-60 update_shop" @if(isset($info['now_shop_state']) && in_array($info['now_shop_state'],[0,1])) style="display: none;"  @endif  onclick="otheraction.updateShop(this)">更新[当前所属店铺已更新]</button>

                </td>
            </tr>
            <tr>
                <th>分类名称(按人数)<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="person_name" value="{{ $info['person_name'] or '' }}" placeholder="请输入分类名称"/>
                </td>
            </tr>
            <tr>
                <th>是否开启<span class="must">*</span></th>
                <td>
                    @foreach ($isOpen as $k=>$txt)
                        <label><input type="radio"  name="is_open"  value="{{ $k }}"  @if(isset($defaultIsOpen) && $defaultIsOpen == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>排号前缀<span class="must">*</span></th>
                <td>
                    <label><input type="radio"  name="prefix_id"  value="0"  @if(isset($defaultNumPre) && $defaultNumPre == 0) checked="checked"  @endif  />无前缀 </label>
                    @foreach ($num_pre_kv as $k=>$txt)
                        <label><input type="radio"  name="prefix_id"  value="{{ $k }}"  @if(isset($defaultNumPre) && $defaultNumPre == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>排序[降序]<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sort_num" value="{{ $info['sort_num'] or '' }}" placeholder="请输入排序" onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
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
    var SAVE_URL = "{{ url('api/shop/tablePerson/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('shop/tablePerson')}}";//保存成功后跳转到的地址

    var SELECT_SHOP_URL = "{{ url('shop/shop/select') }}";// 选择店铺地址
    var AJAX_SHOP_SELECTED_URL = "{{ url('api/shop/shop/ajax_selected') }}";// ajax选中店铺地址
</script>
<script src="{{ asset('/js/shop/lanmu/tablePerson_edit.js') }}"  type="text/javascript"></script>
</body>
</html>