<?php

namespace App\Http\Controllers\Admin;

use App\Business\Controller\API\RunBuy\CTAPICityBusiness;
use App\Business\Controller\API\RunBuy\CTAPILabelsBusiness;
use App\Business\Controller\API\RunBuy\CTAPISellerBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopTypeBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class ShopController extends WorksController
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
        // 省
        $reDataArr['province_kv'] = CTAPICityBusiness::getCityByPid($request, $this,  0);
        $reDataArr['defaultProvince'] = -1;
        // 状态
        $reDataArr['status'] =  CTAPIShopBusiness::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 默认状态
        // 店铺分类
        $reDataArr['type_kv'] = CTAPIShopTypeBusiness::getListKV($request, $this);
        $reDataArr['defaultType'] = -1;// 默认
        // 店铺标签
        $reDataArr['labels_kv'] = CTAPILabelsBusiness::getListKV($request, $this);
        $reDataArr['defaultLabel'] = -1;// 默认

        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  CommonRequest::getInt($request, 'seller_id');
        return view('admin.shop.index', $reDataArr);
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
        $seller_id = CommonRequest::getInt($request, 'seller_id');
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
            'now_seller_state' => 0,
            'seller_id' => $seller_id,
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPIShopBusiness::getInfoData($request, $this, $id, ['shopSeller', 'labels']);
            $intro = $info['intro'] ?? '';
            $info['intro'] = replace_enter_char($intro,2);
            $range_time = '';
            $open_time = $info['open_time'] ?? '';
            $close_time = $info['close_time'] ?? '';
            if(!empty($open_time) && !empty($close_time)) $range_time = $open_time . ' - ' . $close_time;
            $info['range_time'] = $range_time;
        }else{
            if($seller_id > 0 ){
                $partnerInfo = CTAPISellerBusiness::getInfoHistoryId($request, $this, $seller_id, []);
                $info['seller_name'] = $partnerInfo['seller_name'] ?? '';
                $info['seller_id_history'] = $partnerInfo['history_id'] ?? 0;
                $info['now_seller_state'] = $partnerInfo['now_state'] ?? 0;
            }
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        // 状态
        $reDataArr['status'] =  CTAPIShopBusiness::$statusArr;
        $reDataArr['defaultStatus'] = $info['status'] ??  -1;// 默认状态
        // 店铺分类
        $reDataArr['type_kv'] = CTAPIShopTypeBusiness::getListKV($request, $this);
        $reDataArr['defaultType'] = $info['shop_type_id'] ?? -1;// 默认
        // 店铺标签- 多选
        $reDataArr['labels_kv'] = CTAPILabelsBusiness::getListKV($request, $this);
        $reDataArr['defaultLabel'] = $info['labelIds'] ?? [];// 默认选中标签id数组
        // 省
        $reDataArr['province_kv'] = CTAPICityBusiness::getCityByPid($request, $this,  0);
        $reDataArr['defaultProvince'] = $info['province_id'] ??  -1;
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        return view('admin.shop.add', $reDataArr);
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
        // 省
        $reDataArr['province_kv'] = CTAPICityBusiness::getCityByPid($request, $this,  0);
        $reDataArr['defaultProvince'] = -1;
        // 状态
        $reDataArr['status'] =  CTAPIShopBusiness::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 默认状态
        // 店铺分类
        $reDataArr['type_kv'] = CTAPIShopTypeBusiness::getListKV($request, $this);
        $reDataArr['defaultType'] = -1;// 默认
        // 店铺标签
        $reDataArr['labels_kv'] = CTAPILabelsBusiness::getListKV($request, $this);
        $reDataArr['defaultLabel'] = -1;// 默认

        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  CommonRequest::getInt($request, 'seller_id');
        return view('admin.shop.select', $reDataArr);
    }

    /**
     * 选中/更新
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
        $info = CTAPIShopBusiness::getInfoHistoryId($request, $this, $id, []);
        return ajaxDataArr(1, $info, '');

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
        $seller_id = CommonRequest::getInt($request, 'seller_id');
        // $seller_id_history = CommonRequest::getInt($request, 'seller_id_history');
        $shop_type_id = CommonRequest::getInt($request, 'shop_type_id');
        $shop_name = CommonRequest::get($request, 'shop_name');
        $status = CommonRequest::getInt($request, 'status');
        $per_price = CommonRequest::get($request, 'per_price');
        $linkman = CommonRequest::get($request, 'linkman');
        $mobile = CommonRequest::get($request, 'mobile');
        $tel = CommonRequest::get($request, 'tel');
        $province_id = CommonRequest::getInt($request, 'province_id');
        $city_id = CommonRequest::getInt($request, 'city_id');
        $area_id = CommonRequest::getInt($request, 'area_id');
        $addr = CommonRequest::get($request, 'addr');
        $admin_username = CommonRequest::get($request, 'admin_username');
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');
        $intro = CommonRequest::get($request, 'intro');
        $intro =  replace_enter_char($intro,1);

        // 标签
        $labelIds = CommonRequest::get($request, 'label_ids');
        if(!is_array($labelIds) && is_string($labelIds)){// 转为数组
            $labelIds = explode(',',$labelIds);
        }
        $label_ids = implode(',', $labelIds);
        if(!empty($label_ids)) $label_ids = ',' . $label_ids . ',';

        $range_time = CommonRequest::get($request, 'range_time');

        $saveData = [
            'seller_id' => $seller_id,
            'shop_type_id' => $shop_type_id,
//            'department_id' => $department_id,
//            'group_id' => $group_id,
//            'position_id' => $position_id,
            'shop_name' => $shop_name,
            'status' => $status,
            'per_price' => $per_price,
            'linkman' => $linkman,
            'mobile' => $mobile,
            'tel' => $tel,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'area_id' => $area_id,
            'addr' => $addr,
            'intro' => $intro,
            // 'admin_username' => $admin_username,
            'label_ids' => $label_ids,// 标签id串(逗号分隔-未尾逗号结束)
            'labelIds' => $labelIds,// 此下标为标签关系
        ];
        // 经营时间
        $timeArr = explode('-',$range_time);
        if(count($timeArr) >= 2){
            $saveData['open_time'] = trim($timeArr[0]);
            $saveData['close_time'] = trim($timeArr[1]);
        }

        if($id <= 0){
            $saveData['admin_username'] = $admin_username;
            if ($admin_password != $sure_password){
                return ajaxDataArr(0, null, '密码和确定密码不一致！');
            }
            $saveData['admin_password'] = $admin_password;
        }

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPIShopBusiness::replaceById($request, $this, $saveData, $id, true);
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
        return  CTAPIShopBusiness::getList($request, $this, 2 + 4, [], ['province', 'city', 'area', 'shopCity', 'shopCityPartner', 'shopSeller', 'shopType', 'labels']);
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_ids(Request $request){
//        $this->InitParams($request);
//        $result = CTAPIShopBusiness::getList($request, $this, 1 + 0);
//        $data_list = $result['result']['data_list'] ?? [];
//        $ids = implode(',', array_column($data_list, 'id'));
//        return ajaxDataArr(1, $ids, '');
//    }


    /**
     * 导出
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPIShopBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIShopBusiness::importTemplate($request, $this);
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
        return CTAPIShopBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIShopBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIShopBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIShopBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIShopBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
