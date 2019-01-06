
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
    })

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
    var seller_id = $('input[name=seller_id]').val();
    var judge_seled = judge_validate(1,'所属商家',seller_id,true,'positive_int','','');
    if(judge_seled != ''){
        layer_alert("请选择所属商家",3,0);
        return false;
    }

    var main_name = $('input[name=main_name]').val();
    if(!judge_validate(4,'属性名称',main_name,true,'length',1,50)){
        return false;
    }

    var prop_vals = $('textarea[name=prop_vals]').val();
    if(!judge_validate(4,'属性值',prop_vals,true,'length',2,500)){
        return false;
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
    selectSeller: function(obj){// 选择商家
        var recordObj = $(obj);
        //获得表单各name的值
        var weburl = SELECT_SELLER_URL;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '选择商家';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,900,450,0);
        return false;
    },
    updateSeller : function(obj){// 商家更新
        var recordObj = $(obj);
        var index_query = layer.confirm('确定更新当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var seller_id = $('input[name=seller_id]').val();
            addSeller(seller_id);
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
};

// 获得选中的商家id 数组
function getSelectedSellerIds(){
    var seller_ids = [];
    var seller_id = $('input[name=seller_id]').val();
    seller_ids.push(seller_id);
    console.log('seller_ids' , seller_ids);
    return seller_ids;
}

// 取消
// seller_id 商家id
function removeSeller(seller_id){
    var seled_seller_id = $('input[name=seller_id]').val();
    if(seller_id == seled_seller_id){
        $('input[name=seller_id]').val('');
        $('input[name=seller_id_history]').val('');
        $('.seller_name').html('');
        $('.update_seller').hide();
    }
}

// 增加
// seller_id 商家id, 多个用,号分隔
function addSeller(seller_id){
    if(seller_id == '') return ;
    var data = {};
    data['id'] = seller_id;
    console.log('data', data);
    console.log('AJAX_SELLER_SELECTED_URL', AJAX_SELLER_SELECTED_URL);
    var layer_index = layer.load();
    $.ajax({
        'async': false,// true,//false:同步;true:异步
        'type' : 'POST',
        'url' : AJAX_SELLER_SELECTED_URL,
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
                $('input[name=seller_id]').val(info.id);
                $('input[name=seller_id_history]').val(info.history_id);
                $('.seller_name').html(info.seller_name);
                var now_state = info.now_state;// 最新的 0没有变化 ;1 已经删除  2 试卷不同
                if(now_state == 2 ){
                    $('.update_seller').show();
                }else{
                    $('.update_seller').hide();
                }
            }
            layer.close(layer_index)//手动关闭
        }
    });
}