

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

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate or '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] or 0 }}"/>
        <input type="hidden" name="ower_id" value="{{ $info['ower_id'] or 0 }}"/>

        <table class="table1">
            <tr>
                <th>联系人<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="real_name" value="{{ $info['real_name'] or '' }}" placeholder="请输入联系人"/>
                </td>
            </tr>
            <tr>
                <th>性别<span class="must">*</span></th>
                <td  class="layui-input-block">
                    <label><input type="radio" name="sex" value="1" @if (isset($info['sex']) && $info['sex'] == 1 ) checked @endif>男</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="sex" value="2" @if (isset($info['sex']) && $info['sex'] == 2 ) checked @endif>女</label>
                </td>
            </tr>
            <tr>
                <th>联系手机<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="mobile" value="{{ $info['mobile'] or '' }}" placeholder="请输入联系手机"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>地址名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr_name" value="{{ $info['addr_name'] or '' }}" placeholder="请输入地址名称"/>
                </td>
            </tr>
            <tr>
                <th>详细地址<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] or '' }}" placeholder="请输入详细地址"/>
                </td>
            </tr>
            <tr>
                <th>是否默认地址<span class="must">*</span></th>
                <td  class="layui-input-block">
                    <label><input type="radio" name="is_default" value="1" @if (isset($info['is_default']) && $info['is_default'] == 1 ) checked @endif>非默认</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="is_default" value="2" @if (isset($info['is_default']) && $info['is_default'] == 2 ) checked @endif>默认</label>
                </td>
            </tr>
            <tr>
                <th>经纬度<span class="must"></span></th>
                <td>
                    <span class="latlngtxt">{{ $info['latitude'] or '纬度' }}，{{ $info['longitude'] or '经度' }}</span>
                    <input type="hidden" name="latitude"  value="{{ $info['latitude'] or '' }}" />
                    <input type="hidden" name="longitude"  value="{{ $info['longitude'] or '' }}" />
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectLatLng(this)">选择经纬度</button>
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
    var SAVE_URL = "{{ url('api/admin/commonAddr/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/commonAddr')}}";//保存成功后跳转到的地址
    var SELECT_LATLNG_URL = "{{url('admin/qqMaps/latLngSelect')}}";//选择经纬度的地址

</script>
<script src="{{ asset('/js/admin/lanmu/commonAddr_edit.js') }}"  type="text/javascript"></script>
</body>
</html>