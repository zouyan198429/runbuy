<?php
// 统计订单
namespace App\Business\DB\RunBuy;
use Carbon\Carbon;

/**
 *
 */
class CountOrdersGrabDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\CountOrdersGrab';
    public static $table_name = 'count_orders_grab';// 表名称

    /**
     * 订单完成统计
     *
     * @param obj $orderObj 当前订单对象
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_id_history 操作员工历史id
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function createOrderGrab(&$orderObj, $operate_staff_id , $operate_staff_id_history){
        $currentNow = Carbon::now();
        // 订单统计
        $searchConditon = [
            'city_site_id' => $orderObj->city_site_id,
            'city_partner_id' => $orderObj->city_partner_id,
            'send_staff_id' => $orderObj->send_staff_id,
            'staff_id' => $orderObj->staff_id,
            'order_no' => $orderObj->order_no,
        ];
        $updateFields = [
            'count_date' => $currentNow->toDateString(),
            'count_year' => $currentNow->year,
            'count_month' => $currentNow->month,
            'count_day' => $currentNow->day,
            'city_site_id_history' => $orderObj->city_site_id_history,
            'city_partner_id_history' => $orderObj->city_partner_id_history,
            'send_staff_id_history' =>$orderObj->send_staff_id_history,
            'staff_id_history' =>$orderObj->staff_id_history,
            'total_run_price' => $orderObj->total_run_price,
            'total_amount' => $orderObj->total_amount,
            'total_price' => $orderObj->total_price,
            'balance_status' => 1,
            'operate_staff_id' => $operate_staff_id,
            'operate_staff_id_history' => $operate_staff_id_history,
        ];
        $mainObj = null;
        static::firstOrCreate($mainObj, $searchConditon, $updateFields );
    }

}
