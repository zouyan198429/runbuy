
var SUBMIT_FORM = true;//防止多次点击提交

//获取当前窗口索引
var PARENT_LAYER_INDEX = parent.layer.getFrameIndex(window.name);
//让层自适应iframe
////parent.layer.iframeAuto(PARENT_LAYER_INDEX);
// parent.layer.full(PARENT_LAYER_INDEX);// 用这个
//关闭iframe
$(document).on("click",".closeIframe",function(){
    iframeclose(PARENT_LAYER_INDEX);
});
//刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_only_reset_list(reset_total){
    window.parent.reset_list(true, true, reset_total);//刷新父窗口列表
}
//关闭弹窗,并刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_reset_list_iframe_close(reset_total){
    window.parent.reset_list(true, true, reset_total);//刷新父窗口列表
    parent.layer.close(PARENT_LAYER_INDEX);
}
//关闭弹窗
function parent_reset_list(){
    parent.layer.close(PARENT_LAYER_INDEX);
}

$(function(){

    //提交
    $(document).on("click","#submitBtn",function(){
        //var index_query = layer.confirm('您确定提交保存吗？', {
        //    btn: ['确定','取消'] //按钮
        //}, function(){
        ajax_form();
        //    layer.close(index_query);
        // }, function(){
        //});
        return false;
    });

    // 解析属性值数据
    initPropVal('prop_table', PV_LIST_JSON, 1);
});

//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = $('input[name=id]').val();
    if(!judge_validate(4,'记录id',id,true,'digit','','')){
        return false;
    }

    // 所属商家
    // var seller_id = $('input[name=seller_id]').val();
    // var judge_seled = judge_validate(1,'所属商家',seller_id,true,'positive_int','','');
    // if(judge_seled != ''){
    //     layer_alert("请选择所属商家",3,0);
    //     return false;
    // }

    // 所属店铺
    var shop_id = $('input[name=shop_id]').val();
    var judge_seled = judge_validate(1,'所属店铺',shop_id,true,'positive_int','','');
    if(judge_seled != ''){
        layer_alert("请选择所属店铺",3,0);
        return false;
    }

    var main_name = $('input[name=main_name]').val();
    if(!judge_validate(4,'属性名称',main_name,true,'length',1,50)){
        return false;
    }

    var pv_i = 0;
    var pverr = false;
    $('.pvbody').find('tr').each(function(){
        var trObj = $(this);
        var pv_val = trObj.find('input[name="pv_names[]"]').val();
        if(!judge_validate(4,'属性值',pv_val,true,'length',2,20)){
            pverr = true;
            return false;
        }
        console.log('----pv_i-------', pv_i);
        pv_i += 1;
    });
    if(pverr){
        return false;
    }

    if(pv_i <= 0){
        var prop_vals = $('textarea[name=prop_vals]').val();
        if(!judge_validate(4,'属性值',prop_vals,true,'length',2,500)){
            return false;
        }
    }

    var sort_num = $('input[name=sort_num]').val();
    if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
        return false;
    }

    // 验证通过
    SUBMIT_FORM = false;//标记为已经提交过
    var data = $("#addForm").serialize();
    console.log(SAVE_URL);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : SAVE_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                // go(LIST_URL);

                // countdown_alert("操作成功!",1,5);
                // parent_only_reset_list(false);
                // wait_close_popus(2,PARENT_LAYER_INDEX);
                layer.msg('操作成功！', {
                    icon: 1,
                    shade: 0.3,
                    time: 3000 //2秒关闭（如果不配置，默认是3秒）
                }, function(){
                    if(FRM == 0 || FRM == '0'){
                        var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                        if(id > 0) reset_total = false;
                        parent_reset_list_iframe_close(reset_total);// 刷新并关闭
                    }else{
                        ids = ret.result;
                        console.log(ids);
                        parent.addProp(ids + '');
                        // initList();
                        parent_reset_list();// 关闭弹窗
                    }
                    //do something
                });
                // var supplier_id = ret.result['supplier_id'];
                //if(SUPPLIER_ID_VAL <= 0 && judge_integerpositive(supplier_id)){
                //    SUPPLIER_ID_VAL = supplier_id;
                //    $('input[name="supplier_id"]').val(supplier_id);
                //}
                // save_success();
            }
            layer.close(layer_index)//手动关闭
        }
    });
    return false;
}


//业务逻辑部分
var otheraction = {
    selectShop: function(obj){// 选择商家
        var recordObj = $(obj);
        //获得表单各name的值
        var weburl = SELECT_SHOP_URL;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '选择店铺';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,900,450,0);
        return false;
    },
    updateShop : function(obj){// 商家更新
        var recordObj = $(obj);
        var index_query = layer.confirm('确定更新当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var shop_id = $('input[name=shop_id]').val();
            addShop(shop_id);
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    removePv : function(obj){// 移除
        var recordObj = $(obj);
        var addBtnObj = $(obj);
        var trObj = addBtnObj.closest('tr');//获取当前<tr>'tr'
        var prop_val_id = trObj.find('input[name="pv_ids[]"]').val();
        console.log('------prop_val_id------', prop_val_id);

        var index_query = layer.confirm('确定移除当前记录？[提交]后不可恢复!', {
            btn: ['确定','取消'] //按钮
        }, function(){

            var data = {};
            data['prop_val_id'] = prop_val_id;
            console.log('data', data);
            console.log('JUDGE_PV_USED_URL', JUDGE_PV_USED_URL);
            var layer_index = layer.load();
            $.ajax({
                'async': false,// true,//false:同步;true:异步
                'type' : 'POST',
                'url' : JUDGE_PV_USED_URL,
                'data' : data,
                'dataType' : 'json',
                'success' : function(ret){
                    console.log('ret',ret);
                    if(!ret.apistatus){//失败
                        //alert('失败');
                        err_alert(ret.errorMsg);
                    }else{//成功
                        var info = ret.result;
                        console.log('info', info);

                        recordObj.closest('tr').remove();
                    }
                    layer.close(index_query);

                    layer.close(layer_index)//手动关闭
                }
            });
        }, function(){
        });
        return false;
    },
    moveUp : function(obj, parentTag){// 上移
        var recordObj = $(obj);
        var current = recordObj.closest(parentTag);//获取当前<tr>  'tr'
        var prev = current.prev();  //获取当前<tr>前一个元素
        console.log('index', current.index());
        if (current.index() > 0) {
            current.insertBefore(prev); //插入到当前<tr>前一个元素前
        }else{
            layer_alert("已经是第一个，不能移动了。",3,0);
        }
        return false;
    },
    moveDown : function(obj, parentTag){// 下移
        var recordObj = $(obj);
        var current = recordObj.closest(parentTag);//获取当前<tr>'tr'
        var next = current.next(); //获取当前<tr>后面一个元素
        console.log('length', next.length);
        console.log('next', next);
        if (next.length > 0 && next) {
            current.insertAfter(next);  //插入到当前<tr>后面一个元素后面
        }else{
            layer_alert("已经是最后一个，不能移动了。",3,0);
        }
        return false;
    },
    // 添加属性值
    addPVName : function(obj){// 下移
        var addBtnObj = $(obj);
        var trObj = addBtnObj.closest('tr');//获取当前<tr>'tr'
        var propname = trObj.find('input[name="propname"]').val();
        if(!judge_validate(4,'属性值',propname,true,'length',1,50)){
            return false;
        }
        // 遍历已有的属性值
        var pverr = false;
        $('.pvbody').find('tr').each(function(){
            var trObj = $(this);
            var pv_val = trObj.find('input[name="pv_names[]"]').val();
            if(propname == pv_val){
                layer_alert('属性值[' + propname + ']已存在！',3,0);
                pverr = true;
                return false;
            }
        });
        if(pverr){
            return false;
        }
        // 添加
        // 数据模板，显示数据
        var data_list = {
            'data_list': [{'id':0,'main_name':propname}],
        };
        console.log('data_list', data_list);
        // 解析数据
        initPropVal('prop_table', data_list, 2);
        // 清空
        trObj.find('input[name="propname"]').val('');
        return false;
    },
};

// 初始化属性值列表
// data_list 数据对象 {'data_list':[{}]}
// type类型 1 全替换 2 追加到后面 3 返回html
function initPropVal(class_name, data_list, type){
    var htmlStr = resolve_baidu_template(DYNAMIC_PROPVAL_BAIDU_TEMPLATE,data_list,'');//解析
    if(type == 3) return htmlStr;
    //alert(htmlStr);
    if(type == 1){
        $('.'+ class_name).find('.' + DYNAMIC_PROPVAL_TABLE_BODY).html(htmlStr);
    }else if(type == 2){
        $('.'+ class_name).find('.' + DYNAMIC_PROPVAL_TABLE_BODY).append(htmlStr);
    }
}

// 获得选中的店铺id 数组
function getSelectedShopIds(){
    var shop_ids = [];
    var shop_id = $('input[name=shop_id]').val();
    shop_ids.push(shop_id);
    console.log('shop_ids' , shop_ids);
    return shop_ids;
}

// 取消
// shop_id 店铺id
function removeShop(shop_id){
    var seled_shop_id = $('input[name=shop_id]').val();
    if(shop_id == seled_shop_id){
        $('input[name=seller_id]').val('');

        $('input[name=shop_id]').val('').change();
        $('input[name=shop_id_history]').val('');
        $('.shop_name').html('');
        $('.update_shop').hide();
    }
}

// 增加
// shop_id 店铺id, 多个用,号分隔
function addShop(shop_id){
    if(shop_id == '') return ;
    var data = {};
    data['id'] = shop_id;
    console.log('data', data);
    console.log('AJAX_SHOP_SELECTED_URL', AJAX_SHOP_SELECTED_URL);
    var layer_index = layer.load();
    $.ajax({
        'async': false,// true,//false:同步;true:异步
        'type' : 'POST',
        'url' : AJAX_SHOP_SELECTED_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log('ret',ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                var info = ret.result;
                console.log('info', info);
                $('input[name=seller_id]').val(info.seller_id);

                $('input[name=shop_id]').val(info.id).change();
                $('input[name=shop_id_history]').val(info.history_id);
                $('.shop_name').html(info.shop_name);
                var now_state = info.now_state;// 最新的 0没有变化 ;1 已经删除  2 试卷不同
                if(now_state == 2 ){
                    $('.update_shop').show();
                }else{
                    $('.update_shop').hide();
                }
            }
            layer.close(layer_index)//手动关闭
        }
    });
}

(function() {
    document.write("<!-- 前端模板部分 -->");
    document.write("<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%> -->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_pv_data_list\">");
    document.write("    <%for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    %>");
    document.write("    <tr>");
    document.write("        <td>");
    document.write("            <input type=\"hidden\" class=\"inp wnormal\"  name=\"pv_ids[]\" value=\"<%=item.id%>\"\/>");
    document.write("            <input type=\"text\" class=\"inp wnormal\"  name=\"pv_names[]\" value=\"<%=item.main_name%>\" placeholder=\"请输入属性值名称\" \/>");
    document.write("        <\/td>");
    document.write("        <td>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveUp(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-up bigger-60\"> 上移<\/i>");
    document.write("            <\/a>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveDown(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-down bigger-60\"> 下移<\/i>");
    document.write("            <\/a>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.removePv(this)\">");
    document.write("                <i class=\"ace-icon fa fa-trash-o bigger-60\"> 移除<\/i>");
    document.write("            <\/a>");
    document.write("        <\/td>");
    document.write("    <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();