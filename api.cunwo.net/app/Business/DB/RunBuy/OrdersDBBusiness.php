<?php
// 订单
namespace App\Business\DB\RunBuy;

use App\Models\RunBuy\Orders;
use App\Models\RunBuy\OrdersDoing;
use App\Services\Tool;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class OrdersDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Orders';
    public static $table_name = 'orders';// 表名称


    /**
     * 根据订单号，更新订单信息
     *
     * @param array $saveDate 需要更新的数据
     * @param string  $order_no 订单号
     * @param string  $update_type 更新类型 1正在进行的订单2历史订单4 子订单
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_history_id 操作员工历史id
     * @param string $logContent 操作说明
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function updateOrders($saveDate,  $order_no, $update_type = 0, $operate_staff_id = 0 , $operate_staff_id_history = 0, $logContent = '')
    {
        if(empty($saveDate)) throws('订单更新参数不能为空!');

        // 更新子订单条件
        $saveQueryParams = [
            'where' => [
                ['order_type', 4],
                // ['staff_id', $operate_staff_id],
                ['parent_order_no', $order_no],
            ],
            /*
             *
            'select' => [
                'id','title','sort_num','volume'
                ,'operate_staff_id','operate_staff_id_history'
                ,'created_at' ,'updated_at'
            ],
             *
             */
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];

        $queryParams = [
            'where' => [
                ['order_type', 1],
                // ['staff_id', $operate_staff_id],
                ['order_no',$order_no],
            ],
            // 'select' => ['id', 'status', 'pay_run_price', 'has_refund', 'total_run_price' ]
        ];

        DB::beginTransaction();
        try {
            if(($update_type & 1) == 1) {// 1正在进行的订单
                // 获得订单详情
                $orderInfo = OrdersDoingDBBusiness::getInfoByQuery(1, $queryParams, []);
                if(empty($orderInfo)) throws('订单信息不存在!');
                foreach($saveDate as $k => $v){
                    $orderInfo->{$k} = $v;
                }
                $orderInfo->save();
                OrdersRecordDoingDBBusiness::saveOrderLog($orderInfo , $operate_staff_id , $operate_staff_id_history, $logContent);
                if(($update_type & 4) == 4) {// 4 子订单
                    OrdersDoingDBBusiness::save($saveDate, $saveQueryParams);
                    $childOrders = OrdersDoingDBBusiness::getAllList($saveQueryParams,[]);
                    foreach($childOrders as $childOrderObj){
                        OrdersRecordDoingDBBusiness::saveOrderLog($childOrderObj , $operate_staff_id , $operate_staff_id_history, $logContent);
                    }
                }
            }

            if(($update_type & 2) ==2) {// 2历史订单
                // 获得订单详情
                $orderInfo = OrdersDBBusiness::getInfoByQuery(1, $queryParams, []);
                if(empty($orderInfo)) throws('订单信息不存在!');
                foreach($saveDate as $k => $v){
                    $orderInfo->{$k} = $v;
                }
                $orderInfo->save();
                OrdersRecordDBBusiness::saveOrderLog($orderInfo , $operate_staff_id , $operate_staff_id_history, $logContent);
                if(($update_type & 4) == 4) {// 4 子订单
                    OrdersDBBusiness::save($saveDate, $saveQueryParams);
                    $childOrders = OrdersDBBusiness::getAllList($saveQueryParams,[]);
                    foreach($childOrders as $childOrderObj){
                        OrdersRecordDBBusiness::saveOrderLog($childOrderObj , $operate_staff_id , $operate_staff_id_history, $logContent);
                    }
                }
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return true;
    }

    /**
     * 按状态分组统计订单数量 -- 只处理状态 状态1待支付2等待接单4取货或配送中[从进行表]   8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]-- 从历史表
     *
     * @param string $status 订单状态,多个用逗号分隔, 可为空：所有的
     * @param int  $company_id 企业id
     * @param array $otherWhere 其它条件[['company_id', '=', $company_id],...]
     * @param int $operate_staff_id 操作人id
     * @return  array 状态统计数组 --  一维
     * @author zouyan(305463219@qq.com)
     */
    public static function getGroupCount($status,  $company_id, $otherWhere = [], $operate_staff_id = 0){

        $where = [
            // ['company_id', '=', $company_id],
            // ['send_staff_id', '=', $staff_id],
            // ['status', '=', $status],
        ];
        if(!empty($otherWhere)){
            $where = array_merge($where, $otherWhere);
        }

        // 如果有状态 待接单，则把退款中的也去掉 2等待接单
        if(strpos(',' . $status . ',', ',2,') !== false){
            array_push($where, ['has_refund', '!=', 2]); // 是否退费0未退费1已退费2待退费
            array_push($where, ['refund_price_frozen', '<=', 0]);
        }

        // 数字或单条
        $statusArr = [];
        if( is_numeric($status) ||  (is_string($status) && strpos($status, ',') === false) ){
            if($status != '') array_push($where,['status', '=', $status]);
            if($status != '') array_push($statusArr, $status);
        }else{// 其它的转为数组
            if(is_string($status)) $status = explode(',', $status);
            if(!is_array($status)) $status = [];
            $statusArr = $status;
        }
        // if(empty($status)) throws('参数[status]不能为空');

        // 8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]-- 从历史表
        if(empty($statusArr) || in_array(8, $statusArr) || in_array(16, $statusArr) || in_array(32, $statusArr) || in_array(64, $statusArr)){
            $orderObj = Orders::where($where);
        } else {
            $orderObj = OrdersDoing::where($where);
        }
        // 是数组
        if(!empty($status) && is_array($status)){
            $orderObj = $orderObj->whereIn('status',$status);
        }
        $dataList = $orderObj->select(DB::raw('count(*) as status_count, status'))
            ->groupBy('status')
            ->get();

        $requestData = [];
        foreach($dataList as $info){
            $requestData[$info['status']] = $info['status_count'];
        }
        foreach ($statusArr as $temStatus){
            if(isset($requestData[$temStatus])){
                continue;
            }
            $requestData[$temStatus] = 0;
        }
        return $requestData;
    }


    /**
     * 按状态统计工单数量
     *
     * @param int $company_id 公司id
     * @param string $status 订单状态,多个用逗号分隔, 可为空：所有的
     * @param array $otherWhere 其它条件[['company_id', '=', $company_id],...]
     * @param int $operate_staff_id 添加员工id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public static function getCount($company_id, $status = '', $otherWhere = [], $operate_staff_id = 0){
        $where = [
            // ['company_id', '=', $company_id],
            // ['send_staff_id', '=', $staff_id],
            // ['status', '=', $status],
        ];
        if(!empty($otherWhere)){
            $where = array_merge($where, $otherWhere);
        }

        // 如果有状态 待接单，则把退款中的也去掉 2等待接单
        if(strpos(',' . $status . ',', ',2,') !== false){
            array_push($where, ['has_refund', '!=', 2]); // 是否退费0未退费1已退费2待退费
        }

        // 数字或单条
        $statusArr = [];
        if( is_numeric($status) ||  (is_string($status) && strpos($status, ',') === false) ){
            if($status != '') array_push($where,['status', '=', $status]);
            if($status != '') array_push($statusArr, $status);
        }else{// 其它的转为数组
            if(is_string($status)) $status = explode(',', $status);
            if(!is_array($status)) $status = [];
            $statusArr = $status;
        }
        // if(empty($status)) throws('参数[status]不能为空');

        // 8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]-- 从历史表
        if(empty($statusArr) || in_array(8, $statusArr) || in_array(16, $statusArr) || in_array(32, $statusArr) || in_array(64, $statusArr)){
            $orderObj = Orders::where($where);
        } else {
            $orderObj = OrdersDoing::where($where);
        }
        // 是数组
        if(!empty($status) && is_array($status)){
            $orderObj = $orderObj->whereIn('status',$status);
        }
        $dataCount = $orderObj->count();

        return $dataCount;
    }

    /**
     * 根据订单号，删除正在进行的订单数据
     *
     * @param string  $order_no 订单号, 多个用逗号,分隔--如果多店铺--父订单号
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_history_id 操作员工历史id
     * @param string $logContent 操作说明
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function delDoingOrders( $order_no, $operate_staff_id = 0 , $operate_staff_id_history = 0, $logContent = '')
    {
        DB::beginTransaction();
        try {
            // 获得订单信息
            $queryParams = [
                'where' => [
                    ['order_type', 1],// 订单类型1普通订单/父订单4子订单
                    // ['order_no', $order_no],
                    // ['pay_run_price', 1],// 是否支付跑腿费0未支付1已支付
                ],
                'select' => ['id', 'has_son_order', 'order_no']
                /*
                'select' => [
                    'id','title','sort_num','volume'
                    ,'operate_staff_id','operate_staff_id_history'
                    ,'created_at' ,'updated_at'
                ],
                */
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            if (strpos($order_no, ',') === false) { // 单条
                array_push($queryParams['where'], ['order_no', $order_no]);
            } else {
                $queryParams['whereIn']['order_no'] = explode(',', $order_no);
            }
            $ordersList = OrdersDoingDBBusiness::getAllList($queryParams, []);
//            $orderInfo = OrdersDoingDBBusiness::getInfoByQuery(1, $queryParams, []);
//            if(empty($orderInfo)) return ;// throws('订单[' . $order_no . '] 记录不存在');
            if(is_object($ordersList) && count($ordersList) <= 0) return ;
            $order_noArr = [];
            $orderIds = [];
            foreach($ordersList as $orderInfo){
//                $order_noArr = [$order_no];
//                $orderIds = [$orderInfo->id];
                array_push($order_noArr, $orderInfo->order_no);
                array_push($orderIds, $orderInfo->id);
                if($orderInfo->has_son_order == 1){// 是否有子订单0无1有
                    $queryParams = [
                        'where' => [
                            ['order_type', 4],// 订单类型1普通订单/父订单4子订单
                            ['parent_order_no', $order_no],
                        ],
                        'select' => ['id', 'order_no']
                    ];
                    $childOrderList = OrdersDoingDBBusiness::getAllList($queryParams, [])->toArray();

                    $childOrders = array_column($childOrderList, 'order_no');
                    $order_noArr =  array_merge($order_noArr, $childOrders);

                    $childOrderIds = array_column($childOrderList, 'id');
                    $orderIds =  array_merge($orderIds, $childOrderIds);
                }
            }

            $order_no_str = implode(',', $order_noArr);
            $order_ids_str = implode(',', $orderIds);


            // 获得订单商品
            $queryGoodsParams = [
                'where' => [
                  //  ['order_type', 4],// 订单类型1普通订单/父订单4子订单
                  //  ['parent_order_no', $order_no],
                ],
                'select' => ['id']
            ];
            if (strpos($order_no_str, ',') === false) { // 单条
                array_push($queryGoodsParams['where'], ['order_no', $order_no_str]);
            } else {
                $queryGoodsParams['whereIn']['order_no'] = explode(',', $order_no_str);
            }
            $childOrderList = OrdersGoodsDoingDBBusiness::getAllList($queryGoodsParams, [])->toArray();
            if(count($childOrderList) > 0){
                $orderGoodsIds = array_column($childOrderList, 'id');
                // 删除订单属性
                $delQuery = [
                    'where' => [
                        //  ['order_type', 4],// 订单类型1普通订单/父订单4子订单
                        //  ['parent_order_no', $order_no],
                    ],
                    // 'select' => ['id']
                ];
                $delQuery['whereIn']['orders_goods_id'] = $orderGoodsIds;
                OrderGoodsPropsDoingDBBusiness::del($delQuery);
            }

            // 删除记录
            $delQuery = [
                'where' => [
                    //  ['order_type', 4],// 订单类型1普通订单/父订单4子订单
                    //  ['parent_order_no', $order_no],
                ],
                // 'select' => ['id']
            ];
            if (strpos($order_no_str, ',') === false) { // 单条
                array_push($delQuery['where'], ['order_no', $order_no_str]);
            } else {
                $delQuery['whereIn']['order_no'] = explode(',', $order_no_str);
            }
            OrdersRecordDoingDBBusiness::del($delQuery);

            // 删除订单商品
            OrdersGoodsDoingDBBusiness::del($delQuery);
            // 删除订单表

            $delQuery = [
                'where' => [
                    //  ['order_type', 4],// 订单类型1普通订单/父订单4子订单
                    //  ['parent_order_no', $order_no],
                ],
                // 'select' => ['id']
            ];
            if (strpos($order_ids_str, ',') === false) { // 单条
                array_push($delQuery['where'], ['id', $order_ids_str]);
            } else {
                $delQuery['whereIn']['id'] = explode(',', $order_ids_str);
            }
            OrdersDoingDBBusiness::del($delQuery);

            // 写操作日志
            $queryParams = [
                'where' => [
                   // ['order_type', 4],// 订单类型1普通订单/父订单4子订单
                   // ['parent_order_no', $order_no],
                ],
                //'select' => ['id', 'order_no']
            ];
            if (strpos($order_no_str, ',') === false) { // 单条
                array_push($queryParams['where'], ['order_no', $order_no_str]);
            } else {
                $queryParams['whereIn']['order_no'] = explode(',', $order_no_str);
            }
            $orderList = OrdersDBBusiness::getAllList($queryParams, []);
            if(empty($logContent)) $logContent = '删除订单[' . $order_no_str . ']正在进行表相关数据';
            foreach($orderList as $orderInfoObj){

                OrdersRecordDBBusiness::saveOrderLog($orderInfoObj , $operate_staff_id , $operate_staff_id_history, $logContent);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
    }

    /**
     * 根据订单号，抢单/派单
     *
     * @param $order_no  订单号,多个用逗号分隔
     * @param int  $company_id 企业id
     * @param int $send_staff_id 派送给的用户id
     * @param int $operate_staff_id 操作人id
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function grabOrder($order_no, $company_id, $send_staff_id, $operate_staff_id = 0){
        if(empty($order_no)) throws('订单号不能为空！');
        if(empty($send_staff_id)) throws('指定派送人员不能为空！');
        // 获得派送人员信息
        $staffInfo = StaffDBBusiness::getInfo($send_staff_id, ['city_site_id', 'city_partner_id', 'admin_type', 'open_status', 'account_status', 'on_line']);
        if(empty($staffInfo)) throws('指定派送人员信息不存在!');
        if($staffInfo['admin_type'] != 32) throws('指定派送人员不是跑腿人员!');
        if($staffInfo['open_status'] != 2) throws('指定派送人员不是已审核状态!');
        if($staffInfo['account_status'] == 1) throws('指定派送人员冻结状态!');
        if($staffInfo['on_line'] != 2) throws('非上班状态，不能接单!');
        $city_site_id = $staffInfo['city_site_id'];
        $city_partner_id = $staffInfo['city_partner_id'];

        // 根据订单号，获得订单信息
        $queryParams = [
            'where' => [
                ['order_type', 1],// 订单类型1普通订单/父订单4子订单
            ],
            /*
            'select' => [
                'id','title','sort_num','volume'
                ,'operate_staff_id','operate_staff_id_history'
                ,'created_at' ,'updated_at'
            ],
            */
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        if (strpos($order_no, ',') === false) { // 单条
            array_push($queryParams['where'], ['order_no', $order_no]);
        } else {
            $queryParams['whereIn']['order_no'] = explode(',', $order_no);
        }
        $orderLists = OrdersDoingDBBusiness::getAllList($queryParams, []);
        if(is_object($orderLists) && count($orderLists) <= 0) throws('订单[' . $order_no . '] 记录不存在'); //记录不存在

        // 遍历判断订单是否可以操作
        foreach($orderLists as $orderInfoObj){
            $temOrderNo = $orderInfoObj->order_no;
            // if($orderInfoObj->city_site_id != $city_site_id) throws('订单[' . $temOrderNo . '] 与指定派送人员不是同一个城市!');
            if($orderInfoObj->send_staff_id > 0 ) throws('订单[' . $temOrderNo . '] 已指定派送人员!');
            if($orderInfoObj->status != 2 ) throws('订单[' . $temOrderNo . '] 非待接单状态!');
            // has_refund 是否退费0未退费1已退费2待退费
            if($orderInfoObj->has_refund == 2 ) throws('订单[' . $temOrderNo . ']待退费中 !');
            if($orderInfoObj->refund_price_frozen > 0 ) throws('订单[' . $temOrderNo . ']还有未完成的退费!');
        }

        DB::beginTransaction();
        try {
            // 获得派送人员历史id
            $send_staff_id_history = 0;
            $saveData = [];
            static::addOprate($saveData, $send_staff_id, $send_staff_id_history);

            // 操作人员历史
            $temData = [];
            $operate_staff_id_history = 0;
            if($send_staff_id != $operate_staff_id){
                static::addOprate($temData, $operate_staff_id,$operate_staff_id_history);
            }else{
                $operate_staff_id_history = $send_staff_id_history;
            }

            // 遍历判断订单是否可以操作
            $cityOrderAmount = [];
            foreach($orderLists as $orderInfoObj){
                $temOrderNo = $orderInfoObj->order_no;
                // if($orderInfoObj->city_site_id != $city_site_id) throws('订单[' . $temOrderNo . '] 与指定派送人员不是同一个城市!');
                if($orderInfoObj->send_staff_id > 0 ) throws('订单[' . $temOrderNo . '] 已指定派送人员!');
                if($orderInfoObj->status != 2 ) throws('订单[' . $temOrderNo . '] 非待接单状态!');
                // has_refund 是否退费0未退费1已退费2待退费
                if($orderInfoObj->has_refund == 2 ) throws('订单[' . $temOrderNo . ']待退费中 !');
                if($orderInfoObj->refund_price_frozen > 0 ) throws('订单[' . $temOrderNo . ']还有未完成的退费!');

                $orderSaveData = [
                     'send_staff_id' => $send_staff_id,// 派送用户id
                     'send_staff_id_history' => $send_staff_id_history,// 派送用户历史id
                     'receipt_time' => date("Y-m-d H:i:s",time()),// 接单时间
                    'status' => 4,// 状态1待支付2等待接单4取货或配送中8订单完成16作废
                ];
                OrdersDoingDBBusiness::updateOrders($orderSaveData,  $temOrderNo, 1 + 2 + 4, $operate_staff_id, $operate_staff_id_history
                    , '指定派送人员成功！派送人员:' . $send_staff_id);
                if( isset($cityOrderAmount[$orderInfoObj->city_site_id]) ){
                    $cityOrderAmount[$orderInfoObj->city_site_id] += 1;
                }else{
                    $cityOrderAmount[$orderInfoObj->city_site_id] = 1;
                }
            }
            // 更新订单饱和度
            foreach($cityOrderAmount as $tem_city_id => $tem_order_amount){
                CityDBBusiness::cityOrdersOperate($tem_city_id, 1, $tem_order_amount);// 减订单
            }

        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
        return 1;
    }

    /**
     * 根据订单号，订单完成
     *
     * @param $order_no  订单号,多个用逗号分隔
     * @param int  $company_id 企业id
     * @param int $operate_staff_id 操作人id
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function finishOrder($order_no, $company_id, $operate_staff_id = 0){
        if(empty($order_no)) throws('订单号不能为空！');
        // 根据订单号，获得订单信息
        $queryParams = [
            'where' => [
                ['order_type', 1],// 订单类型1普通订单/父订单4子订单
            ],
            /*
            'select' => [
                'id','title','sort_num','volume'
                ,'operate_staff_id','operate_staff_id_history'
                ,'created_at' ,'updated_at'
            ],
            */
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        if (strpos($order_no, ',') === false) { // 单条
            array_push($queryParams['where'], ['order_no', $order_no]);
        } else {
            $queryParams['whereIn']['order_no'] = explode(',', $order_no);
        }
        $orderLists = OrdersDoingDBBusiness::getAllList($queryParams, []);
        if(is_object($orderLists) && count($orderLists) <= 0) throws('订单[' . $order_no . '] 记录不存在'); //记录不存在

        // 遍历判断订单是否可以操作
        $parentOrderNos = [];// 如果有子订单的父订单数组
        foreach($orderLists as $orderInfoObj){
            $temOrderNo = $orderInfoObj->order_no;
            if($orderInfoObj->send_staff_id <= 0 ) throws('订单[' . $temOrderNo . '] 未指定派送人员!');
            if($orderInfoObj->status != 4 ) throws('订单[' . $temOrderNo . '] 非取货或配送中状态!');
            if($orderInfoObj->has_refund == 2 ) throws('订单[' . $temOrderNo . ']待退费中 !');
            if($orderInfoObj->refund_price_frozen > 0 ) throws('订单[' . $temOrderNo . ']还有未完成的退费!');
            // 是否有子订单0无1有
            if($orderInfoObj->has_son_order == 1) array_push($parentOrderNos, $temOrderNo);
        }
        // 有子订单号
        $childOrderNos = [];
        if(!empty($parentOrderNos)){
            $temParentOrderNoStr = implode(',', $parentOrderNos);
            $queryParams = [
                'where' => [
                    ['order_type', 4],// 订单类型1普通订单/父订单4子订单
                    // ['parent_order_no', $order_no],
                ],
                'select' => ['id', 'order_no']
            ];
            if (strpos($temParentOrderNoStr, ',') === false) { // 单条
                array_push($queryParams['where'], ['parent_order_no', $temParentOrderNoStr]);
            } else {
                $queryParams['whereIn']['parent_order_no'] = explode(',', $temParentOrderNoStr);
            }
            $childOrderList = OrdersDoingDBBusiness::getAllList($queryParams, [])->toArray();
            $childOrderNos = array_column($childOrderList, 'order_no');
        }

        DB::beginTransaction();
        try {
            // 操作人员历史
            $temData = [];
            $operate_staff_id_history = 0;
            static::addOprate($temData, $operate_staff_id,$operate_staff_id_history);

            // 遍历判断订单是否可以操作
            $order_no_arr = [];
            foreach($orderLists as $orderInfoObj){
                $temOrderNo = $orderInfoObj->order_no;
                if($orderInfoObj->send_staff_id <= 0 ) throws('订单[' . $temOrderNo . '] 未指定派送人员!');
                if($orderInfoObj->status != 4 ) throws('订单[' . $temOrderNo . '] 非取货或配送中状态!');
                if($orderInfoObj->has_refund == 2 ) throws('订单[' . $temOrderNo . ']待退费中 !');
                if($orderInfoObj->refund_price_frozen > 0 ) throws('订单[' . $temOrderNo . ']还有未完成的退费!');
                array_push($order_no_arr, $orderInfoObj->order_no);
                // 订单统计数据
                CountOrdersGrabDBBusiness::createOrderGrab($orderInfoObj, $operate_staff_id , $operate_staff_id_history);

                // 更新订单
                $orderSaveData = [
                    'finish_time' => date("Y-m-d H:i:s",time()),// 送到完成时间
                    'status' => 8,// 状态1待支付2等待接单4取货或配送中8订单完成16作废
                ];
                OrdersDoingDBBusiness::updateOrders($orderSaveData,  $temOrderNo, 2 + 4, $operate_staff_id, $operate_staff_id_history
                    , '订单完成！');
            }
            if(!empty($order_no_arr) || !empty($childOrderNos)){
                $goodOrderNoArr = array_merge($order_no_arr, $childOrderNos);
                // 订单商品统计
                OrdersGoodsDoingDBBusiness::finishGoods(implode(',', $goodOrderNoArr), $operate_staff_id, $operate_staff_id_history);
            }
            if(!empty($order_no_arr)){

                // 删除正在进行订单数据
                OrdersDoingDBBusiness::delDoingOrders( implode(',', $order_no_arr), $operate_staff_id , $operate_staff_id_history, '');// 删除正在进行的订单

            }


        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
        return 1;
    }


    /**
     * 获得最新的，待接单的订单数据
     *
     * @param int  $operate_type 操作类型 1 商家 或者 店铺 2 非商家 或者 店铺
     * @param string  $status 状态, 多个用逗号,分隔 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
     * @param int $city_site_id  城市id
     * @param array $other_where 其它条件
     * @param int $order_id  订单id  获得的最新的订单支付时间
     *            第一次为：0： 直接返回当前最大的订单id
     *            [不用这个-改为最新的订单支付时间]最大订单id :  1：获得大于当前订单id的待接订单及数量，同时获得当前最大的订单id
     *            最新的订单支付时间:
     * @param int  $company_id 企业id
     * @param string $send_staff_id 派送给的用户id--请求数据，可能要接单的，可为0：  非0:主要记录最近一次访问
     * @param int $operate_staff_id 操作人id
     * @return  array
     * @author zouyan(305463219@qq.com)
     */
    public static function getCityWaitOrder($operate_type, $status, $city_site_id, $other_where, $order_id, $company_id, $send_staff_id, $operate_staff_id = 0){
        if(!is_numeric($city_site_id)) $city_site_id = 0;
        // if(!is_numeric($order_id)) $order_id = 0;

        if(is_numeric($order_id)) $order_id = 0;

        if(!is_numeric($send_staff_id)) $send_staff_id = 0;
        if(!is_array($other_where)) $other_where = [];
        // 获得缓存中最大的订单id
        // $maxOrderDoingId = 0;// Tool::getRedis('order:maxOrderDoingId', 3);
        // if(!is_numeric($maxOrderDoingId))  $maxOrderDoingId = 0;


        $return = [
          'order_id' => date("Y-m-d H:i:s",time()),// $maxOrderDoingId,// 最新的订单id
          'city_site_id' => $city_site_id,// 城市id
          'order_num' => 0,// 待处理的订单数量
          'order_list' => [// 待处理的订单数组  ['id' => '订单id' , 'order_no'=> '订单号']
          ],
          'statusList'=> [],// 按状态订单数组 [ '状态值' => [ ['id' => '订单id' , 'order_no'=> '订单号'],... ] ]
          'statusCount'=> [],// 按状态 ['状态值' => '数量',...]
        ];

        $where = [];
        if(!empty($other_where)) $where = array_merge($where, $other_where);
        if($operate_type == 1){// 操作类型 1 商家 或者 店铺 2 非商家 或者 店铺
            array_push($where, ['is_order', 2]);
        }else{
            array_push($where, ['order_type', 1]);// 订单类型1普通订单/父订单4子订单
        }
        if($city_site_id > 0 ){
           array_push($where, ['city_site_id', '=', $city_site_id]);
        }

        // if($maxOrderDoingId <= 0){// 如果缓存中的最大订单id为0,则重新查库验证
        if(empty($order_id)){
            $queryOrderParams = [
                'where' => [
                    // ['order_type', 1],// 订单类型1普通订单/父订单4子订单
                    //['order_no', $order_no],
                    //['pay_run_price', 1],// 是否支付跑腿费0未支付1已支付
                    // ['id', '>', $order_id],
                    // ['pay_time_latest', '>', $order_id],
                ],
                'select' => [
                    'id', 'pay_time_latest'// ,'title','sort_num','volume'
                ],
                'orderBy' => [ 'pay_time_latest'=>'desc', 'id' => 'desc'],// 'sort_num'=>'desc',
            ];

            if(!empty($where)) $queryOrderParams['where'] = array_merge($queryOrderParams['where'], $where);
            if(!empty($order_id)) array_push($queryOrderParams['where'], ['pay_time_latest', '>', $order_id]);

            if (strpos($status, ',') === false) { // 单条
                array_push($queryOrderParams['where'], ['status', $status]);
            } else {
                $queryOrderParams['whereIn']['status'] = explode(',', $status);
            }
            $orderInfo = OrdersDoingDBBusiness::getInfoByQuery(1, $queryOrderParams, []);
            if(!empty($orderInfo)){//  有记录
                // $maxOrderDoingId = $orderInfo->id;
                // $return['order_id'] = $maxOrderDoingId;
                $order_id = $orderInfo->pay_time_latest;
                $return['order_id'] = $order_id;
            //}else{// 没有记录,则当前时间
            //    $return['order_id'] = date("Y-m-d H:i:s",time());
            }
            return $return;
        }

        // 请求 参数为0:第一次请求，直接返回当前最大的订单id
//        if($order_id <=0 ){
//            return $return;
//        }

        // 获得最新的订单
        $queryParams = [
            'where' => [
                // ['operate_type', 1],// 订单类型1普通订单/父订单4子订单
                // ['id', '>', $order_id],
                // ['send_staff_id', 0],
                // ['pay_order_no',$order_no],
                ['pay_time_latest', '>', $order_id],
            ],
            'select' => ['id', 'order_no', 'status', 'pay_time_latest'],// , 'order_type', 'has_son_order', 'is_order'
            'orderBy' => ['pay_time_latest'=>'asc', 'id'=>'asc'],//'sort_num'=>'desc',
        ];
        if(!empty($where)) $queryParams['where'] = array_merge($queryParams['where'], $where);
        if (strpos($status, ',') === false) { // 单条
            array_push($queryParams['where'], ['status', $status]);
        } else {
            $queryParams['whereIn']['status'] = explode(',', $status);
        }

        $orderList = OrdersDoingDBBusiness::getAllList($queryParams, [])->toArray();
        if(empty($orderList)){ // 没有记录,则当前时间
            // $return['order_id'] = date("Y-m-d H:i:s",time());
            return $return;
        }
        $statusCount = [];
        $statusList = [];
        foreach($orderList as $v){
            $statusList[$v['status']][] = $v;
            if(isset($statusCount[$v['status']])){
                $statusCount[$v['status']] += 1;
            }else{
                $statusCount[$v['status']] = 1;
            }
            if(Tool::diffDate($order_id, $v['pay_time_latest'], 1, '时间', 2) > 0){
                $order_id = $v['pay_time_latest'];
                $return['order_id'] = $order_id;
            }
//            if($maxOrderDoingId < $v['id']){
//                $maxOrderDoingId = $v['id'];
//                $return['order_id'] = $maxOrderDoingId;
//            }
        }
        $return['order_list'] = $orderList;
        $return['order_num'] = count($orderList);
        $return['statusList'] = $statusList;
        $return['statusCount'] = $statusCount;


        return $return;
    }

    /**
     * 跑城市最新支付时间范围内已支付订单列表，自动取消订单用
     *
     * @param string $beginDateTime >= 开始时间 格式 'Y-m-d'
     * @param string $endDateTime <= 结束时间 格式 'Y-m-d'
     * @param int $city_site_id 城市id
     * @return array 需要取消并退款的订单信息  二维数组
         [
            [
                'order_no' => $order_no, // 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
                'my_order_no' => $my_order_no,//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
                'refund_amount' => $amount,// 需要退款的金额--0为全退---单位元
                'refund_reason' => $refund_reason,// 退款的原因--:为空，则后台自己组织内容
            ]
        ]
     * @author zouyan(305463219@qq.com)
     */
    public static function getCancelOrderList($beginDateTime = '', $endDateTime = '', $city_site_id = 0){

        // 获得当前订单信息
        $queryParams = [
            'where' => [
                ['order_type', 1],// 订单类型1普通订单/父订单4子订单
                ['status', 2],// 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
                ['pay_run_price', 1],// 是否支付跑腿费0未支付1已支付
                ['has_refund' , '!=', 2],// 是否退费0未退费1已退费2待退费
            ],
            'select' => ['id', 'order_no', 'total_run_price'],//  total_run_price 总跑腿费[扣除退款的] , 'refund_price_frozen' 退费冻结[申请时冻结，成功/失败时减掉]['id', 'city_site_id', 'city_partner_id', 'seller_id'],
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        if(!empty($beginDateTime) && empty($endDateTime)) array_push($queryParams['where'], ['pay_time_latest', '>=', $beginDateTime]);
        if(empty($beginDateTime) && !empty($endDateTime)) array_push($queryParams['where'], ['pay_time_latest', '<=', $endDateTime]);
        if(!empty($beginDateTime) && !empty($endDateTime)){
            $queryParams['whereBetween'] = [
                    'pay_time_latest' => [$beginDateTime, $endDateTime]
                ];
        }
        if($city_site_id > 0 )  array_push($queryParams['where'], ['city_site_id', '=', $city_site_id]);

        $dataList = OrdersDoingDBBusiness::getAllList($queryParams, '')->toArray();
        $resultList = [];
        foreach($dataList as $v){
            array_push($resultList, [
                'order_no' => $v['order_no'],// 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
                'my_order_no' => '',//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
                'refund_amount' => 0,//$v['total_run_price'] 需要退款的金额--0为全退---单位元
                'refund_reason' => '订单超期，系统自动取消并退款',// 退款的原因--:为空，则后台自己组织内容
            ]);
        }
        return $resultList;
    }
}
