<?php

namespace App\Http\Controllers\Shop;

use App\Business\Controller\API\RunBuy\CTAPIPropBusiness;
use App\Business\Controller\API\RunBuy\CTAPISellerBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class PropController extends WorksController
{
    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;

        $reDataArr['city_site_id'] =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  $this->seller_id;// CommonRequest::getInt($request, 'seller_id');
        $reDataArr['shop_id'] =  $this->shop_id;// CommonRequest::getInt($request, 'shop_id');

        return view('shop.prop.index', $reDataArr);
    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add(Request $request,$id = 0)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $frm = CommonRequest::getInt($request, 'frm');// 来源 0 列表页 1 商品添加页
        $reDataArr['frm'] = $frm;
        // $seller_id = $this->seller_id;// CommonRequest::getInt($request, 'seller_id');
        $shop_id =  $this->shop_id;// CommonRequest::getInt($request, 'shop_id');
//        if($seller_id <=0 && $shop_id > 0 ) {// 店铺id,转换为商家id
//            $frm = 1;
//            $shopInfo = CTAPIShopBusiness::getInfoData($request, $this, $shop_id);
//            $seller_id = $shopInfo['seller_id'] ?? 0;
//        }

        $info = [
            'id'=>$id,
//            'now_seller_state' => 0,
//            'seller_id' => $seller_id,
            'now_shop_state' => 0,
            'shop_id' => $shop_id,
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPIPropBusiness::getInfoData($request, $this, $id, [], ['propVals.name', 'name', 'shop']);// , 'seller'
        }else{
            if($shop_id > 0 ){
                $partnerInfo = CTAPIShopBusiness::getInfoHistoryId($request, $this, $shop_id, []);
                $info['shop_name'] = $partnerInfo['shop_name'] ?? '';
                $info['shop_id_history'] = $partnerInfo['history_id'] ?? 0;
                $info['now_shop_state'] = $partnerInfo['now_state'] ?? 0;
            }
        }
        $reDataArr['operate'] = $operate;
        $reDataArr['info'] = $info;
        return view('shop.prop.add', $reDataArr);
    }

    /**
     * 选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function select(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;

        $reDataArr['city_site_id'] =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  $this->seller_id;// CommonRequest::getInt($request, 'seller_id');
        $reDataArr['shop_id'] =  $this->shop_id;// CommonRequest::getInt($request, 'shop_id');
        return view('shop.prop.select', $reDataArr);
    }

    /**
     * 选中/更新--单选
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_selected(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        $info = CTAPIPropBusiness::getInfoHistoryId($request, $this, $id, []);
        return ajaxDataArr(1, $info, '');
    }

    /**
     * ajax增加员工数据-根据试卷id,多个,号分隔
     *
     * @param Request $request
     * @return mixed
     * @author liuxin
     */
    public function ajax_selected_multi(Request $request){
        $this->InitParams($request);
        $subjectData = CTAPIPropBusiness::getPropByPropIds($request, $this);
        return ajaxDataArr(1, $subjectData, '');
    }

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
       //  $seller_id = $this->seller_id;// CommonRequest::getInt($request, 'seller_id');
        $shop_id = $this->shop_id;// CommonRequest::getInt($request, 'shop_id');
        // $seller_id_history = CommonRequest::getInt($request, 'seller_id_history');
        $main_name = CommonRequest::get($request, 'main_name');
        $sort_num = CommonRequest::getInt($request, 'sort_num');
        $pv_ids = CommonRequest::get($request, 'pv_ids');
        if(is_string($pv_ids) || is_numeric($pv_ids)){
            $pv_ids = explode(',' ,$pv_ids);
        }
        $pv_names = CommonRequest::get($request, 'pv_names');
        if(is_string($pv_names) || is_numeric($pv_names)){
            $pv_names = explode(',' ,$pv_names);
        }
        foreach($pv_names as $k => $v){
            $pv_names[$k] = trim($v);
        }

        $prop_vals = CommonRequest::get($request, 'prop_vals');
        // $prop_vals =  replace_enter_char($prop_vals,1);
        if(!is_array($prop_vals) && is_string($prop_vals)){// 转为数组
            $prop_vals = explode(PHP_EOL, $prop_vals);
        }

        $propValArr = [];
        foreach($pv_ids as $k => $v){
            if(!is_numeric($v)) continue;
            array_push($propValArr, ['pv_id' => $v, 'pv_val' => $pv_names[$k]]);
        }

        foreach($prop_vals as $k => $v){
            if(!is_numeric($v) && !is_string($v)) continue;
            $v = trim($v);
            if(strlen($v) <= 0 || in_array($v, $pv_names)) {
                unset($prop_vals[$k]);
                continue;
            }
            $prop_vals[$k] = $v;
        }

        if(empty($main_name)){
            return ajaxDataArr(0, null, '属性名称不能为空！');
        }

        if(empty($propValArr)  && empty($prop_vals)){
            return ajaxDataArr(0, null, '属性值不能为空！');
        }

        $saveData = [
            // 'seller_id' => $seller_id,
            'shop_id' => $shop_id,
            'sort_num' => $sort_num,

            'main_name' => $main_name,
            // 'prop_vids' => $propValArr,// 修改已有的 二维数组 [['pv_id' => '属性值id', 'pv_val' => '属性值名称'],....]
            // 'prop_vals' => $prop_vals,// 新加的 ['属性值',....]
        ];
        if(!empty($propValArr)) $saveData['prop_vids'] = $propValArr;
        if(!empty($prop_vals)) $saveData['prop_vals'] = $prop_vals;

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPIPropBusiness::replaceById($request, $this, $saveData, $id, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request){
        $this->InitParams($request);
        return  CTAPIPropBusiness::getList($request, $this, 2 + 4, [], ['propVals.name', 'name', 'city', 'cityPartner', 'seller', 'shop']);
    }

    /**
     * ajax查询属性值id是否有商品正在使用,有在使用的抛出错误（正在使用的商品id）
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_pv_used(Request $request){
        $this->InitParams($request);
        $prop_val_id = CommonRequest::getInt($request, 'prop_val_id');
        $goods = CTAPIPropBusiness::judgePvIdUsed($request, $this, $prop_val_id, false);
        return ajaxDataArr(1, $goods, '');
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_get_ids(Request $request){
        $this->InitParams($request);
        $result = CTAPIPropBusiness::getList($request, $this, 1 + 0);
        $data_list = $result['result']['data_list'] ?? [];
        $ids = implode(',', array_column($data_list, 'id'));
        return ajaxDataArr(1, $ids, '');
    }


    /**
     * 导出
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPIPropBusiness::getList($request, $this, 1 + 0);
//    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPIPropBusiness::importTemplate($request, $this);
//    }


    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_del(Request $request)
    {
        $this->InitParams($request);
        return CTAPIPropBusiness::delAjax($request, $this);
    }

    /**
     * ajax根据部门id,小组id获得所属部门小组下的员工数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_child(Request $request){
//        $this->InitParams($request);
//        $parent_id = CommonRequest::getInt($request, 'parent_id');
//        // 获得一级城市信息一维数组[$k=>$v]
//        $childKV = CTAPIPropBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIPropBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIPropBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import(Request $request)
//    {
//        $this->InitParams($request);
//        // 上传并保存文件
//        $result = Resource::fileSingleUpload($request, $this, 1);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//        $resultDatas = CTAPIPropBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
