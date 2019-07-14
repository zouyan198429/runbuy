

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
    {{--  本页单独使用 --}}
    <script src="{{asset('dist/lib/kindeditor/kindeditor.min.js')}}"></script>
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate or '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        {{--<input type="hidden" name="id" value="{{ $info['id'] or 0 }}"/>--}}
        <table class="table1">
            <tr>
                <th>所属城市分站<span class="must">*</span></th>
                <td>
                    <span class="city_name">{{ $info['city_name'] or '' }}</span>
                    <input type="hidden" name="city_site_id"  value="{{ $info['city_site_id'] or '' }}" />
                    <input type="hidden" name="city_site_id_history"  value="{{ $info['city_site_id_history'] or '' }}" />
                    {{--<button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCity(this)">选择所属分站</button>--}}

                    {{--<button type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-pencil bigger-60 update_city" @if(isset($info['now_city_state']) && in_array($info['now_city_state'],[0,1])) style="display: none;"  @endif  onclick="otheraction.updateCity(this)">更新[当前所属城市已更新]</button>--}}

                </td>
            </tr>
            {{--<tr style="display: none;">--}}
                {{--<th>标题<span class="must">*</span></th>--}}
                {{--<td>--}}
                    {{--<input type="text" class="inp wnormal"  name="title" value="{{ $info['title'] or '' }}" placeholder="请输入标题" />--}}
                {{--</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th>内容<span class="must">*</span></th>--}}
                {{--<td>--}}
                    {{--<textarea class="kindeditor" name="content" rows="15" id="doc-ta-1" style=" width:770px;height:400px;">{!!  htmlspecialchars($info['content'] ?? '' )   !!}</textarea>--}}
                    {{--<textarea type="text" class="inptext wlong"  style=" height:500px" /></textarea>--}}
                    {{--<p class="tip">根据客户描述，进行记录或备注。</p>--}}
                {{--</td>--}}
            {{--</tr>--}}
            <tr>
                <th>超出2公里，每公里费用<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="price_distance_every" value="{{ $info['price_distance_every_format'] or '' }}" placeholder="请输入费用"  onkeyup="numxs(this) " onafterpaste="numxs(this)"   />
                </td>
            </tr>
            <tr>
                <th>每多跑一商家费用<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="price_shop_every" value="{{ $info['price_shop_every_format'] or '' }}" placeholder="请输入费用"  onkeyup="numxs(this) " onafterpaste="numxs(this)"   />
                </td>
            </tr>

            <tr>
                <td colspan="2"  class="prop_td">
                    <div class="table-header">
                        <button  type="button" class="btn btn-danger  btn-xs ace-icon  bigger-60"  onclick="javascript:void(0);">时间段费用</button>
                    </div>
                    <table class=" table2"  >
                        <thead>
                        <tr>
                            <th>时间段</th>
                            <th>费用</th>
                        </tr>
                        </thead>
                        <tbody class="data_list time_list">
                            @foreach ($timeList as $k => $v)
                                <tr data-time_name="{{ $v['begin_time'] }}--{{ $v['end_time'] }}">
                                    <td>
                                        <input type="hidden" name="time_ids[]" value="{{ $v['id'] }}">
                                        <input type="hidden" name="time_nums[]" value="{{ $v['time_num'] }}">
                                        {{ $v['begin_time'] }}--{{ $v['end_time'] }}
                                    </td>
                                    <td>
                                        <label>
                                            <input type="text" class="inp wnormal"  name="init_prices[]" value="{{ $v['init_price_format'] or '' }}" placeholder="请输入费用"  onkeyup="numxs(this) " onafterpaste="numxs(this)"   />
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </td>
            </tr>
            {{--<tr style="display: none;">--}}
                {{--<th>排序[降序]<span class="must"></span></th>--}}
                {{--<td>--}}
                    {{--<input type="text" class="inp wnormal"  name="sort_num" value="{{ $info['sort_num'] or '' }}" placeholder="请输入排序"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />--}}
                {{--</td>--}}
            {{--</tr>--}}
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
    var SAVE_URL = "{{ url('api/city/feeScaleTime/ajax_save_bath') }}";// ajax保存记录地址
    var LIST_URL = "{{url('city/feeScaleTime')}}";//保存成功后跳转到的地址

    var SELECT_CITY_URL = "{{ url('city/city/select') }}";// 选择城市分站地址
    var AJAX_CITY_SELECTED_URL = "{{ url('api/city/city/ajax_selected') }}";// ajax选中城市分站地址
</script>
<script src="{{ asset('/js/city/lanmu/feeScaleTime_editBath.js') }}"  type="text/javascript"></script>
</body>
</html>