const REL_CHANGE = {
    'city':{// 市-二级分类
        'child_sel_name': 'city_id',// 第二级下拉框的name
        'child_sel_txt': {'': "请选择市" },// 第二级下拉框的{值:请选择文字名称}
        'change_ajax_url': PROVINCE_CHILD_URL,// 获取下级的ajax地址
        'parent_param_name': 'parent_id',// ajax调用时传递的参数名
        'other_params':{},//其它参数 {'aaa':123,'ccd':'dfasfs'}
    },
    // 'area':{// 区县---二级分类
    //     'child_sel_name': 'area_id',// 第二级下拉框的name
    //     'child_sel_txt': {'': "请选择区县" },// 第二级下拉框的{值:请选择文字名称}
    //     'change_ajax_url': CITY_CHILD_URL,// 获取下级的ajax地址
    //     'parent_param_name': 'parent_id',// ajax调用时传递的参数名
    //     'other_params':{},//其它参数 {'aaa':123,'ccd':'dfasfs'}
    // }
};

$(function(){
    //当前市
    if(PROVINCE_ID > 0){
        changeFirstSel(REL_CHANGE.city,PROVINCE_ID,CITY_ID, false);

        // 当前区县
        // if(CITY_ID > 0) {
        //     // var send_department_id = $('select[name=send_department_id]').val();
        //     var tem_config = REL_CHANGE.area;
        //     // tem_config.other_params = {'department_id':send_department_id};
        //     changeFirstSel(tem_config,CITY_ID,AREA_ID, false);
        // }
    }


    //省值变动
    $(document).on("change",'select[name=province_id]',function(){
        // 初始化区县下拉框
        initSelect('area_id' ,{"": "请选择区县"});
        changeFirstSel(REL_CHANGE.city, $(this).val(), 0, true);
        return false;
    });
    //市值变动
    // $(document).on("change",'select[name=city_id]',function(){
    //     // var province_id = $('select[name=province_id]').val();
    //     var tem_config = REL_CHANGE.area;
    //     // tem_config.other_params = {'province_id':province_id};
    //     changeFirstSel(tem_config, $(this).val(), 0, true);
    //     return false;
    // });
});

(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
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
    document.write("            <td><%=item.cityPath%><\/td>");
    document.write("            <td><%=item.city_name%><\/td>");
    document.write("            <td><%=item.code%><\/td>");
    document.write("            <td><%=item.head%><\/td>");
    document.write("            <td><%=item.initial%><\/td>");
    document.write("            <td><%=item.sort_num%><\/td>");
    // document.write("            <td><%=city_name.tel%><\/td>");
    document.write("            <td><%=item.is_city_site_text%><\/td>");
    document.write("            <td><%=item.city_type_text%><\/td>");
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
    document.write("");
    document.write("            <\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();