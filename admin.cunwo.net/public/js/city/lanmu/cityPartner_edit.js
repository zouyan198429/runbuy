
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
    window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
}
//关闭弹窗,并刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_reset_list_iframe_close(reset_total){
    window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    parent.layer.close(PARENT_LAYER_INDEX);
}
//关闭弹窗
function parent_reset_list(){
    parent.layer.close(PARENT_LAYER_INDEX);
}
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

    // 所属所属城市分站
    var city_site_id = $('input[name=city_site_id]').val();
    var judge_seled = judge_validate(1,'所属城市分站',city_site_id,true,'positive_int','','');
    if(judge_seled != ''){
        layer_alert("请选择所属城市分站",3,0);
        return false;
    }

    var partner_name = $('input[name=partner_name]').val();
    if(!judge_validate(4,'名称',partner_name,true,'length',1,30)){
        return false;
    }

    var status = $('input[name=status]:checked').val() || '';
    var judge_seled = judge_validate(1,'状态',status,true,'custom',/^[0124]$/,"");
    if(judge_seled != ''){
        layer_alert("请选择状态",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var linkman = $('input[name=linkman]').val();
    if(!judge_validate(4,'联系人',linkman,true,'length',1,30)){
        return false;
    }

    var mobile = $('input[name=mobile]').val();
    if(!judge_validate(4,'手机',mobile,true,'mobile','','')){
        return false;
    }

    var tel = $('input[name=tel]').val();
    if(!judge_validate(4,'电话',tel,false,'length',1,20)){
        return false;
    }


    var province_id = $('select[name=province_id]').val();
    var judge_seled = judge_validate(1,'省',province_id,false,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择省",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var city_id = $('select[name=city_id]').val();
    var judge_seled = judge_validate(1,'市',city_id,false,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择市",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var area_id = $('select[name=area_id]').val();
    var judge_seled = judge_validate(1,'区县',area_id,false,'digit','','');
    if(judge_seled != ''){
        layer_alert("请选择区县",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    var addr = $('input[name=addr]').val();
    if(!judge_validate(4,'地址',addr,false,'length',1,60)){
        return false;
    }

    if( id<=0 ){
        var admin_username = $('input[name=admin_username]').val();
        if(!judge_validate(4,'用户名',admin_username,true,'length',6,20)){
            return false;
        }
        var admin_password = $('input[name=admin_password]').val();
        var sure_password = $('input[name=sure_password]').val();

        // var admin_password = $('input[name=admin_password]').val();
        if(!judge_validate(4,'密码',admin_password,true,'length',6,20)){
            return false;
        }

        // var sure_password = $('input[name=sure_password]').val();
        if(!judge_validate(4,'确认密码',sure_password,true,'length',6,20)){
            return false;
        }

        if(admin_password !== sure_password){
            layer_alert('确认密码和密码不一致！',5,0);
            return false;
        }
    }

    var intro = $('textarea[name=intro]').val();
    if(!judge_validate(4,'介绍',intro,false,'length',2,6000)){
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
    selectCity: function(obj){// 选择城市分站
        var recordObj = $(obj);
        //获得表单各name的值
        var weburl = SELECT_CITY_URL;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '选择城市分站';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,900,450,0);
        return false;
    },
    updateCity : function(obj){// 城市更新
        var recordObj = $(obj);
        var index_query = layer.confirm('确定更新当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var city_site_id = $('input[name=city_site_id]').val();
            addCity(city_site_id);
            layer.close(index_query);
        }, function(){
        });
        return false;
    },
};

// 获得选中的城市id 数组
function getSelectedCityIds(){
    var city_ids = [];
    var city_site_id = $('input[name=city_site_id]').val();
    city_ids.push(city_site_id);
    console.log('city_ids' , city_ids);
    return city_ids;
}

// 取消
// city_id 城市id
function removeCity(city_site_id){
    var seled_city_site_id = $('input[name=city_site_id]').val();
    if(city_site_id == seled_city_site_id){
        $('input[name=city_site_id]').val('');
        $('input[name=city_site_id_history]').val('');
        $('.city_name').html('');
        $('.update_city').hide();
    }
}

// 增加
// city_site_id 城市id, 多个用,号分隔
function addCity(city_site_id){
    if(city_site_id == '') return ;
    var data = {};
    data['id'] = city_site_id;
    console.log('data', data);
    console.log('AJAX_CITY_SELECTED_URL', AJAX_CITY_SELECTED_URL);
    var layer_index = layer.load();
    $.ajax({
        'async': false,// true,//false:同步;true:异步
        'type' : 'POST',
        'url' : AJAX_CITY_SELECTED_URL,
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
                $('input[name=city_site_id]').val(info.id);
                $('input[name=city_site_id_history]').val(info.history_id);
                $('.city_name').html(info.city_name);
                var now_state = info.now_state;// 最新的 0没有变化 ;1 已经删除  2 试卷不同
                if(now_state == 2 ){
                    $('.update_city').show();
                }else{
                    $('.update_city').hide();
                }
            }
            layer.close(layer_index)//手动关闭
        }
    });
}

