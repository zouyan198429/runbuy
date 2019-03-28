<?php
// 订单
namespace App\Business\DB\RunBuy;

use App\Models\RunBuy\Orders;
use App\Models\RunBuy\OrdersDoing;
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
                ['staff_id', $operate_staff_id],
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
                ['staff_id', $operate_staff_id],
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
        }

        // 数字或单条
        $statusArr = [];
        if( is_numeric($status) ||  (is_string($status) && strpos($status, ',') === false) ){
            if($status != '') array_push($where,['$status', '=', $status]);
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
        foreach ($status as $temStatus){
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
            if($status != '') array_push($where,['$status', '=', $status]);
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
     * @param string  $order_no 订单号
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
                    ['order_no', $order_no],
                    // ['pay_run_price', 1],// 是否支付跑腿费0未支付1已支付
                ],
                'select' => ['id', 'has_son_order']
                /*
                'select' => [
                    'id','title','sort_num','volume'
                    ,'operate_staff_id','operate_staff_id_history'
                    ,'created_at' ,'updated_at'
                ],
                */
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            $orderInfo = OrdersDoingDBBusiness::getInfoByQuery(1, $queryParams, []);
            if(empty($orderInfo)) return ;// throws('订单[' . $order_no . '] 记录不存在');
            $order_noArr = [$order_no];
            $orderIds = [$orderInfo->id];
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
}
