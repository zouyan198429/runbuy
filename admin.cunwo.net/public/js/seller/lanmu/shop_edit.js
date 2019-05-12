
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
    //执行一个laydate实例
    // 开始日期
    // layui.laydate.render({
    //     elem: '.range_time' //指定元素
    //     ,type: 'time'
    //     ,range: true
    //     ,value: RANGE_TIME// '2018-08-18' //必须遵循format参数设定的格式
    //    // ,min: get_now_format()//'2017-1-1'
    //     //,max: get_now_format()//'2017-12-31'
    //     // ,calendar: true//是否显示公历节日
    // });

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
    });

    // 上班时间快捷选择
    let openQuickSel = ['8:30:00','9:00:00','10:00:00','14:30:00','15:00:00'];
    // 下班时间快捷选择
    let closeQuickSel = ['13:30:00','14:00:00','15:00:00','18:00:00','22:00:00'];
    // ,quickSel:['8:30:00','9:00:00','10:00:00','18:00:00','22:00:00']
    let pickConfig ={
        skin:'blue',isShowClear:false,readOnly:true,isShowToday:false
        ,dateFmt:'H:mm:ss',qsEnabled:false
        // ,minTime:'09:00:00',maxTime:'17:30:00'
    };
    // 上班时间
    // $(document).on("click",'input[name="open_time_input[]"]',function(){
    $(document).on("click",'#open_time_input',function(){
        let obj = $(this);
        let openConfig = copy(pickConfig,true);// JSON.parse(JSON.stringify(pickConfig));
        openConfig.el = this;
        openConfig.quickSel = openQuickSel;
        openConfig.maxTime = '#F{$dp.$D(\'close_time_input\') || \'23:59:59\'}';
        openConfig.vel = 'open_time_seled';
        openConfig.onpicking = function(dp){
            // if(!confirm('日期框原来的值为: '+dp.cal.getDateStr()+', 要用新选择的值:' + dp.cal.getNewDateStr() + '覆盖吗?'))
            //     return true;
        };
        openConfig.onpicked = function(){
            //  d5222.click();
            $('#close_time_input').trigger("click");// 触发搜索事件
        };
        console.log('-----openConfig--------',openConfig);
        WdatePicker(openConfig);// openConfig
    });
    // 下班时间
    // $(document).on("click",'input[name="close_time_input[]"]',function(){
    $(document).on("click",'#close_time_input',function(){
        let obj = $(this);
        let closeConfig = copy(pickConfig,true);// JSON.parse(JSON.stringify(pickConfig))
        closeConfig.el = this;
        closeConfig.quickSel = closeQuickSel;
        closeConfig.minTime = '#F{$dp.$D(\'open_time_input\') || \'00:01:59\'}';
        closeConfig.vel = 'close_time_seled';
        console.log('-----closeConfig--------',closeConfig);
        WdatePicker(closeConfig);
    });

    // 初始化营业时间列表
    initOpenTime();
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

    // 所属商家
    var seller_id = $('input[name=seller_id]').val();
    var judge_seled = judge_validate(1,'所属商家',seller_id,true,'positive_int','','');
    if(judge_seled != ''){
        layer_alert("请选择所属商家",3,0);
        return false;
    }

    var shop_name = $('input[name=shop_name]').val();
    if(!judge_validate(4,'店铺名称',shop_name,true,'length',1,30)){
        return false;
    }

    var shop_type_id = $('input[name=shop_type_id]:checked').val() || '';
    var judge_seled = judge_validate(1,'分类',shop_type_id,true,'positive_int','',"");
    if(judge_seled != ''){
        layer_alert("请选择分类",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
        return false;
    }

    // 标签
    if(!judge_list_checked('selLabelIds',2)) {//没有选中的
        layer_alert('请选择标签！',3,0);
        return false;
    }

    // var range_time = $('input[name=range_time]').val();
    // var judge_seled = judge_validate(1,'营业时间',range_time,true,'length',8,30);
    // if(judge_seled != ''){
    //     layer_alert("请选择营业时间",3,0);
    //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
    //     return false;
    // }

    // 营业时间判断

    var timeCount = $('.open_time_list').find('tr').length;
    console.log('---timeCount----',timeCount);
    if(timeCount <= 0){
        layer_alert('请添加营业时间！',3,0);
        return false;
    }

    var per_price = $('input[name=per_price]').val();
    if(!judge_validate(4,'人均',per_price,true,'doublepositive','','')){
        return false;
    }

    // var status = $('input[name=status]:checked').val() || '';
    // var judge_seled = judge_validate(1,'状态',status,true,'custom',/^[0124]$/,"");
    // if(judge_seled != ''){
    //     layer_alert("请选择状态",3,0);
    //     //err_alert('<font color="#000000">' + judge_seled + '</font>');
    //     return false;
    // }

    var linkman = $('input[name=linkman]').val();
    if(!judge_validate(4,'联系人',linkman,false,'length',1,30)){
        return false;
    }

    var mobile = $('input[name=mobile]').val();
    // if(!judge_validate(4,'手机',mobile,true,'mobile','','')){
    //     return false;
    // }
    if(!judge_validate(4,'手机',mobile,true,'length',6,30)){
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

    // 纬度
    var latitude = $('input[name=latitude]').val();
    var judge_latitude =  judge_validate(1,'纬度',latitude,true,'double','','');
    // 经度
    var longitude = $('input[name=longitude]').val();
    var judge_longitude =  judge_validate(1,'经度',longitude,true,'double','','');
    if(judge_latitude != '' || judge_longitude != ''){
        layer_alert("请选择经纬度",3,0);
        //err_alert('<font color="#000000">' + judge_seled + '</font>');
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
    selectLatLng: function(obj){// 选择经纬度
        var recordObj = $(obj);
        //获得表单各name的值
        var weburl = SELECT_LATLNG_URL;
        weburl += '?frm=1&lat=' +$('input[name=latitude]').val() + '&lng=' + $('input[name=longitude]').val();
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '选择经纬度';//"查看供应商";
        console.log('weburlLatLng', weburl);
        layeriframe(weburl,tishi,900,450,0);
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
    del : function(obj, parentTag){// 删除
        var recordObj = $(obj);
        var index_query = layer.confirm('确定移除当前记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var trObj = recordObj.closest(parentTag);// 'tr'
            trObj.remove();
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
    propOpenChange : function(obj, parentTag){// 是否开启
        var obj = $(obj);
        var checkboxVal = obj.prop('checked');
        console.log('checkboxVal', checkboxVal);
        var is_open = 1;
        if(checkboxVal)  is_open = 2;
        console.log('is_open', is_open);
        var trObj = obj.closest(parentTag);//获取当前<tr>  'tr'
        trObj.find('input[name="is_open[]"]').val(is_open);
    },
    addTime:function () {// 添加时间
        // 开始时间
        var open_time = $('input[name=open_time_seled]').val();
        console.log('-----open_time----',open_time);
        if(open_time == ''){
            layer_alert("请选择开始时间",3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            return false;
        }
        // 结束时间
        var close_time = $('input[name=close_time_seled]').val();
        console.log('-----close_time----',close_time);
        if(close_time == ''){
            layer_alert("请选择结束时间",3,0);
            //err_alert('<font color="#000000">' + judge_seled + '</font>');
            return false;
        }
        // 比较时间
        var difSecond = compare_time(1, open_time, close_time, "开始时间", "结束时间");
        console.log('-----difSecond----',difSecond);
        if(typeof difSecond == 'string' ){
            layer_alert(difSecond,3,0);
            return false;
        }
        if(difSecond < 0){
            layer_alert("开始时间不能大于结束时间",3,0);
            return false;
        }

        var judge_open_ok = judgeInTimeList(open_time, "开始时间");
        if(!judge_open_ok) return false;

        var judge_close_ok = judgeInTimeList(close_time, "结束时间");
        if(!judge_close_ok) return false;

        // alert('通过验证');
        // 添加新的营业时间
        var data_list = {
            'data_list': [{
                id:0,
                open_time:open_time,
                close_time:close_time,
                is_open:2,
                range_time:open_time + ' - ' + close_time,
            }],
        };
        // 解析数据
        initAnswer('open_time_td', data_list, 2);
        // 清空时间数据
        $('input[name=open_time_input]').val('');
        $('input[name=open_time_seled]').val('');
        $('input[name=close_time_input]').val('');
        $('input[name=close_time_seled]').val('');
    }
};

// 选择经纬度
function latLngSelected(Lat, Lng) {
    $('input[name=latitude]').val(Lat);
    $('input[name=longitude]').val(Lng);
    $('.latlngtxt').html(Lat + ',' + Lng);
}

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

// 初始化营业时间列表
function  initOpenTime() {
    var data_list = {
        'data_list': OPEN_TIMES_LIST,
    };
    initAnswer('open_time_td', data_list, 1);
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
// 判断开始/结束时间，是否在已有营业时间列表内
// judge_time 需要判断的时间
// time_name 判断的时间名称 如：开始时间
// 返回值 true:没有错误, false：有错误
function judgeInTimeList(judge_time, time_name) {
    // 验证开始时间与已有的时间列表是否有冲突
    var hasTimeOK = true;
    $('.open_time_list').find('tr').each(function () {
        var trObj = $(this);

        // 开始时间
        var open_time_val = trObj.find('input[name="open_time[]"]').val();
        console.log('-----open_time_val----',open_time_val);
        // 结束时间
        var close_time_val = trObj.find('input[name="close_time[]"]').val();
        console.log('-----close_time_val----',close_time_val);

        var time_name_tishi = "营业时间[" + open_time_val + "-" + close_time_val + "]";
        // 开始时间判断
        let open_begin_diff = compare_time(1, judge_time, open_time_val, time_name, time_name_tishi + "开始时间");
        console.log('-----open_begin_diff----',open_begin_diff);
        let open_end_diff = compare_time(1, judge_time, close_time_val, time_name, time_name_tishi + "结束时间");
        console.log('-----open_end_diff----',open_end_diff);
        if(typeof open_begin_diff == 'string' ){
            layer_alert(open_begin_diff,3,0);
            hasTimeOK = false;
            return false;
        }
        if(typeof open_end_diff == 'string' ){
            layer_alert(open_end_diff,3,0);
            hasTimeOK = false;
            return false;
        }
        // 在某一段中间
        if( open_begin_diff <= 0 &&  open_end_diff >= 0){
            layer_alert(time_name + "[" + judge_time + "]不能在" + time_name_tishi + "段内",3,0);
            hasTimeOK = false;
            return false;
        }

    });
    return hasTimeOK;
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
    document.write("    <tr>");
    document.write("        <td>");
    document.write("            <label class=\"pos-rel\">");
    document.write("                <input onclick=\"otheraction.seledSingle(this , \'.table2\')\" type=\"checkbox\" class=\"ace check_item\" value=\"<%=item.id%>\">");
    document.write("                <span class=\"lbl\"><\/span>");
    document.write("            <\/label>");
    document.write("            <input type=\"hidden\" name=\"open_time_ids[]\" value=\"<%=item.id%>\"\/>");
    document.write("            <input type=\"hidden\" name=\"open_time[]\" value=\"<%=item.open_time%>\"\/>");
    document.write("           <input type=\"hidden\" name=\"close_time[]\" value=\"<%=item.close_time%>\"\/>");
    document.write("           <input type=\"hidden\" name=\"is_open[]\" value=\"<%=item.is_open%>\"\/>");
    document.write("        <\/td>");
    document.write("        <td><%=item.range_time%><\/td>");
    document.write("        <td><input type=\"checkbox\" class=\"is_open\"  onchange=\"otheraction.propOpenChange(this, 'tr')\"  <%if( item.is_open == 2){%> checked <%}%> value=\"2\"/><\/td>");
    document.write("        <td>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveUp(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-up bigger-60\"> 上移<\/i>");
    document.write("            <\/a>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.moveDown(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-arrow-down bigger-60\"> 下移<\/i>");
    document.write("            <\/a>");
    document.write("            <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.del(this, \'tr\')\">");
    document.write("                <i class=\"ace-icon fa fa-trash-o bigger-60\"> 移除<\/i>");
    document.write("            <\/a>");
    document.write("        <\/td>");
    document.write("    <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();