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

// $(function(){
window.onload = function() {
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
    ajax_status_count(0, 0, 0);//ajax工单状态统计
    // reset_list_self(false, false, true, 2);
    // 自动更新数据
    var autoObj = new Object();
    autoObj.orderProcessList = function(){
        ajax_status_count(1, 0, 0);//ajax工单状态统计
    };
    setInterval(autoObj.orderProcessList,60000);

};
// });

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

// window.onload = function() {
//     initPic();
// };
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

$(function(){
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

//业务逻辑部分
var otheraction = {
//     staff: function(obj, id, text){// 帐号管理
//         var obj = $(obj);
//         var href = STAFF_LIST_URL + '?id=' + id;//
//         layuiGoIframe(href, text);
//         return false;
//     },
//
    cancel : function(obj,order_no,pay_type){// 取消
        var obj = $(obj);
        var index_query = layer.confirm('确定取消当前订单吗？不可恢复!', {
            btn: ['确定','取消'] //按钮
        }, function(){
            cancelOrder(order_no,pay_type);
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    print : function(obj,order_no,order_id){// 打印订单
        var obj = $(obj);
        var index_query = layer.confirm('确定打印当前订单吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            printOrder(order_no,order_id);
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
};
// 取消订单
function cancelOrder(order_no,pay_type){
    var data = {'order_no':order_no,'pay_type':pay_type};
    console.log(CANCEL_ORDER_URL);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : CANCEL_ORDER_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                ajax_status_count(0, 0, 0);//ajax工单状态统计
                reset_list_self(true,false,true, 2);
            }
            layer.close(layer_index)//手动关闭
        }
    })
}

// 打印订单
function printOrder(order_no,order_id){
    var data = {'order_no':order_no,'order_id':order_id};
    console.log(PRINT_ORDER_URL);
    console.log(data);
    var layer_index = layer.load();
    //layer_alert("已打印"+print_nums+"打印第"+begin_page+"页-第"+end_page+"页;每次打"+per_page_num+"页",3);
    var url = PRINT_ORDER_URL + '/' + order_id;
    console.log('打印订单地址：', url);
    PrintOneURL(url);
    layer.close(layer_index)//手动关闭
}
(function() {
    document.write("");
    document.write("<!-- 前端模板部分 -->");
    document.write("<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%> -->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("    <%for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    %>");
    document.write("    <div class=\"w-wrap4 order_status obligation clearfix\">");
    document.write("        <table class=\"order_info clearfix\">");
    document.write("            <thead><tr><td>订单号<\/td><td>地址|客户（电话）<\/td><td>希望速度（到达时间）<\/td><td>商品金额<\/td><td>跑腿费 /退费（未支付）<\/td><td>下单时间<\/td><td>订单状态<\/td><\/tr><\/thead>");
   // document.write("            <span><%=item.created_at%><\/span>");
   document.write("             <tbody><tr>        ");
    document.write("            <td><%=item.order_no_format%><\/td>");
    document.write("            <td class=\"shop\"><%=item.addr.addr%>  <%=item.addr.real_name%>(<%=item.addr.mobile_format%>)<\/td>");
    // document.write("            <td>餐具数量:<%=item.tableware%>份<\/td>");
    document.write("            <td><%=item.second_num%>分钟(<%=item.send_end_time%>)<\/td>"); //希望速度:
    document.write("            <td><%=item.total_price%>元(共<%=item.total_amount%>份)<\/td>"); //商品金额:
    document.write("            <td><%=item.pay_run_amount%>元(<%=item.pay_run_price_text%>) "); //跑腿费:
    document.write("             退费:<%=item.refund_price%>元(<%=item.has_refund_text%>)<\/td>");
    document.write("            <%if(item.refund_time){%>");
    document.write("            <td>t-<%=item.refund_time%><\/td>");//退费时间:
    document.write("            <%}%>");
    document.write("            <%if(item.order_time){%>");
    document.write("            <td>x-<%=item.order_time%><\/td>");//下单时间:
    document.write("            <%}%>");
    document.write("            <td class=\"status\">");
    document.write("                <span><%=item.status_text%><\/span>");
    document.write("            <\/td>");
    document.write("            <\/tr><\/tbody><\/table><table><tr>");
    document.write("            <td>付款时间 <\/td> <td>接单时间 <\/td> <td>取消时间 <\/td> <td>完成时间 <\/td><\/tr> <\/tbody><\/table><table><tr>");
    document.write("            <%if(item.pay_time){%>");
    document.write("            <td>f-<%=item.pay_time%><\/td>"); //付款时间:
    document.write("            <%}%>");
    document.write("            <%if(item.receipt_time){%>");
    document.write("            <td>j-<%=item.receipt_time%><\/td>"); //接单时间:
    document.write("            <%}%>");
    document.write("            <%if(item.cancel_time){%>");
    document.write("            <td>q-<%=item.cancel_time%><\/td>");//取消时间:
    document.write("            <%}%>");
    document.write("            <%if(item.finish_time){%>");
    document.write("            <td>w-<%=item.finish_time%><\/td>");//完成时间:
    document.write("            <%}%>");
    document.write("            <\/tr><\/tbody><\/table><table><tr>");
    document.write("            <%if(item.send_staff_id > 0){%>");
    document.write("            <td>接单人:<%=item.send.real_name%> (<%=item.send.staff_id%>:<%=item.send.nickname%> )<img  src=\"<%=item.send.avatar_url%>\"  style=\"width:30px;\"><\/td>");
    document.write("            <%if(item.send.real_name){%>");
    document.write("            <td>接单人姓名:<\/td>");
    document.write("            <%}%>");
    document.write("            <%if(item.send.mobile){%>");
    document.write("            <td>接单人手机:<%=item.send.mobile_format%><\/td>");
    document.write("            <%}%>");
    document.write("            <%if(item.send.tel){%>");
    document.write("            <td>接单人电话:<%=item.send.tel%><\/td>");
    document.write("            <%}%>");
    document.write("            <%}%>");
    document.write("            <i><\/i>");
    document.write("            <\/tr><\/tbody><\/table>");
    document.write("            <\/div>");
    document.write("            <div class=\"w-wrap5 clearfix\">");
    document.write("            <div class=\"operate\">");
    document.write("                <%if(item.status == 2){%>");
    document.write("                <a class=\"cancel\" href=\"javascript:void(0);\" onclick=\"otheraction.cancel(this,\'<%=item.order_no%>\',\'<%=item.pay_type%>\')\" >取消订单<\/a>");
    document.write("                <%}%>");
    document.write("                <a class=\"cancel\" href=\"javascript:void(0);\" onclick=\"otheraction.print(this,\'<%=item.order_no%>\',\'<%=item.id%>\')\" >打印订单<\/a>");
    document.write("            <\/div>");

    document.write("            <%for(var j = 0; j<item.shopList.length;j++){");
    document.write("            var shopitem = item.shopList[j];");
    document.write("            var orders_goods = shopitem.orders_goods;");
    document.write("            %>");
    document.write("            <table style=\"margin-top: 5px;margin-bottom: 20px; border:1px solid #ddd;\">");
    document.write("                <tr>");
    document.write("                    <td>");
    document.write("                        <span class=\"shop\">店铺：<%=shopitem.shop.shop_name%><\/span><br>");
    document.write("                        <span>地址：");
    document.write("                        <%=shopitem.city?shopitem.city.city_name:''%>");
    document.write("                        <%=shopitem.area?shopitem.area.city_name:''%>");
    document.write("                        <%=shopitem.shop.addr%>");
    document.write("                        <\/span><br>");
    document.write("                        <span>金额：<%=shopitem.total_price%>元(共<%=shopitem.total_amount%>份)<\/span>");
    document.write("                    <\/td>");
    //document.write("                <\/tr>");
    //document.write("                <tr>");
    document.write("                    <td>");
    document.write("                        <!--循序输出-->");
    document.write("                        <%for(var k = 0; k<orders_goods.length;k++){");
    document.write("                        var gooditem = orders_goods[k];");
    document.write("                        %>");
    document.write("                        <div class=\"single_item clearfix\">");
    document.write("                                    <span class=\"pro_info\">");
    // document.write("                                        <a href=\"<%=gooditem.resource_url%>\">");
    // document.write("                                        <img width=\"40\" height=\"40\" src=\"<%=gooditem.resource_url%>\" \/>");
    // document.write("                                        <\/a>");
    // document.write("                                        <span>");
    document.write("                                        <%=gooditem.goods_name%>----<%=gooditem.cart_id%>");
    document.write("                                        <%if(gooditem.pricePropValName  && gooditem.pricePropValName !=\'\'){%>");
    document.write("                                        [<%=gooditem.pricePropName%>:<%=gooditem.pricePropValName%> ]");
    document.write("                                        <%}%>");
    document.write("                                        ");//<\/span>
    document.write("                                        <%if(gooditem.prop){%>");
    document.write("                                            <%for(var h = 0; h<gooditem.prop.length;h++){");
    document.write("                                                var propitem = gooditem.prop[h];");
    document.write("                                                %>");
    document.write("                                                <i><%=propitem.prop_name%>：<%=propitem.pv_names%><\/i>");
    document.write("                                             <%}%>");
    document.write("                                        <%}%>");
    document.write("                                    <\/span>");
    document.write("                            <div class=\"money\">");
    document.write("                                <span>¥<%=gooditem.price%><\/span>");
    document.write("                            <\/div>");
    document.write("                            <div class=\"count\">* <%=gooditem.amount%>=¥<%=gooditem.total_price%><\/div>");
    document.write("                        <\/div>");
    document.write("                        <%}%>");
    document.write("                    <\/td>");
    document.write("                <\/tr>");
    document.write("            <\/table>");
    document.write("            <%}%>");
    document.write("        <\/div>");
    document.write("    <\/div>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();