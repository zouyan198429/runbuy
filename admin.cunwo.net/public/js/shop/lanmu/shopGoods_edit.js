
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
    // window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    let list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
    eval( 'window.parent.' + list_fun_name + '(' + true +', ' + true +', ' + reset_total +', 2)');
}
//关闭弹窗,并刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_reset_list_iframe_close(reset_total){
    // window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    let list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
    eval( 'window.parent.' + list_fun_name + '(' + true +', ' + true +', ' + reset_total +', 2)');
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
    });
    hideShowPrice();// 价格显示/隐藏 if(ID_VAL <= 0)
    initProp(ID_VAL);// 页面初始化属性
});

// 价格显示/隐藏
function hideShowPrice(){
    var price_type = $('input[name=price_type]').val();
    if(price_type == 1){
        $('.price_prop_table').hide();
        $('input[name=price]').show();
    }else{
        $('.price_prop_table').show();
        $('input[name=price]').hide();
    }
}
// 页面初始化属性
// id 商品id
function initProp(id) {
    if(id <= 0) return ;
    var data = {};
    data['good_id'] = id;
    var layer_index = layer.load();
    console.log(data);
    console.log(AJAX_PROP_URL);
    $.ajax({
        'type' : 'POST',
        'url' : AJAX_PROP_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log('ret',ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                var prop_list = ret.result;
                console.log('prop_list', prop_list);
                // 解析数据
                initAnswer('prop_td', prop_list, 1);
                autoCountPropNum();
                // 处理价格属性
                var propData = prop_list['data_list'];
                console.log('propData', propData);
                // 遍历
                for (var i = 0; i < propData.length; i++) {
                  var itemProp = propData[i];
                  if(itemProp.is_price == 1 || itemProp.is_price == "1"){
                      // 数据模板，显示数据
                      var data_list = {
                          'data_list': itemProp.pv_list,
                      };
                      console.log('pv_list', itemProp.pv_list);
                      // 解析数据
                      initPriceProp('price_prop_table', data_list, 2);
                      break;
                  }

                }
            }
            layer.close(layer_index)//手动关闭
        }
    });
}

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

    var price_type = $('input[name=price_type]').val();
    var judge_seled = judge_validate(1,'价格类型',price_type,true,'custom',/^[12]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择价格类型",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    // 判断属性是否有勾选
    var seletedProp = true;
    $('.prop_list').find('tr').each(function () {
        var trObj = $(this);
        var  propName = trObj.data('prop_name');
        if(!judge_list_checked(trObj.find('.pv_selected'),3)) {//没有选中的
            seletedProp = false;
            layer_alert('请选择[' + propName + ']的属性值！',3,0);
            return false;
        }
    });
    if(!seletedProp) return false;

    var price = $('input[name=price]').val();
    if(price_type == 1 && !judge_validate(4,'价格',price,true,'doublepositive','','')){
        return false;
    }

    // 判断属性价格
    var hasPropPrice = true;
    $('.data_list_price').find('tr').each(function () {
        var trObj = $(this);
        var  propName = trObj.data('prop_name');

        var price_pv_val = trObj.find('input[name="price_pv_val[]"]').val();
        if(price_type == 2 && !judge_validate(4,'属性值[' + propName + ']价格',price_pv_val,true,'doublepositive','','')){
            hasPropPrice = false;
            return false;
        }
    });
    if(!hasPropPrice) return false;

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
    selectProp: function(obj){// 选择属性
        var recordObj = $(obj);
        // 所属店铺
        var shop_id = $('input[name=shop_id]').val();
        var judge_seled = judge_validate(1,'所属店铺',shop_id,true,'positive_int','','');
        if(judge_seled != ''){
            layer_alert("请选择所属店铺",3,0);
            return false;
        }
        //获得表单各name的值
        var weburl = SELECT_PROP_URL + '?shop_id=' + shop_id;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '选择属性';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,900,450,0);
        return false;
    },
    addSelectProp: function(obj){// 新加属性
        var recordObj = $(obj);
        // 所属店铺
        var shop_id = $('input[name=shop_id]').val();
        var judge_seled = judge_validate(1,'所属店铺',shop_id,true,'positive_int','','');
        if(judge_seled != ''){
            layer_alert("请选择所属店铺",3,0);
            return false;
        }
        //获得表单各name的值
        var weburl = ADD_PROP_URL + '?frm=1&shop_id=' + shop_id;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '新加属性';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,900,450,0);
        return false;
    },
    edit : function(obj, parentTag, prop_id){// 更新属性
        var recordObj = $(obj);
        var index_query = layer.confirm('确定更新当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var trObj = recordObj.closest(parentTag);// 'tr'
            updateProp(prop_id,trObj);
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    del : function(obj, parentTag){// 删除
        var recordObj = $(obj);
        var index_query = layer.confirm('确定移除当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var trObj = recordObj.closest(parentTag);// 'tr'
            trObj.remove();
            autoCountPropNum();
            goodsPrice();// 处理价格
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
    batchDel:function(obj, parentTag, delTag) {// 批量删除
        var recordObj = $(obj);
        var index_query = layer.confirm('确定移除选中记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var hasDel = false;
            recordObj.closest(parentTag).find('.check_item').each(function () {
                if (!$(this).prop('disabled') && $(this).val() != '' &&  $(this).prop('checked') ) {
                    // $(this).prop('checked', checkAllObj.prop('checked'));
                    var trObj = $(this).closest(delTag);// 'tr'
                    trObj.remove();
                    hasDel = true;
                }
            });
            if(!hasDel){
                err_alert('请选择需要操作的数据');
            }
            autoCountPropNum();
            goodsPrice();// 处理价格
            layer.close(index_query);
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
    seledAll:function(obj, parentTag){
        var checkAllObj =  $(obj);
        /*
        checkAllObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function(){
            if(!$(this).prop('disabled')){
                $(this).prop('checked', checkAllObj.prop('checked'));
            }
        });
        */
        checkAllObj.closest(parentTag).find('.check_item').each(function(){
            if(!$(this).prop('disabled')){
                $(this).prop('checked', checkAllObj.prop('checked'));
            }
        });
    },
    seledSingle:function(obj, parentTag) {// 单选点击
        var checkObj = $(obj);
        var allChecked = true;
        /*
         checkObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function () {
            if (!$(this).prop('disabled') && $(this).val() != '' &&  !$(this).prop('checked') ) {
                // $(this).prop('checked', checkAllObj.prop('checked'));
                allChecked = false;
                return false;
            }
        });
        */
        checkObj.closest(parentTag).find('.check_item').each(function () {
            if (!$(this).prop('disabled') && $(this).val() != '' &&  !$(this).prop('checked') ) {
                // $(this).prop('checked', checkAllObj.prop('checked'));
                allChecked = false;
                return false;
            }
        });
        // 全选复选操选中/取消选中
        /*
        checkObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function () {
            if (!$(this).prop('disabled') && $(this).val() == ''  ) {
                $(this).prop('checked', allChecked);
                return false;
            }
        });
        */
        checkObj.closest(parentTag).find('.check_all').each(function () {
            $(this).prop('checked', allChecked);
        });

    },
    propMustChange : function(obj, parentTag){// 是否必填
        var obj = $(obj);
        var checkboxVal = obj.prop('checked');
        console.log('checkboxVal', checkboxVal);
        var is_musts = 0;
        if(checkboxVal)  is_musts = 1;
        console.log('is_musts', is_musts);
        var trObj = obj.closest(parentTag);//获取当前<tr>  'tr'
        trObj.find('input[name="is_musts[]"]').val(is_musts);
    },
    propMultiChange : function(obj, parentTag){// 是否多选
        var obj = $(obj);
        var checkboxVal = obj.prop('checked');
        console.log('checkboxVal', checkboxVal);
        var is_multis = 0;
        if(checkboxVal)  is_multis = 1;
        console.log('is_multis', is_multis);
        var trObj = obj.closest(parentTag);//获取当前<tr>  'tr'
        trObj.find('input[name="is_multis[]"]').val(is_multis);
    },
    propPVChange : function(obj, parentTag){// 选择属性值
        var obj = $(obj);
        var names = obj.data('names');
        console.log('names', names);
        // var checkboxVal = obj.prop('checked');
        // console.log('checkboxVal', checkboxVal);
        // var is_multis = 0;
        // if(checkboxVal)  is_multis = 1;
        // console.log('is_multis', is_multis);
        var trObj = obj.closest(parentTag);//获取当前<tr>  'tr'
        // trObj.find('input[name="is_multis[]"]').val(is_multis);
        var pvObj = trObj.find('.pv_selected');
        var pvIds = get_list_checked(pvObj,3,1);
        console.log('pvIds',pvIds);
        trObj.find('input[name="pv_ids[]"]').val(pvIds);
        goodsPrice();// 处理价格
    },
    propPriceChange : function(obj, parentTag){// 选择价格属性
        var obj = $(obj);
        goodsPrice();// 处理价格
    },
};

// 处理价格
function goodsPrice(){
    var has_prop_price = 1;
    var price_obj = null;
    // 遍历清除所有的是否价格属性标
    $('.prop_list').find('tr').each(function () {
        var trObj = $(this);
        var is_price = 0;
        var priceObj = trObj.find('.price_prop_id');
        if(priceObj.prop('checked')) is_price = 1;
        trObj.find('input[name="is_prices[]"]').val(is_price)
    });

    // 遍历是否价格属性
    $('.prop_list').find('.price_prop_id').each(function () {
        var radioObj = $(this);
        var propVal = radioObj.val();
        console.log('propVal' , propVal);
        var seleced = radioObj.prop('checked');
        if(seleced){
            has_prop_price = 2;
            price_obj = radioObj;
            return false;//实现break功能
        }
        // if(!seleced) return ;//实现continue功能
    });
    $('input[name=price_type]').val(has_prop_price);
    if(has_prop_price == 1) {
        $('data_list_price').html('');
    }else{
        var price_pv_data = [];
        var check_price_pv_ids = [];// 选中的属性值数组
        price_obj.closest('tr').find('.price_prop_val').each(function () {
            var temObj = $(this);
            if(!temObj.prop('checked')) return ;//实现continue功能
            var pv_id = temObj.val();
            check_price_pv_ids.push(pv_id);
            if($('.price_prop_val_' + pv_id).length > 0) return ;//实现continue功能  已经存在价格
            var pv_obj = {'id' : pv_id, 'main_name' : temObj.data('names'), 'price' : '', 'selected' : 1};
            price_pv_data.push(pv_obj);
        });
        console.log('check_price_pv_ids' , check_price_pv_ids);
        console.log('price_pv_data' , price_pv_data);
        // 移除其它已经存在的非选中的数据
        $('.data_list_price').find('tr').each(function () {
            var temObj = $(this);
            var pv_id = temObj.data('prop_id');
            console.log('pv_id', pv_id);
            if($.inArray(pv_id + '', check_price_pv_ids) <= -1) {
                console.log('移除对象', pv_id);
                temObj.remove();
            }
        });

        // 数据模板，显示数据
        var data_list = {
            'data_list': price_pv_data,
        };
        // 解析数据
        initPriceProp('price_prop_table', data_list, 2);
    }
    hideShowPrice();// 价格显示/隐藏
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


// 更新属性
// id 属性id
// trObj tr对象
function updateProp(id, trObj) {
    if(id <= 0) return ;
    var data = {};
    data['id'] = id;
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : AJAX_UPDATE_PROP_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log('ret',ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                var prop_list = ret.result;
                console.log('prop_list', prop_list);
                var data_list = {
                    'data_list': prop_list,
                };
                // 解析数据
                var htmlStr = initAnswer('', data_list, 3);
                trObj.after(htmlStr);
                trObj.remove();
                autoCountStaffNum();
            }
            layer.close(layer_index)//手动关闭
        }
    });
}


// 初始化答案列表
// data_list 数据对象 {'data_list':[{}]}
// type类型 1 全替换 2 追加到后面 3 返回html
function initAnswer(class_name, data_list, type){
    var htmlStr = resolve_baidu_template(DYNAMIC_BAIDU_TEMPLATE,data_list,'');//解析
    if(type == 3) return htmlStr;
    //alert(htmlStr);
    //alert(body_data_id);
    if(type == 1){
        $('.'+ class_name).find('.' + DYNAMIC_TABLE_BODY).html(htmlStr);
    }else if(type == 2){
        $('.'+ class_name).find('.' + DYNAMIC_TABLE_BODY).append(htmlStr);
    }
}

// 初始化价格属性列表
// data_list 数据对象 {'data_list':[{}]}
// type类型 1 全替换 2 追加到后面 3 返回html
function initPriceProp(class_name, data_list, type){
    var htmlStr = resolve_baidu_template(DYNAMIC_PRICE_BAIDU_TEMPLATE,data_list,'');//解析
    if(type == 3) return htmlStr;
    //alert(htmlStr);
    //alert(body_data_id);
    if(type == 1){
        $('.'+ class_name).find('.' + DYNAMIC_PRICE_TABLE_BODY).html(htmlStr);
    }else if(type == 2){
        $('.'+ class_name).find('.' + DYNAMIC_PRICE_TABLE_BODY).append(htmlStr);
    }
}

// 获得参考人员数量
function autoCountPropNum(){
    var total = 0;
    $('.prop_td').each(function () {
        var departmentObj = $(this);
        var prop_num = departmentObj.find('.data_list').find('tr').length;
        console.log('prop_num',prop_num);
        // departmentObj.find('input[name="prop_num[]"]').val(prop_num);
        // departmentObj.find('.prop_num').html(prop_num);
        total += parseInt(prop_num);
    });
    $('.prop_num').html(total);

}

// 获得属性id 数组
function getSelectedPropIds(){
    var prop_ids = [];
    $('.prop_td').find('.data_list').find('input[name="prop_ids[]"]').each(function () {
        var prop_id = $(this).val();
        prop_ids.push(prop_id);
    });
    console.log('prop_ids' , prop_ids);
    return prop_ids;
}

// 取消
// prop_id 属性id
function removeProp(prop_id){
    $('.prop_td').find('.data_list').find('input[name="prop_ids[]"]').each(function () {

        var tem_prop_id = $(this).val();
        if(prop_id == tem_prop_id){
            $(this).closest('tr').remove();
            return ;
        }
    });
    autoCountPropNum();
}

// 增加
// prop_id 属性id, 多个用,号分隔
function addProp( prop_id){
    console.log('addProp', prop_id);
    if(prop_id == '') return ;
    // 去掉已经存在的记录id
    var selected_ids = getSelectedPropIds();
    var prop_id_arr = prop_id.split(",");
    console.log('prop_id_arr', prop_id_arr);
    //差集
    var diff_arr = prop_id_arr.filter(function(v){ return selected_ids.indexOf(v) == -1 });
    prop_id = diff_arr.join(',');
    if(prop_id == '') return ;

    var data = {};
    data['id'] = prop_id;
    var layer_index = layer.load();
    console.log('AJAX_PROP_SELECTED_MULTI_URL',AJAX_PROP_SELECTED_MULTI_URL);
    console.log('data', data);
    $.ajax({
        'async': false,// true,//false:同步;true:异步
        'type' : 'POST',
        'url' : AJAX_PROP_SELECTED_MULTI_URL,
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log('ret',ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                var prop_list = ret.result;
                console.log('prop_list', prop_list);
                var data_list = {
                    'data_list': prop_list,
                };
                // 解析数据
                initAnswer('prop_td', data_list, 2);
                autoCountPropNum();
                // 处理选默认选择中
                for(var k = 0; k < prop_list.length; k++) {
                    var propItem = prop_list[k];
                    var trObj = $('#prop_tr_' + propItem.id);
                    var pvObj = trObj.find('.pv_selected');
                    var pvIds = get_list_checked(pvObj,3,1);
                    console.log('pvIds',pvIds);
                    trObj.find('input[name="pv_ids[]"]').val(pvIds);
                }
            }
            layer.close(layer_index)//手动关闭
        }
    });
}


(function() {
    document.write("<!-- 前端模板部分 -->");
    document.write("<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("    <%for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    var pv_list = item.pv_list;");
    document.write("    var now_prop = item.now_prop;");
    document.write("    can_modify = true;");
    document.write("    %>");
    document.write("    <tr  data-prop_name=\"<%=item.main_name%>\"  id=\"prop_tr_<%=item.id%>\">");
    document.write("        <td>");
    document.write("            <label class=\"pos-rel\">");
    document.write("                <input onclick=\"otheraction.seledSingle(this , \'.table2\')\" type=\"checkbox\" class=\"ace check_item\" value=\"<%=item.id%>\">");
    document.write("                <span class=\"lbl\"><\/span>");
    document.write("            <\/label>");
    document.write("            <input type=\"hidden\" name=\"prop_ids[]\" value=\"<%=item.id%>\"\/>");
    document.write("            <input type=\"hidden\" name=\"prop_id_historys[]\" value=\"<%=item.id_history%>\"\/>");
    document.write("            <input type=\"hidden\" name=\"is_prices[]\" value=\"<%=item.is_price%>\"\/>");
   document.write("            <input type=\"hidden\" name=\"is_multis[]\" value=\"<%=item.is_multi%>\"\/>");
   document.write("            <input type=\"hidden\" name=\"is_musts[]\" value=\"<%=item.is_must%>\"\/>");
    document.write("           <input type=\"hidden\" name=\"pv_ids[]\" value=\"<%=item.pv_ids%>\"\/>");
    document.write("        <\/td>");
    document.write("        <td><%=item.main_name%><\/td>");
    document.write("           <td  class=\"pv_selected\">");
    document.write("            <%for(var j = 0; j<pv_list.length;j++){");
    document.write("                var jitem = pv_list[j];");
    document.write("                 %>");
    document.write("                 <label><input type=\"checkbox\" class=\"price_prop_val\"  data-names=\"<%=jitem.main_name%>\" onchange=\"otheraction.propPVChange(this, 'tr')\"   <%if( jitem.selected == 1){%> checked <%}%> value=\"<%=jitem.id%>\"/><%=jitem.main_name%><\/label> ");
    document.write("              </a>");
    document.write("            <%}%>");
    document.write("           <\/td>");
    document.write("        <td><input type=\"radio\" class=\"price_prop_id\" name=\"price_prop_id\" onchange=\"otheraction.propPriceChange(this, 'tr')\" <%if( item.is_price == 1){%> checked <%}%>  value=\"<%=item.id%>\"\/><\/td>");
    document.write("        <td><input type=\"checkbox\" class=\"is_musts\" onchange=\"otheraction.propMustChange(this, 'tr')\"  <%if( item.is_must == 1){%> checked <%}%> value=\"1\"\/><\/td>");
    document.write("        <td><input type=\"checkbox\" class=\"is_multis\" onchange=\"otheraction.propMultiChange(this, 'tr')\"   <%if( item.is_multi == 1){%> checked <%}%> value=\"1\"\/><\/td>");
    document.write("        <td>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveUp(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-up bigger-60\"> 上移<\/i>");
    document.write("            <\/a>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveDown(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-down bigger-60\"> 下移<\/i>");
    document.write("            <\/a>");
    document.write("            <%if( now_prop == 2 || now_prop == 4){%>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.edit(this, \'tr\', <%=item.id%>)\">");
    document.write("                <i class=\"ace-icon fa fa-pencil bigger-60 pink\"> 更新[属性已更新]<\/i>");
    document.write("            <\/a>");
    document.write("            <%}%>");
    document.write("            <%if( now_prop == 1){%>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.del(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-trash-o bigger-60 wrong\"> 删除[属性已删]<\/i>");
    document.write("            <\/a>");
    document.write("            <%}%>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.del(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-trash-o bigger-60\"> 移除<\/i>");
    document.write("            <\/a>");
    document.write("        <\/td>");
    document.write("    <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<script type=\"text\/template\"  id=\"baidu_template_price_data_list\">");
    document.write("    <%for(var i = 0; i<data_list.length;i++){");
    document.write("    var item = data_list[i];");
    document.write("    if(item.selected != 1) continue;");
    document.write("    %>");
    document.write("    <tr data-prop_id=\"<%=item.id%>\"   data-prop_name=\"<%=item.main_name%>\" class=\"price_prop_val_<%=item.id%>\">");
    document.write("        <td>");
    document.write("            <input type=\"hidden\" name=\"price_pv_id[]\" value=\"<%=item.id%>\"\/>");
    document.write("            <%=item.main_name%>");
    document.write("        <\/td>");
    document.write("        <td><input type=\"text\" name=\"price_pv_val[]\"  value=\"<%=item.price%>\" placeholder=\"请输入价格\"  onkeyup=\"numxs(this) \" onafterpaste=\"numxs(this)\" \/><\/td>");
    document.write("        <td>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveUp(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-up bigger-60\"> 上移<\/i>");
    document.write("            <\/a>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveDown(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-down bigger-60\"> 下移<\/i>");
    document.write("            <\/a>");
    document.write("        <\/td>");
    document.write("    <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
