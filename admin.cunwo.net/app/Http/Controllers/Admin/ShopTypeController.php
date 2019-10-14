<?php

namespace App\Http\Controllers\Admin;

use App\Business\Controller\API\RunBuy\CTAPIShopTypeBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class ShopTypeController extends WorksController
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
        // $info = CTAPIShopTypeBusiness::getInfoData($request, $this, 1, [], '');
        // pr($info);
        // 获得第一级省一维数组[$k=>$v]
        // $reDataArr['province_kv'] = CTAPIShopTypeBusiness::getCityByPid($request, $this,  0);
        // $reDataArr['province_kv'] = CTAPIShopTypeBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
        // $reDataArr['province_id'] = 0;
        return view('admin.shopType.index', $reDataArr);
    }

    /**
     * 同事选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function select(Request $request)
//    {
//        $this->InitParams($request);
//        $reDataArr = $this->reDataArr;
//        $reDataArr['province_kv'] = CTAPIShopTypeBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPIShopTypeBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        return view('admin.shopType.select', $reDataArr);
//    }

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
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
            'resource_list' => [],
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPIShopTypeBusiness::getInfoData($request, $this, $id, [], [ 'siteResources'], []);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
//        $reDataArr['province_kv'] = CTAPIShopTypeBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPIShopTypeBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
        return view('admin.shopType.add', $reDataArr);
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
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $type_name = CommonRequest::get($request, 'type_name');
        $sort_num = CommonRequest::getInt($request, 'sort_num');

        // 图片资源
        $resource_id = CommonRequest::get($request, 'resource_id');
        if(is_string($resource_id) || is_numeric($resource_id)){
            if(strlen(trim($resource_id)) > 0){
                $resource_id = explode(',' ,$resource_id);
            }
        }
        if(!is_array($resource_id)) $resource_id = [];

        $resource_ids = implode(',', $resource_id);
        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';


        $saveData = [
            'type_name' => $type_name,
            'sort_num' => $sort_num,
            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            'resourceIds' => $resource_id,// 此下标为图片资源关系
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPIShopTypeBusiness::replaceById($request, $this, $saveData, $id, true);
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
        return  CTAPIShopTypeBusiness::getList($request, $this, 2 + 4, [], ['siteResources']);
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
//        $result = CTAPIShopTypeBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIShopTypeBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIShopTypeBusiness::importTemplate($request, $this);
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
        return CTAPIShopTypeBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIShopTypeBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIShopTypeBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIShopTypeBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIShopTypeBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
