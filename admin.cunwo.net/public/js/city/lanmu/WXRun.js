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

    // $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
});

//重载列表
//is_read_page 是否读取当前页,否则为第一页 true:读取,false默认第一页
// ajax_async ajax 同步/导步执行 //false:同步;true:异步  需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
// do_num 调用时: 1 初始化页面时[默认];2 初始化页面后的调用
function reset_list_self(is_read_page, ajax_async, reset_total, do_num){
    console.log('is_read_page', typeof(is_read_page));
    console.log('ajax_async', typeof(ajax_async));
    var layer_index = layer.load();
    reset_list(is_read_page, false, reset_total, do_num);
    // initList();
    initPic();
    layer.close(layer_index)//手动关闭
}

window.onload = function() {
    $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
//     initPic();
};
function initPic(){
    // baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

//业务逻辑部分
var otheraction = {
    pass : function(id){// 通过
        var index_query = layer.confirm('确定通过审核吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            staffOperate(id, 2, '');
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    notpass : function(id){// 不通过//prompt层
        layer.prompt({title: '确定不通过审核吗？', formType: 0, maxlength: 80}, function(reason, index){
            layer.close(index);
            if(reason == '') return false;
            // alert(reason + '->' + id);
            staffOperate(id, 3, reason);
        });
        // var index_query = layer.confirm('确定不通过审核吗？', {
        //     btn: ['确定','取消'] //按钮
        // }, function(){
        //     layer.close(index_query);
        // }, function(){
        // });
        return false;
    },
    frozen : function(id){// 冻结
        layer.prompt({title: '确定冻结吗？', formType: 0, maxlength: 80 }, function(reason, index){
            layer.close(index);
            if(reason == '') return false;
            // alert(reason + '->' + id);
            staffOperate(id, 4, reason);
        });
        // var index_query = layer.confirm('确定冻结吗？', {
        //     btn: ['确定','取消'] //按钮
        // }, function(){
        //     layer.close(index_query);
        // }, function(){
        // });
        return false;
    },
    thaw : function(id){// 解冻
        var index_query = layer.confirm('确定解冻吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            staffOperate(id, 5, '');
            layer.close(index_query);
        }, function(){
        });
        return false;
    },

};

// 用户操作
function staffOperate(staff_id, operate_type, reason){
    var data = {'id':staff_id,'operate_type':operate_type,'reason':reason};
    console.log(STAFF_OPERATE_URL);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : STAFF_OPERATE_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                reset_list_self(true,false,true, 2);
            }
            layer.close(layer_index)//手动关闭
        }
    })
}

(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    document.write("        var can_modify = false;");
    document.write("        if( item.issuper==0 ){");
    document.write("        can_modify = true;");
    document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr  <%if( item.account_status == 1){%> class=\" red \" <%}%> >");
    document.write("            <td>");
    document.write("                <label class=\"pos-rel\">");
    document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
    document.write("                  <span class=\"lbl\"><\/span>");
    document.write("                <\/label>");
    document.write("            <\/td>");
    document.write("            <td><%=item.site_name%><hr/><%=item.partner_name%><\/td>");
    document.write("            <td><%=item.real_name%><hr/><%=item.mobile%><\/td>");
    // document.write("            <td><%=item.wx_unionid%><\/td>");
    // document.write("            <td><%=item.mini_openid%><\/td>");
    // document.write("            <td><%=item.mp_openid%><\/td>");
    document.write("            <td><%=item.nickname%><\/td>");
    document.write("            <td><%=item.sex_text%><\/td>");
    document.write("            <td><%=item.country%><\/td>");
    document.write("            <td><%=item.province%><\/td>");
    document.write("            <td><%=item.city%><\/td>");
    document.write("           <td>");
    // document.write("               <a href=\"<%=item.avatar_url%>\">");
    document.write("                <img  src=\"<%=item.avatar_url%>\"  style=\"width:50px;\">");
    // document.write("              </a>");
    document.write("           <\/td>");
    document.write("            <td><%=item.on_line_text%><\/td>");
    document.write("            <td >");
    document.write("            <%=item.open_status_text%>");
    document.write("            <%if( item.open_status == 3){%>");
    document.write("                [<%=item.open_fail_reason%>]");
    document.write("            <%}%>");
    document.write("            <hr/>");
    document.write("            <%=item.account_status_text%>");
    document.write("            <%if( item.account_status == 1){%>");
    document.write("                [<%=item.frozen_fail_reason%>]");
    document.write("            <%}%>");
    document.write("            <\/td>");
    // document.write("            <td><%=item.issuper_text%><hr/><%=item.account_status_text%><\/td>");
    document.write("            <td><%=item.created_at%><hr/><%=item.updated_at%><\/td>");
    document.write("            <td >");
    // document.write("                <%if( true){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.showLog(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-list-alt bigger-60\"> 查看操作日志<\/i>");
    // document.write("                <\/a>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.showOnLineLog(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-list-ol bigger-60\"> 查看上班日志<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    // document.write("                <\/a>");
    document.write("                <%if( can_modify){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> 删除<\/i>");
    // document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("            <%if( item.open_status == 1){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.pass(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-check bigger-60\"> 通过<\/i>");
    document.write("                <\/a>");
    document.write("            <%}%>");
    document.write("            <%if( item.open_status == 1){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.notpass(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-times bigger-60\"> 不通过<\/i>");
    document.write("                <\/a>");
    document.write("            <%}%>");
    document.write("            <%if( item.account_status == 0){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.frozen(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-lock bigger-60\"> 冻结<\/i>");
    document.write("                <\/a>");
    document.write("            <%}%>");
    document.write("            <%if( item.account_status == 1){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.thaw(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-unlock bigger-60\"> 解冻<\/i>");
    document.write("                <\/a>");
    document.write("            <%}%>");
    document.write("");
    document.write("            <\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();