

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
        <table class="table1">
            <tr>
                <th>所属商家<span class="must">*</span></th>
                <td>
                    <span class="seller_name">{{ $info['seller_name'] or '' }}</span>
                    <input type="hidden" name="seller_id"  value="{{ $info['seller_id'] or '' }}" />
                    <input type="hidden" name="seller_id_history"  value="{{ $info['seller_id_history'] or '' }}" />
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectSeller(this)">选择所属商家</button>

                    <button type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-pencil bigger-60 update_seller" @if(isset($info['now_seller_state']) && in_array($info['now_seller_state'],[0,1])) style="display: none;"  @endif  onclick="otheraction.updateSeller(this)">更新[当前所属商家已更新]</button>

                </td>
            </tr>

            <tr>
                <th>店铺名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="shop_name" value="{{ $info['shop_name'] or '' }}" placeholder="请输入商户名称"/>
                </td>
            </tr>
            <tr>
                <th>分类<span class="must">*</span></th>
                <td>
                    @foreach ($type_kv as $k=>$txt)
                        <label><input type="radio"  name="shop_type_id"  value="{{ $k }}"  @if(isset($defaultType) && $defaultType == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>标签<span class="must">*</span></th>
                <td  class="selLabelIds">
                    @foreach ($labels_kv as $k=>$txt)
                        <label><input type="checkbox"  name="label_ids[]"  value="{{ $k }}"  @if(isset($defaultLabel) && in_array($k, $defaultLabel)) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>营业时间<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal range_time"  readonly name="range_time" value="" placeholder="请选择营业时间范围"  />
                </td>
            </tr>
            <tr>
                <th>人均<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="per_price" value="{{ $info['per_price'] or '' }}" placeholder="请输入人均"  onkeyup="numxs(this) " onafterpaste="numxs(this)"   />
                </td>
            </tr>
            <tr>
                <th>状态<span class="must">*</span></th>
                <td>
                    @foreach ($status as $k=>$txt)
                        <label><input type="radio"  name="status"  value="{{ $k }}"  @if(isset($defaultStatus) && $defaultStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>联系人<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="linkman" value="{{ $info['linkman'] or '' }}" placeholder="请输入联系人"/>
                </td>
            </tr>
            <tr>
                <th>手机<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="mobile" value="{{ $info['mobile'] or '' }}" placeholder="请输入手机"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>电话</th>
                <td>
                    <input type="text" class="inp wnormal"  name="tel" value="{{ $info['tel'] or '' }}" placeholder="请输入电话"/>
                </td>
            </tr>
            <tr>
            <th>地址</th>
            <td>
                <select class="wnormal" name="province_id" style="width: 100px;">
                    <option value="">请选择省</option>
                    @foreach ($province_kv as $k=>$txt)
                        <option value="{{ $k }}"  @if(isset($defaultProvince) && $defaultProvince == $k) selected @endif >{{ $txt }}</option>
                    @endforeach
                </select>
                <select class="wnormal" name="city_id" style="width: 100px;">
                    <option value="">请选择市</option>
                </select>
                <select class="wnormal" name="area_id" style="width: 100px;">
                    <option value="">请选择区县</option>
                </select>
                <br/><br/>
                <input type="text" class="inp wnormal"  style="width:600px;"  name="addr" value="{{ $info['addr'] or '' }}" placeholder="请输入地址"/>
            </td>
            </tr>
            @if( isset($info['id']) && $info['id'] <= 0 )
                <tr>
                    <th>用户名<span class="must">*</span></th>
                    <td>
                        <input type="text" class="inp wnormal"  name="admin_username" value="{{ $info['admin_username'] or '' }}" placeholder="请输入用户名"/>
                    </td>
                </tr>
                <tr>
                    <th>登录密码<span class="must">*</span></th>
                    <td>
                        <input type="password"  class="inp wnormal"   name="admin_password" placeholder="登录密码" />修改时，可为空，不修改密码。
                    </td>
                </tr>
                <tr>
                    <th>确认密码<span class="must">*</span></th>
                    <td>
                        <input type="password" class="inp wnormal"     name="sure_password"  placeholder="确认密码"/>修改时，可为空，不修改密码。
                    </td>
                </tr>
            @endif
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
    var SAVE_URL = "{{ url('api/admin/shop/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/shop')}}";//保存成功后跳转到的地址

    var PROVINCE_CHILD_URL  = "{{url('api/admin/city/ajax_get_child')}}";// 获得地区子区域信息
    var CITY_CHILD_URL  = "{{url('api/admin/city/ajax_get_child')}}";// 获得地区子区域信息

    const PROVINCE_ID = "{{ $info['province_id'] or -1}}";// 省默认值
    const CITY_ID = "{{ $info['city_id'] or -1 }}";// 市默认值
    const AREA_ID = "{{ $info['area_id'] or -1 }}";// 区默认值

    var SELECT_SELLER_URL = "{{ url('admin/seller/select') }}";// 选择商家地址
    var AJAX_SELLER_SELECTED_URL = "{{ url('api/admin/seller/ajax_selected') }}";// ajax选中商家地址


    var RANGE_TIME = "{{ $info['range_time'] or '' }}" ;//开考时间
</script>
<script src="{{ asset('/js/admin/lanmu/shop_edit.js') }}"  type="text/javascript"></script>
</body>
</html>