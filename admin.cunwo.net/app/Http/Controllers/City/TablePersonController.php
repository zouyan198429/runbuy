<?php

namespace App\Http\Controllers\City;

use App\Business\Controller\API\RunBuy\CTAPINumPrefixBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopBusiness;
use App\Business\Controller\API\RunBuy\CTAPITablePersonBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class TablePersonController extends WorksController
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
        // 是否开启
        $reDataArr['isOpen'] =  CTAPITablePersonBusiness::$isOpenArr;
        $reDataArr['defaultIsOpen'] = -1;// 列表页默认状态

        // 排号前缀
        $reDataArr['num_pre_kv'] = CTAPINumPrefixBusiness::getListKV($request, $this);
        $reDataArr['defaultNumPre'] = -1;// 默认

        $reDataArr['city_site_id'] = $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] = $this->city_partner_id;// CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  CommonRequest::getInt($request, 'seller_id');
        $reDataArr['shop_id'] =  CommonRequest::getInt($request, 'shop_id');
        return view('city.tablePerson.index', $reDataArr);
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
//        $reDataArr['province_kv'] = CTAPITablePersonBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPITablePersonBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        return view('city.tablePerson.select', $reDataArr);
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
        $shop_id =  CommonRequest::getInt($request, 'shop_id');
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
            'now_shop_state' => 0,
            'shop_id' => $shop_id,
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPITablePersonBusiness::getInfoData($request, $this, $id, [], ['shop'], []);
        }else{
            if($shop_id > 0 ){
                $partnerInfo = CTAPIShopBusiness::getInfoHistoryId($request, $this, $shop_id, []);
                $info['shop_name'] = $partnerInfo['shop_name'] ?? '';
                $info['shop_id_history'] = $partnerInfo['history_id'] ?? 0;
                $info['now_shop_state'] = $partnerInfo['now_state'] ?? 0;
            }
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        // 是否开启
        $reDataArr['isOpen'] =  CTAPITablePersonBusiness::$isOpenArr;
        $reDataArr['defaultIsOpen'] = $info['is_open'] ?? -1;// 列表页默认状态

        // 排号前缀
        $reDataArr['num_pre_kv'] = CTAPINumPrefixBusiness::getListKV($request, $this);
        $reDataArr['defaultNumPre'] = $info['prefix_id'] ?? -1;// 默认
        return view('city.tablePerson.add', $reDataArr);
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
        $shop_id = CommonRequest::getInt($request, 'shop_id');
        $person_name = CommonRequest::get($request, 'person_name');
        $sort_num = CommonRequest::getInt($request, 'sort_num');
        $is_open = CommonRequest::getInt($request, 'is_open');
        $prefix_id = CommonRequest::getInt($request, 'prefix_id');

        $saveData = [
            'shop_id' => $shop_id,
            'person_name' => $person_name,
            'is_open' => $is_open,
            'prefix_id' => $prefix_id,
            'sort_num' => $sort_num,
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPITablePersonBusiness::replaceById($request, $this, $saveData, $id, true);
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
        return  CTAPITablePersonBusiness::getList($request, $this, 2 + 4, [], ['city', 'cityPartner', 'seller', 'shop' , 'numPrefix']);
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
//        $result = CTAPITablePersonBusiness::getList($request, $this, 1 + 0);
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
//        CTAPITablePersonBusiness::getList($request, $this, 1 + 0);
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
//        CTAPITablePersonBusiness::importTemplate($request, $this);
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
        return CTAPITablePersonBusiness::delAjax($request, $this);
    }

    /**
     * ajax获得商品分类信息;根据店铺id，获得店铺分类信息数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_get_kv(Request $request){
        $this->InitParams($request);
        // 获得一级城市信息一维数组[$k=>$v]
        $listKV = CTAPITablePersonBusiness::getListKV($request, $this);

        return  ajaxDataArr(1, $listKV, '');
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
//        $childKV = CTAPITablePersonBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPITablePersonBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPITablePersonBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPITablePersonBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
