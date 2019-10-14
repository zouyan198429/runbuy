<?php

namespace App\Http\Controllers\Admin;

use App\Business\Controller\API\RunBuy\CTAPICityBusiness;
use App\Business\Controller\API\RunBuy\CTAPINoticeBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class NoticeController extends WorksController
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
        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
        return view('admin.notice.index', $reDataArr);
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
//        $reDataArr['province_kv'] = CTAPINoticeBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPINoticeBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
//        return view('admin.notice.select', $reDataArr);
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
        $city_site_id =  CommonRequest::getInt($request, 'city_site_id');
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
            'now_city_state' => 0,
            'city_site_id' => $city_site_id,
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPINoticeBusiness::getInfoData($request, $this, $id, [], ['city'], []);
        }else{
            if($city_site_id > 0 ){
                $cityInfo = CTAPICityBusiness::getInfoHistoryId($request, $this, $city_site_id, []);
                $info['city_name'] = $cityInfo['city_name'] ?? '';
                $info['city_site_id_history'] = $cityInfo['history_id'] ?? 0;
                $info['now_city_state'] = $cityInfo['now_state'] ?? 0;
            }
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        return view('admin.notice.add', $reDataArr);
    }

    /**
     * 详情
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request, $id = 0)
    {
        $this->InitParams($request);

        $reDataArr = $this->reDataArr;

        // 详情信息
        $infoDatas = [
            'id'=>$id,
        ];

        if ($id > 0) { // 获得详情数据
            $infoDatas =CTAPINoticeBusiness::getInfoData($request, $this, $id, [], ['oprateStaffHistory'], []);
            // 修改点击点
            $id = $infoDatas['id'] ??  0;
            $volume = $infoDatas['volume'] ??  0;
            $saveData = [
                'volume' => $volume + 1,
            ];
            CTAPINoticeBusiness::replaceById($request, $this, $saveData, $id, false);
            $infoDatas['volume'] = $volume + 1;
        }
        // $reDataArr = array_merge($reDataArr, $infoDatas);
        $reDataArr['info'] = $infoDatas;

        // 上一条
        $preList = CTAPINoticeBusiness::getNearList($request, $this, $id, 1, 1, 0, [], '', []);
        $reDataArr['preList'] = $preList;
        // 下一条
        $nextList = CTAPINoticeBusiness::getNearList($request, $this, $id, 2, 1, 0, [], '', []);
        $reDataArr['nextList'] = $nextList;
        return view('manage.notice.info', $reDataArr);
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
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        // $city_site_id_history = CommonRequest::getInt($request, 'city_site_id_history');
        $title = CommonRequest::get($request, 'title');
        $resource = CommonRequest::get($request, 'resource');
        $content = CommonRequest::get($request, 'content');
        $content = stripslashes($content);
        $sort_num = CommonRequest::getInt($request, 'sort_num');

        $saveData = [
            'city_site_id' => $city_site_id,
            'title' => $title,
            'resource' => $resource,
            'content' => $content,
            'sort_num' => $sort_num,
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPINoticeBusiness::replaceById($request, $this, $saveData, $id, true);
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
        return  CTAPINoticeBusiness::getList($request, $this, 2 + 4, [], [ 'city', 'oprateStaffHistory']);
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
//        $result = CTAPINoticeBusiness::getList($request, $this, 1 + 0);
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
//        CTAPINoticeBusiness::getList($request, $this, 1 + 0);
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
//        CTAPINoticeBusiness::importTemplate($request, $this);
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
        return CTAPINoticeBusiness::delAjax($request, $this);
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
//        $childKV = CTAPINoticeBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPINoticeBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPINoticeBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPINoticeBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
