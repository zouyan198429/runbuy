<?php
// 订单操作记录
namespace App\Business\DB\RunBuy;

/**
 *
 */
class OrdersRecordDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\OrdersRecord';
    public static $table_name = 'orders_record';// 表名称

    /**
     * 日志
     *
     * @param obj $orderObj 当前订单对象
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_history_id 操作员工历史id
     * @param string $logContent 操作说明
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function saveOrderLog($orderObj , $operate_staff_id , $operate_staff_id_history, $logContent){
        // 工单操作日志
        $orderLog = [
            'order_no' => $orderObj->order_no,
            // 'status_old' => $orderObj->company_id,
            'status_new' => $orderObj->status,

            //'company_id' => $orderObj->company_id,
            //'work_id' => $orderObj->id,
            //'work_status_new' => $orderObj->status,
            'content' => $logContent,// "创建工单", // 操作内容
            'operate_staff_id' => $operate_staff_id,//$orderObj->operate_staff_id,
            'operate_staff_id_history' => $operate_staff_id_history,//$orderObj->operate_staff_id_history,
        ];
        static::create($orderLog);
    }
}
