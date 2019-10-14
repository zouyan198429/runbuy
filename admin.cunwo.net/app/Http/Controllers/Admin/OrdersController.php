<?php

namespace App\Http\Controllers\Admin;

use App\Business\Controller\API\RunBuy\CTAPICityBusiness;
use App\Business\Controller\API\RunBuy\CTAPICountOrdersGrabBusiness;
use App\Business\Controller\API\RunBuy\CTAPIOrdersBusiness;
use App\Business\Controller\API\RunBuy\CTAPIOrdersDoingBusiness;
use App\Business\Controller\API\RunBuy\CTAPIWalletRecordBusiness;
use App\Http\Controllers\WorksController;
use App\Services\DBRelation\RelationDB;
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
        $reDataArr['status'] =  CTAPICityBusiness::$status_arr;
        $reDataArr['defaultStatus'] = 1;// 列表页默认状态
        $reDataArr['countStatus'] = [1,2,4];// 列表页需要统计的状态数组
        $reDataArr['countPlayStatus'] = '2,4';// '2,4';// 需要播放提示声音的状态，多个逗号,分隔

        // 省
        $reDataArr['province_kv'] = CTAPICityBusiness::getCityByPid($request, $this,  0);
        $reDataArr['defaultProvince'] = -1;

        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  CommonRequest::getInt($request, 'seller_id');
        $reDataArr['shop_id'] =  CommonRequest::getInt($request, 'shop_id');
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
            $info = CTAPIOrdersBusiness::getInfoData($request, $this, $id, [], '', []);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        return view('admin.orders.add', $reDataArr);
    }

    /**
     * 打印订单--未完成状态的--不需要登录就能访问
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function print(Request $request,$id = 0)
    {
        // $this->InitParams($request);
        // $this->source = 2;
        $reDataArr = $this->reDataArr;
        $relations = [
            'addrHistory', 'staffHistory', 'partnerHistory', 'sendHistory'
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
        $defaultQueryParams = [
            'where' => [
//                ['company_id', $company_id],
                ['id', $id],
            ],
//            'select' => [
//                'id','company_id','position_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                ,'created_at'
//            ],
            'orderBy' => [ 'id'=>'desc'],// 'sort_num'=>'desc',
            'limit' => 1,
        ];// 查询条件参数
        $result = CTAPIOrdersBusiness::getList($request, $this, 1, $defaultQueryParams, $relations, [], 1);
        $data_list = $result['result']['data_list'] ?? [];
        $parent_orders = $result['result']['parent_orders'] ?? [];
        $childList = [];
        if(!empty($parent_orders)){
            CTAPIOrdersBusiness::mergeRequest($request, $this, [
                'order_type' => 4,// 订单类型1普通订单/父订单4子订单
                'parent_order_no' => implode(',', $parent_orders),
            ]);
            $childResult = CTAPIOrdersBusiness::getList($request, $this, 1, [], $relations, [], 1);
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
//        pr($data_list);
//        $result['result']['data_list'] = $data_list;
//        return $result;
        $reDataArr['webName'] = config('public.webName');// 系统名称
        $reDataArr['orderList'] = $data_list;//订单列表
        return view('admin.orders.print_order_detail', $reDataArr);
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
            'addrHistory', 'staffHistory', 'partnerHistory', 'sendHistory'
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
           // ,['staff_id', '=', $user_id]
        ];//  其它条件[['company_id', '=', $company_id],...]

        $staff_id = CommonRequest::getInt($request, 'staff_id');
        if($staff_id > 0 )  array_push($otherWhere, ['staff_id', '=', $staff_id]);

        $send_staff_id = CommonRequest::getInt($request, 'send_staff_id');
        if($send_staff_id > 0 )  array_push($otherWhere, ['send_staff_id', '=', $send_staff_id]);

        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        if($city_site_id > 0 )  array_push($otherWhere, ['city_site_id', '=', $city_site_id]);

        $city_partner_id = CommonRequest::getInt($request, 'city_partner_id');
        if($city_partner_id > 0 )  array_push($otherWhere, ['city_partner_id', '=', $city_partner_id]);

        $seller_id = CommonRequest::getInt($request, 'seller_id');
        if($seller_id > 0 )  array_push($otherWhere, ['seller_id', '=', $seller_id]);

        $shop_id = CommonRequest::getInt($request, 'shop_id');
        if($shop_id > 0 )  array_push($otherWhere, ['shop_id', '=', $shop_id]);

        $statusCountList = CTAPIOrdersBusiness::getStatusCount($request, $this, $status, $otherWhere, 1);
        return ajaxDataArr(1, $statusCountList, '');
    }

    /**
     * ajax获得统计数据 状态统计
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_status_count(Request $request)
    {
        $this->InitParams($request);
        $user_id = $this->user_id;
        $status = '1,2,4';// CommonRequest::get($request, 'status');// 订单状态,多个用逗号分隔, 可为空：所有的
        $otherWhere = [
            ['order_type', '=', 1]// // 订单类型1普通订单/父订单4子订单
           // ,['staff_id', '=', $user_id]
        ];//  其它条件[['company_id', '=', $company_id],...]

        $staff_id = CommonRequest::getInt($request, 'staff_id');
        if($staff_id > 0 )  array_push($otherWhere, ['staff_id', '=', $staff_id]);

        $send_staff_id = CommonRequest::getInt($request, 'send_staff_id');
        if($send_staff_id > 0 )  array_push($otherWhere, ['send_staff_id', '=', $send_staff_id]);

        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        if($city_site_id > 0 )  array_push($otherWhere, ['city_site_id', '=', $city_site_id]);

        $city_partner_id = CommonRequest::getInt($request, 'city_partner_id');
        if($city_partner_id > 0 )  array_push($otherWhere, ['city_partner_id', '=', $city_partner_id]);

        $seller_id = CommonRequest::getInt($request, 'seller_id');
        if($seller_id > 0 )  array_push($otherWhere, ['seller_id', '=', $seller_id]);

        $shop_id = CommonRequest::getInt($request, 'shop_id');
        if($shop_id > 0 )  array_push($otherWhere, ['shop_id', '=', $shop_id]);

        $statusCountList = CTAPIOrdersBusiness::getStatusCount($request, $this, $status, $otherWhere, 1);
        return ajaxDataArr(1, $statusCountList, '');
    }

    //  退单测试
    // order_no 或 my_order_no 之一不能为空
    // amount 需要退款的金额--不传为0为全退---单位元
    // refund_reason 退款的原因--:为空，则后台自己组织内容
    public function refundOrder(Request $request)
    {
        $this->InitParams($request);
        return CTAPIWalletRecordBusiness::cancelOrder($request, $this, 0);
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

    /**
     * 统计-订单数量
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function countOrders(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $reDataArr['count_types'] =  CTAPICountOrdersGrabBusiness::$countTypeArr;
        $reDataArr['defaultCountType'] = 1;// 列表页默认状态

        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['send_staff_id'] =  CommonRequest::getInt($request, 'send_staff_id');
        $reDataArr['staff_id'] =  CommonRequest::getInt($request, 'staff_id');

        return view('admin.orders.countOrders', $reDataArr);
    }

    /**
     * ajax 获得统计数据
     * @param Request $request
     * @return mixed 这段时间内的待接订单数量
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_count_orders(Request $request)
    {
        $this->InitParams($request);
        // $send_staff_id = $this->user_id;
        $count_type = CommonRequest::get($request, 'count_type');// 统计类型 1 按天统计[当月天的] ; 2 按月统计[当年的]; 3 按年统计
        if(!in_array($count_type, [1,2,3])){
            // return ajaxDataArr(0, null, '请选择统计类型！');
            throws('请选择统计类型！');
        }
        $begin_date = CommonRequest::get($request, 'begin_date');// 开始日期--可为空
        $end_date = CommonRequest::get($request, 'end_date');// 结束日期--可为空
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');// 城市分站id
        $city_partner_id = CommonRequest::getInt($request, 'city_partner_id');// 城市合伙人id
        $send_staff_id = CommonRequest::getInt($request, 'send_staff_id');// 派送用户id
        $staff_id = CommonRequest::getInt($request, 'staff_id');// 下单用户id

        $result = CTAPICountOrdersGrabBusiness::getCounts($request, $this, 0, $count_type, $begin_date, $end_date
            , 'record_num', $city_site_id , $city_partner_id, $send_staff_id, $staff_id, 0);
        return ajaxDataArr(1, $result, '');
    }
}
