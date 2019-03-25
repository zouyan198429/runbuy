<?php

namespace App\Http\Controllers\Admin;

use App\Business\Controller\API\RunBuy\CTAPIOrdersBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class OrdersController extends WorksController
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
        return view('admin.orders.index', $reDataArr);
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
//        $reDataArr['province_kv'] = CTAPIOrdersBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPIOrdersBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        return view('admin.orders.select', $reDataArr);
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
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPIOrdersBusiness::getInfoData($request, $this, $id, [], '');
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        return view('admin.orders.add', $reDataArr);
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
        $ower_id = CommonRequest::getInt($request, 'ower_id');
        $real_name = CommonRequest::get($request, 'real_name');
        $sex = CommonRequest::getInt($request, 'sex');
        $mobile = CommonRequest::get($request, 'mobile');
        $addr_name = CommonRequest::get($request, 'addr_name');
        $addr = CommonRequest::get($request, 'addr');
        $is_default = CommonRequest::getInt($request, 'is_default');
        $latitude = CommonRequest::get($request, 'latitude');
        $longitude = CommonRequest::get($request, 'longitude');

        $saveData = [
            'ower_type' => 64,
            'ower_id' => $ower_id,
            'real_name' => $real_name,
            'sex' => $sex,
            'mobile' => $mobile,
            'addr_name' => $addr_name,
            'addr' => $addr,
            'is_default' => $is_default,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPIOrdersBusiness::replaceById($request, $this, $saveData, $id, true);
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
        $relations = [
            'addrHistory', 'staffHistory', 'partnerHistory'
            ,'provinceHistory','cityHistory','areaHistory'
            , 'sellerHistory', 'shopHistory'
            ,'ordersGoods.goodsHistory'
            ,'ordersGoods.resourcesHistory'
            ,'ordersGoods.goodsPriceHistory.propName'
            ,'ordersGoods.goodsPriceHistory.propValName'
            ,'ordersGoods.props.propName'
            ,'ordersGoods.props.propValName'
        ];
        //  显示到定位点的距离
        CTAPIOrdersBusiness::mergeRequest($request, $this, [
            'order_type' => 1,// 订单类型1普通订单/父订单4子订单
        ]);
        $result = CTAPIOrdersBusiness::getList($request, $this, 2 + 4, [], $relations);
        $data_list = $result['result']['data_list'] ?? [];
        $parent_orders = $result['result']['parent_orders'] ?? [];
        $childList = [];
        if(!empty($parent_orders)){
            CTAPIOrdersBusiness::mergeRequest($request, $this, [
                'order_type' => 4,// 订单类型1普通订单/父订单4子订单
                'parent_order_no' => implode(',', $parent_orders),
            ]);
            $childResult = CTAPIOrdersBusiness::getList($request, $this, 1, [], $relations);
            $childList = $childResult['result']['data_list'] ?? [];
        }
        $formatChildList = [];
        foreach ($childList as $k => $v){
            $formatChildList[$v['parent_order_no']][] = $v;
        }

        foreach($data_list as $k => $v){
            $parent_order_no = $v['order_no'] ?? '';
            $has_son_order = $v['has_son_order'] ?? 0;// 是否有子订单0无1有
            $childOrder = $formatChildList[$parent_order_no] ?? [];
            if($has_son_order == 1 ){// 有子订单
                $data_list[$k]['shopList'] = $childOrder;
            }else{
                $data_list[$k]['shopList'][] = $v;
                if(isset($v['orders_goods'])) unset($data_list[$k]['orders_goods']);
            }
        }

        $data_list = array_values($data_list);
        $result['result']['data_list'] = $data_list;
        return $result;
    }

    /**
     * ajax获得统计数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_getCountByStatus(Request $request)
    {
        $this->InitParams($request);
        $user_id = $this->user_id;
        $status = '1,2,4';// 订单状态,多个用逗号分隔, 可为空：所有的
        $otherWhere = [
            ['order_type', '=', 1]// // 订单类型1普通订单/父订单4子订单
            ,['staff_id', '=', $user_id]
        ];//  其它条件[['company_id', '=', $company_id],...]
        $statusCountList = CTAPIOrdersBusiness::getStatusCount($request, $this, $status, $otherWhere, 1);
        return ajaxDataArr(1, $statusCountList, '');
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
//        $result = CTAPIOrdersBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIOrdersBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIOrdersBusiness::importTemplate($request, $this);
//    }


    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_del(Request $request)
//    {
//        $this->InitParams($request);
//        return CTAPIOrdersBusiness::delAjax($request, $this);
//    }

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
//        $childKV = CTAPIOrdersBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIOrdersBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIOrdersBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIOrdersBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
