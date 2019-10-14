
$(function(){
    // $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
    //提交
    $(document).on("click",".status_click",function(){
        var obj = $(this);
        var status = obj.data('status');
        console.log(status);
        // 获得兄弟姐妹
        // obj.siblings().removeClass("on");
        // obj.addClass("on");
        $('select[name=status]').val(status);
        $(".search_frm").click();
        return false;
    })
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
    ajax_status_count(0, 0, 0);//ajax工单状态统计
    // reset_list_self(false, false, true, 2);
    // 自动更新数据
    var autoObj = new Object();
    autoObj.orderProcessList = function(){
        ajax_status_count(1, 0, 0);//ajax工单状态统计
    };
    setInterval(autoObj.orderProcessList,30000);// 30秒
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

//ajax状态统计
// from_id 来源 0 页面第一次加载,不播放音乐 1 每分钟获得数量，有变化，播放音乐
function ajax_status_count(from_id ,staff_id, operate_staff_id){
    // if (!SUBMIT_FORM) return false;//false，则返回
    append_sure_form(SURE_FRM_IDS,FRM_IDS);//把搜索表单值转换到可以查询用的表单中
    //获得表单各name的值
    var data = get_frm_values(SURE_FRM_IDS);
    // 验证通过
    // SUBMIT_FORM = false;//标记为已经提交过
    // var data = {
    //     'staff_id': staff_id,
    //     'operate_staff_id': operate_staff_id,
    // };
    data.staff_id = staff_id;
    data.operate_staff_id = operate_staff_id;
    delete data.status;
    // delete data.field;
    // delete data.keyword;
    console.log(SATUS_COUNT_URL);
    console.log(data);
    if( from_id == 0)  var layer_count_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : SATUS_COUNT_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                var statusCount = ret.result;
                console.log(statusCount);
                var doStatus = NEED_PLAY_STATUS;
                var doStatusArr = doStatus.split(',');

                // 遍历
                var needPlay = false;// true：播放；false:不播放
                var selected_status = $('select[name=status]').val();
                for(var temStatus in statusCount){//遍历json对象的每个key/value对,p为key
                    var countObj = $(".status_count_" + temStatus );
                    if(countObj.length <= 0) continue;
                    var temCount = statusCount[temStatus];
                    var oldCount = countObj.data('old_count');
                    console.log(oldCount);
                    console.log(temCount);
                    if(oldCount != temCount){
                        countObj.html(temCount);
                        countObj.data('old_count', temCount);
                        console.log('new_order');
                        // 数量变大了
                        if( from_id == 1 && (!needPlay) && temCount > oldCount  && doStatusArr.indexOf(temStatus) >= 0){
                            needPlay = true;
                        }

                        // 刷新列表-当前页
                        if( from_id == 1 && (selected_status == temStatus || (needPlay && selected_status == '' )) ){
                            console.log('刷新列表-当前页');
                            // reset_list(true, true);
                            reset_list_self(true,false,true,2);
                        }
                    }
                }
                if(needPlay && from_id == 1){// 播放
                    console.log('播放提示音');
                    run_sound('new_order');
                }

                // status_count_
            }
            // SUBMIT_FORM = true;//标记为未提交过
            if( from_id == 0)   layer.close(layer_count_index);//手动关闭
        }
    });
    return false;
}

//业务逻辑部分
var otheraction = {
//     staff: function(obj, id, text){// 帐号管理
//         var obj = $(obj);
//         var href = STAFF_LIST_URL + '?id=' + id;//
//         layuiGoIframe(href, text);
//         return false;
//     },
    createQrcode : function(obj, id){// 生成二维码
        var obj = $(obj);
        var index_query = layer.confirm('确定生成二维码吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            create_qrcode(id);
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    print : function(obj, id){// 打印二维码
        var recordObj = $(obj);
        var index_query = layer.confirm('确定打印当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            printTables('print',id);
            layer.close(index_query);
        }, function(){
        });
        return false;
        //if(false) {
        //   var sure_cancel_data = {
        //       'content':'确定删除当前记录？删除后不可恢复! ',//提示文字
        //       'sure_event':'del_sure('+id+');',//确定
        //   };
        //  sure_cancel_alert(sure_cancel_data);
        //  return false;
        //}
    },
    batchPrint:function(obj) {// 批量打印二维码
        var recordObj = $(obj);
        var index_query = layer.confirm('确定打印当前记录？!', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1);
            printTables('batch_print',ids);
            layer.close(index_query);
        }, function(){
        });
        return false;

    },
    down : function(obj, id){// 下载二维码
        var recordObj = $(obj);
        var index_query = layer.confirm('确定下载当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            downOperate('down',id);
            layer.close(index_query);
        }, function(){
        });
        return false;
        //if(false) {
        //   var sure_cancel_data = {
        //       'content':'确定删除当前记录？删除后不可恢复! ',//提示文字
        //       'sure_event':'del_sure('+id+');',//确定
        //   };
        //  sure_cancel_alert(sure_cancel_data);
        //  return false;
        //}
    },
    downDrive : function(obj){// 下载网页打印机驱动
        var recordObj = $(obj);
        var index_query = layer.confirm('确定下载网页打印机驱动？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            down_drive();
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
};

//打印操作
function printTables(operate_type,ids){
    if(operate_type=='' || ids ==''){
        err_alert('请选择需要操作的数据');
        return false;
    }
    var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
    //layer_alert("已打印"+print_nums+"打印第"+begin_page+"页-第"+end_page+"页;每次打"+per_page_num+"页",3);
    var url = PRINT_QRCODE_URL + '/' + ids;
    console.log('打印二维码地址：', url);
    PrintOneURL(url);
    layer.close(layer_index)//手动关闭
}
//下载操作
function downOperate(operate_type,id){
    if(operate_type=='' || id ==''){
        err_alert('请选择需要操作的数据');
        return false;
    }
    var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
    //layer_alert("已打印"+print_nums+"打印第"+begin_page+"页-第"+end_page+"页;每次打"+per_page_num+"页",3);
    var url = DOWN_QRCODE_URL + '/' + id;
    console.log('下载二维码地址：', url);
    // PrintOneURL(url);
    go(url);
    layer.close(layer_index)//手动关闭
}

//下载网页打印机驱动
function down_drive(){
    var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
    //layer_alert("已打印"+print_nums+"打印第"+begin_page+"页-第"+end_page+"页;每次打"+per_page_num+"页",3);
    var url = DOWN_DRIVE_URL ;
    console.log('下载网页打印机驱动：', url);
    // PrintOneURL(url);
    go(url);
    layer.close(layer_index)//手动关闭
}
// 生成二维码
function create_qrcode(id){
    var data = {'id':id};
    console.log(CREATE_QRCODE_URL);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : CREATE_QRCODE_URL,
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
    document.write("        var resource_list = item.resource_list;");
    document.write("        var has_qrcode = item.has_qrcode;");
    document.write("        var qrcode_url = item.qrcode_url;");
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
    document.write("            <td><%=item.site_name%><\/td>");
    document.write("            <td><%=item.partner_name%><\/td>");
    document.write("            <td><%=item.seller_name%><\/td>");
    document.write("            <td><%=item.shop_name%><\/td>");
    document.write("            <td><%=item.table_name%><\/td>");
    document.write("            <td><%=item.person_name%><br/>(前缀:<%=item.prefix_name%>)<\/td>");
    document.write("           <td>");
    document.write("            <%for(var j = 0; j<resource_list.length;j++){");
    document.write("                var jitem = resource_list[j];");
    document.write("                 %>");
    document.write("               <a href=\"<%=jitem.resource_url%>\">");
    document.write("                <img  src=\"<%=jitem.resource_url%>\"  style=\"width:60px;\">");
    document.write("              </a>");
    document.write("            <%}%>");
    document.write("           <\/td>");
    document.write("            <td>");
    document.write("            <%if( has_qrcode == 1){%>");
    // document.write("               <%=item.has_qrcode_text%><br/>");
    document.write("               <button class=\"layui-btn  layui-btn-xs layui-btn-normal\"  onclick=\"otheraction.createQrcode(this,<%=item.id%>)\" >生成二维码</button>");
    document.write("            <%}else if(has_qrcode == 2){%>");
    document.write("               <a href=\"<%=qrcode_url%>\">");
    document.write("                <img  src=\"<%=qrcode_url%>\"  style=\"width:100px;\">");
    document.write("              </a><br/>");
    document.write("               <button class=\"layui-btn  layui-btn-xs layui-btn-normal\"  onclick=\"otheraction.print(this,<%=item.id%>)\" >打印</button>");
    document.write("               <button class=\"layui-btn  layui-btn-xs layui-btn-normal\"  onclick=\"otheraction.down(this,<%=item.id%>)\" >下载</button>");
    document.write("            <%}%>");
    document.write("           <\/td>");
    document.write("            <td><%=item.is_open_text%><\/td>");
    document.write("            <td><%=item.status_text%><\/td>");
    document.write("            <td><%=item.sort_num%><\/td>");
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