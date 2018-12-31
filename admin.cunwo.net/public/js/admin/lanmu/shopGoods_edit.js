
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

const REL_CHANGE = {
    'goodsType':{// 商品分类-二级分类
        'child_sel_name': 'type_id',// 第二级下拉框的name
        'child_sel_txt': {'': "请选择分类" },// 第二级下拉框的{值:请选择文字名称}
        'change_ajax_url': GOODS_TYPE_KV_URL,// 获取下级的ajax地址
        'parent_param_name': 'shop_id',// ajax调用时传递的参数名
        'other_params':{},//其它参数 {'aaa':123,'ccd':'dfasfs'}
    }
};

window.onload = function() {
    var layer_index = layer.load();
    initPic();
    layer.close(layer_index)//手动关闭

};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

$(function(){
    //当前店铺分类
    if(SHOP_ID > 0){
        changeFirstSel(REL_CHANGE.goodsType,SHOP_ID,TYPE_ID, true);
    }


    //店铺值变动
    $(document).on("change",'input[name=shop_id]',function(){
        var seller_id = $('input[name=seller_id]').val();
        console.log('shop_id:change',seller_id);
        var tem_config = REL_CHANGE.goodsType;
        tem_config.other_params = {'seller_id':seller_id};
        changeFirstSel(tem_config, $(this).val(), 0, true);
        return false;
    });

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

    // 判断是否上传图片
    var uploader = $('#myUploader').data('zui.uploader');
    var files = uploader.getFiles();
    var filesCount = files.length;

    var imgObj = $('#myUploader').closest('.resourceBlock').find(".upload_img");

    if( (!judge_list_checked(imgObj,3)) && filesCount <=0 ) {//没有选中的
        layer_alert('请选择要上传的图片！',3,0);
        return false;
    }

    // 所属店铺
    var shop_id = $('input[name=shop_id]').val();
    var judge_seled = judge_validate(1,'所属店铺',shop_id,true,'positive_int','','');
    if(judge_seled != ''){
        layer_alert("请选择所属店铺",3,0);
        return false;
    }

    var type_id = $('select[name=type_id]').val();
    var judge_seled = judge_validate(1,'分类',type_id,true,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择分类",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var goods_name = $('input[name=goods_name]').val();
    if(!judge_validate(4,'商品名称',goods_name,true,'length',1,30)){
        return false;
    }

    var is_hot = $('input[name=is_hot]:checked').val() || '';
    var judge_seled = judge_validate(1,'是否热销',is_hot,true,'positive_int','',"");
    if(judge_seled != ''){
        layer_alert("请选择是否热销",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var is_sale = $('input[name=is_sale]:checked').val() || '';
    var judge_seled = judge_validate(1,'是否上架',is_sale,true,'positive_int','',"");
    if(judge_seled != ''){
        layer_alert("请选择是否上架",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var price = $('input[name=price]').val();
    if(!judge_validate(4,'价格',price,true,'doublepositive','','')){
        return false;
    }

    var sort_num = $('input[name=sort_num]').val();
    if(!judge_validate(4,'排序',sort_num,false,'digit','','')){
        return false;
    }

    var intro = $('textarea[name=intro]').val();
    if(!judge_validate(4,'介绍',intro,false,'length',2,6000)){
        return false;
    }

    // 验证通过
    // 上传图片
    if(filesCount > 0){
        var layer_index = layer.load();
        uploader.start();
        var intervalId = setInterval(function(){
            var status = uploader.getState();
            console.log('获取上传队列状态代码',uploader.getState());
            if(status == 1){
                layer.close(layer_index)//手动关闭
                clearInterval(intervalId);
                ajax_save(id);
            }
        },1000);
    }else{
        ajax_save(id);
    }

}
// 验证通过后，ajax保存
function ajax_save(id){
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
                    var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                    if(id > 0) reset_total = false;
                    parent_reset_list_iframe_close(reset_total);// 刷新并关闭
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
};

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