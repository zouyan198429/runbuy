

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

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate or '' }}商品</div>--}}
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
                <th>价格<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="price" value="{{ $info['price'] or '' }}" placeholder="请输入价格"  onkeyup="numxs(this) " onafterpaste="numxs(this)"   />
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
</script>
<script src="{{ asset('/js/admin/lanmu/shopGoods_edit.js') }}"  type="text/javascript"></script>
</body>
</html>