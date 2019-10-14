<?php

namespace App\Http\Controllers\City;

use App\Business\Controller\API\RunBuy\CTAPICityBusiness;
use App\Business\Controller\API\RunBuy\CTAPICityPartnerBusiness;
use App\Business\Controller\API\RunBuy\CTAPISellerBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class SellerController extends WorksController
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
        // $info = CTAPISellerBusiness::getInfoData($request, $this, 1, [], '');
        // pr($info);
        // 省
        $reDataArr['province_kv'] = CTAPICityBusiness::getCityByPid($request, $this,  0);
        $reDataArr['defaultProvince'] = -1;
        // 状态
        $reDataArr['status'] =  CTAPISellerBusiness::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 默认状态

        $reDataArr['city_site_id'] =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        return view('city.seller.index', $reDataArr);
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
        $city_partner_id = $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
            'now_partner_state' => 0,
            'city_partner_id' => $city_partner_id,
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPISellerBusiness::getInfoData($request, $this, $id, [], ['sellerCityPartner'], []);
            $intro = $info['intro'] ?? '';
            $info['intro'] = replace_enter_char($intro,2);
        }else{
            if($city_partner_id > 0 ){
                $partnerInfo = CTAPICityPartnerBusiness::getInfoHistoryId($request, $this, $city_partner_id, []);
                $info['partner_name'] = $partnerInfo['partner_name'] ?? '';
                $info['city_partner_id_history'] = $partnerInfo['history_id'] ?? 0;
                $info['now_partner_state'] = $partnerInfo['now_state'] ?? 0;
            }
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        // 状态
        $reDataArr['status'] =  CTAPISellerBusiness::$statusArr;
        $reDataArr['defaultStatus'] = $info['status'] ??  -1;// 默认状态
        // 省
        $reDataArr['province_kv'] = CTAPICityBusiness::getCityByPid($request, $this,  0);
        $reDataArr['defaultProvince'] = $info['province_id'] ??  -1;
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
//        $reDataArr['province_kv'] = CTAPISellerBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPISellerBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
        return view('city.seller.add', $reDataArr);
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
        $reDataArr['status'] =  CTAPISellerBusiness::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 默认状态

        $reDataArr['city_site_id'] =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        return view('city.seller.select', $reDataArr);
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
        $info = CTAPISellerBusiness::getInfoHistoryId($request, $this, $id, []);
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
        $city_partner_id = $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        // $city_partner_id_history = CommonRequest::getInt($request, 'city_partner_id_history');
        $seller_name = CommonRequest::get($request, 'seller_name');
        $status = CommonRequest::getInt($request, 'status');
        $linkman = CommonRequest::get($request, 'linkman');
        $mobile = CommonRequest::get($request, 'mobile');
        $tel = CommonRequest::get($request, 'tel');
        $province_id = CommonRequest::getInt($request, 'province_id');
        $city_id = CommonRequest::getInt($request, 'city_id');
        $area_id = CommonRequest::getInt($request, 'area_id');
        $addr = CommonRequest::get($request, 'addr');
        $latitude = CommonRequest::get($request, 'latitude');
        $longitude = CommonRequest::get($request, 'longitude');
        $admin_username = CommonRequest::get($request, 'admin_username');
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');
        $intro = CommonRequest::get($request, 'intro');
        $intro =  replace_enter_char($intro,1);
        $saveData = [
            'city_partner_id' => $city_partner_id,
//            'department_id' => $department_id,
//            'group_id' => $group_id,
//            'position_id' => $position_id,
            'seller_name' => $seller_name,
            'status' => $status,
            'linkman' => $linkman,
            'mobile' => $mobile,
            'tel' => $tel,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'area_id' => $area_id,
            'addr' => $addr,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'intro' => $intro,
            // 'admin_username' => $admin_username,
        ];
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
        $resultDatas = CTAPISellerBusiness::replaceById($request, $this, $saveData, $id, true);
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
        return  CTAPISellerBusiness::getList($request, $this, 2 + 4, [], ['province', 'city', 'area', 'sellerCity', 'sellerCityPartner']);
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
//        $result = CTAPISellerBusiness::getList($request, $this, 1 + 0);
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
//        CTAPISellerBusiness::getList($request, $this, 1 + 0);
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
//        CTAPISellerBusiness::importTemplate($request, $this);
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
        return CTAPISellerBusiness::delAjax($request, $this);
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
//        $childKV = CTAPISellerBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPISellerBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPISellerBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPISellerBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
