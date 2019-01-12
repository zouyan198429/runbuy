const REL_CHANGE = {
    'city':{// 市-二级分类
        'child_sel_name': 'city_id',// 第二级下拉框的name
        'child_sel_txt': {'': "请选择市" },// 第二级下拉框的{值:请选择文字名称}
        'change_ajax_url': PROVINCE_CHILD_URL,// 获取下级的ajax地址
        'parent_param_name': 'parent_id',// ajax调用时传递的参数名
        'other_params':{},//其它参数 {'aaa':123,'ccd':'dfasfs'}
    },
    'area':{// 区县---二级分类
        'child_sel_name': 'area_id',// 第二级下拉框的name
        'child_sel_txt': {'': "请选择区县" },// 第二级下拉框的{值:请选择文字名称}
        'change_ajax_url': CITY_CHILD_URL,// 获取下级的ajax地址
        'parent_param_name': 'parent_id',// ajax调用时传递的参数名
        'other_params':{},//其它参数 {'aaa':123,'ccd':'dfasfs'}
    }
};

$(function(){
    //当前市
    if(PROVINCE_ID > 0){
        changeFirstSel(REL_CHANGE.city,PROVINCE_ID,CITY_ID, false);

        // 当前区县
        if(CITY_ID > 0) {
            // var send_department_id = $('select[name=send_department_id]').val();
            var tem_config = REL_CHANGE.area;
            // tem_config.other_params = {'department_id':send_department_id};
            changeFirstSel(tem_config,CITY_ID,AREA_ID, false);
        }
    }


    //省值变动
    $(document).on("change",'select[name=province_id]',function(){
        // 初始化区县下拉框
        initSelect('area_id' ,{"": "请选择区县"});
        changeFirstSel(REL_CHANGE.city, $(this).val(), 0, true);
        return false;
    });
    //市值变动
    $(document).on("change",'select[name=city_id]',function(){
        // var province_id = $('select[name=province_id]').val();
        var tem_config = REL_CHANGE.area;
        // tem_config.other_params = {'province_id':province_id};
        changeFirstSel(tem_config, $(this).val(), 0, true);
        return false;
    });
    $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true);
});

//重载列表
//is_read_page 是否读取当前页,否则为第一页 true:读取,false默认第一页
// ajax_async ajax 同步/导步执行 //false:同步;true:异步  需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
function reset_list_self(is_read_page, ajax_async, reset_total){
    console.log('is_read_page', typeof(is_read_page));
    console.log('ajax_async', typeof(ajax_async));
    var layer_index = layer.load();
    reset_list(is_read_page, false, reset_total);
    // initList();
    initPic();
    layer.close(layer_index)//手动关闭
}

// window.onload = function() {
//     initPic();
// };
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

//业务逻辑部分
var otheraction = {
    staffShop: function(obj, city_site_id, city_partner_id, seller_id, shop_id, text){// 帐号管理
        var obj = $(obj);
        var href = STAFF_SHOP_LIST_URL + '?city_site_id=' + city_site_id + '&city_partner_id=' + city_partner_id + '&seller_id=' + seller_id + '&shop_id=' + shop_id;//
        layuiGoIframe(href, text);
        return false;
    },
    goodsType: function(obj, city_site_id, city_partner_id, seller_id, shop_id, text){// 商品分类管理
        var obj = $(obj);
        var href = GOODS_TYPE_LIST_URL + '?city_site_id=' + city_site_id + '&city_partner_id=' + city_partner_id + '&seller_id=' + seller_id + '&shop_id=' + shop_id;//
        layuiGoIframe(href, text);
        return false;
    },
    props: function(obj, city_site_id, city_partner_id, seller_id, shop_id, text){// 商品属性管理 , shop_id
        var obj = $(obj);
        var href = PROP_LIST_URL + '?city_site_id=' + city_site_id + '&city_partner_id=' + city_partner_id + '&seller_id=' + seller_id  + '&shop_id=' + shop_id ;// + '&shop_id=' + shop_id
        layuiGoIframe(href, text);
        return false;
    },
    goods: function(obj, city_site_id, city_partner_id, seller_id, shop_id, text){// 商品管理
        var obj = $(obj);
        var href = GOODS_LIST_URL + '?city_site_id=' + city_site_id + '&city_partner_id=' + city_partner_id + '&seller_id=' + seller_id + '&shop_id=' + shop_id;//
        layuiGoIframe(href, text);
        return false;
    },
    orders: function(obj, city_site_id, city_partner_id, seller_id, shop_id, text){// 订单管理
        layer_alert("正在开发中...",3,0);
        return false;
        var obj = $(obj);
        var href = ORDERS_LIST_URL + '?city_site_id=' + city_site_id + '&city_partner_id=' + city_partner_id + '&seller_id=' + seller_id + '&shop_id=' + shop_id;//
        layuiGoIframe(href, text);
        return false;
    },
};

(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    document.write("        var resource_list = item.resource_list;");
    //document.write("        var can_modify = false;");
   // document.write("        if( item.issuper==0 ){");
    document.write("        can_modify = true;");
    //document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr>");
    document.write("            <td>");
    document.write("                <label class=\"pos-rel\">");
    document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
    document.write("                  <span class=\"lbl\"><\/span>");
    document.write("                <\/label>");
    document.write("            <\/td>");
    document.write("            <td><%=item.id%><\/td>");
    document.write("            <td><%=item.site_name%><hr/><%=item.partner_name%><hr/><%=item.seller_name%><\/td>");
    document.write("            <td><%=item.shop_name%><hr/><%=item.linkman%><\/td>");
    document.write("            <td><%=item.mobile%><hr/><%=item.tel%><\/td>");
    document.write("            <td><%=item.province_name%><%=item.city_name%><%=item.area_name%><%=item.addr%><\/td>");
    document.write("           <td>");
    document.write("            <%for(var j = 0; j<resource_list.length;j++){");
    document.write("                var jitem = resource_list[j];");
    document.write("                 %>");
    document.write("               <a href=\"<%=jitem.resource_url%>\">");
    document.write("                <img  src=\"<%=jitem.resource_url%>\"  style=\"width:100px;\">");
    document.write("              </a>");
    document.write("            <%}%>");
    document.write("           <\/td>");
    document.write("            <td><%=item.type_name%><\/td>");
    document.write("            <td><%=item.labelNnames%><\/td>");
    document.write("            <td><%=item.sales_volume%><hr/><%=item.mon_sales_volume%><\/td>");
    document.write("            <td><%=item.open_time%><hr/><%=item.close_time%><hr/><%=item.last_update%><\/td>");
    document.write("            <td><%=item.sort_num%><hr/><%=item.per_price%><\/td>");
    document.write("            <td><%=item.intro%><\/td>");
    document.write("            <td>");
    document.write("                <%if( false){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-check bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    document.write("                <\/a>");
    document.write("                <%if( can_modify){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> 删除<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.staffShop(this,<%=item.city_site_id%>,<%=item.city_partner_id%>,<%=item.seller_id%>,<%=item.id%>,'<%=item.shop_name%>-帐号管理')\">");
    document.write("                    <i class=\"ace-icon fa fa-user-circle-o bigger-60\"> 帐号管理<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.goodsType(this,<%=item.city_site_id%>,<%=item.city_partner_id%>,<%=item.seller_id%>,<%=item.id%>,'<%=item.shop_name%>-商品分类管理')\">");
    document.write("                    <i class=\"ace-icon fa fa-cubes bigger-60\"> 商品分类管理<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.props(this,<%=item.city_site_id%>,<%=item.city_partner_id%>,<%=item.seller_id%>,<%=item.id%>,'<%=item.seller_name%>-商品属性管理')\">");
    document.write("                    <i class=\"ace-icon fa fa-cube bigger-60\"> 商品属性管理<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.goods(this,<%=item.city_site_id%>,<%=item.city_partner_id%>,<%=item.seller_id%>,<%=item.id%>,'<%=item.shop_name%>-商品管理')\">");
    document.write("                    <i class=\"ace-icon fa fa-shopping-basket bigger-60\"> 商品管理<\/i>");
    document.write("                <\/a>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.orders(this,<%=item.city_site_id%>,<%=item.city_partner_id%>,<%=item.seller_id%>,<%=item.id%>,'<%=item.shop_name%>-订单管理')\">");
    document.write("                    <i class=\"ace-icon fa fa-cart-arrow-down bigger-60\"> 订单管理<\/i>");
    document.write("                <\/a>");
    document.write("");
    document.write("            <\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();