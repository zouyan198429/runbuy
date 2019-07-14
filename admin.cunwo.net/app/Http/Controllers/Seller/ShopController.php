<?php

namespace App\Http\Controllers\Seller;

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
        // 经营状态
        $reDataArr['statusBusiness'] =  CTAPIShopBusiness::$statusBusinessArr;
        $reDataArr['defaultStatusBusiness'] = -1;// 默认状态
        // 店铺分类
        $reDataArr['type_kv'] = CTAPIShopTypeBusiness::getListKV($request, $this);
        $reDataArr['defaultType'] = -1;// 默认
        // 店铺标签
        $reDataArr['labels_kv'] = CTAPILabelsBusiness::getListKV($request, $this);
        $reDataArr['defaultLabel'] = -1;// 默认

        $reDataArr['city_site_id'] =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  $this->seller_id;// CommonRequest::getInt($request, 'seller_id');
        return view('seller.shop.index', $reDataArr);
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
        $seller_id = $this->seller_id;// CommonRequest::getInt($request, 'seller_id');
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
            'now_seller_state' => 0,
            'seller_id' => $seller_id,
            'resource_list' => [],
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPIShopBusiness::getInfoData($request, $this, $id, [], ['shopSeller', 'labels', 'siteResources', 'openTimes']);
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

        // $info['resource_list'] = [];
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
        return view('seller.shop.add', $reDataArr);
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

        // 经营状态
        $reDataArr['statusBusiness'] =  CTAPIShopBusiness::$statusBusinessArr;
        $reDataArr['defaultStatusBusiness'] = -1;// 默认状态

        // 店铺分类
        $reDataArr['type_kv'] = CTAPIShopTypeBusiness::getListKV($request, $this);
        $reDataArr['defaultType'] = -1;// 默认
        // 店铺标签
        $reDataArr['labels_kv'] = CTAPILabelsBusiness::getListKV($request, $this);
        $reDataArr['defaultLabel'] = -1;// 默认

        $reDataArr['city_site_id'] =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  $this->seller_id;// CommonRequest::getInt($request, 'seller_id');
        return view('seller.shop.select', $reDataArr);
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
        $seller_id = $this->seller_id;// CommonRequest::getInt($request, 'seller_id');
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
        $latitude = CommonRequest::get($request, 'latitude');
        $longitude = CommonRequest::get($request, 'longitude');
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

        // 图片资源
        $resource_id = CommonRequest::get($request, 'resource_id');
        if(is_string($resource_id) || is_numeric($resource_id)){
            $resource_id = explode(',' ,$resource_id);
        }

        $resource_ids = implode(',', $resource_id);
        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

//        $range_time = CommonRequest::get($request, 'range_time');

        $saveData = [
            'seller_id' => $seller_id,
            'shop_type_id' => $shop_type_id,
//            'department_id' => $department_id,
//            'group_id' => $group_id,
//            'position_id' => $position_id,
            'shop_name' => $shop_name,
//            'status' => $status,
            'per_price' => $per_price,
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
            'label_ids' => $label_ids,// 标签id串(逗号分隔-未尾逗号结束)
            'labelIds' => $labelIds,// 此下标为标签关系
            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            'resourceIds' => $resource_id,// 此下标为图片资源关系
        ];
        // 经营时间
//        $timeArr = explode('-',$range_time);
//        if(count($timeArr) >= 2){
//            $saveData['open_time'] = trim($timeArr[0]);
//            $saveData['close_time'] = trim($timeArr[1]);
//        }

        $open_time_ids = CommonRequest::get($request, 'open_time_ids');// 店铺营业时间 id
        if(is_string($open_time_ids) || !is_array($open_time_ids)) $open_time_ids = explode(',', $open_time_ids);
        $open_time = CommonRequest::get($request, 'open_time');// 营业开始时间
        if(is_string($open_time) || !is_array($open_time)) $open_time = explode(',', $open_time);
        $close_time = CommonRequest::get($request, 'close_time');// 营业结束时间
        if(is_string($close_time) || !is_array($close_time)) $close_time = explode(',', $close_time);
        $is_open = CommonRequest::get($request, 'is_open');// 是否开启1未开启2已开启
        if(is_string($is_open) || !is_array($is_open)) $is_open = explode(',', $is_open);

        $openTimeList = [];
        $pCount = count($open_time_ids);
        foreach ($open_time_ids as $k => $tId){
            if(!is_numeric($tId)) continue;
            $temOpenTime = [
                'id' => $tId,
                'open_time' => $open_time[$k],
                'close_time' => $close_time[$k],
                'is_open' => $is_open[$k],
                'sort_num' => $pCount--,
            ];
            array_push($openTimeList, $temOpenTime);
        }
        if(!empty($openTimeList)){
            // 营业时间验证
            $resultTime = Tool::timesJudgeDo($openTimeList, '', 2 + 4 + 8 + 64, 1, 'open_time', 'close_time', '营业开始时间', '营业结束时间');
//            if (is_string($resultTime)) {
//                return $resultTime;
//            }
            // 判断时间是否合格
            $saveData['open_time_list'] = $openTimeList;
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
     * ajax保存数据-息业
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_close(Request $request)
    {
        $this->InitParams($request);
        $city_site_id = $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $city_partner_id = $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        $seller_id = $this->seller_id;// CommonRequest::getInt($request, 'seller_id');

        $shop_ids = CommonRequest::getInt($request, 'shop_ids');

        $resultDatas = CTAPIShopBusiness::closeById($request, $this, $city_site_id, $city_partner_id , $seller_id, $shop_ids);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax保存数据-开业
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_open(Request $request)
    {
        $this->InitParams($request);
        $city_site_id = $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $city_partner_id = $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        $seller_id = $this->seller_id;// CommonRequest::getInt($request, 'seller_id');

        $shop_ids = CommonRequest::getInt($request, 'shop_ids');

        $resultDatas = CTAPIShopBusiness::openById($request, $this, $city_site_id, $city_partner_id , $seller_id, $shop_ids);
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
        return  CTAPIShopBusiness::getList($request, $this, 2 + 4, [], ['province', 'city', 'area', 'shopCity', 'shopCityPartner', 'shopSeller', 'shopType', 'labels', 'siteResources', 'openTimes']);
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
