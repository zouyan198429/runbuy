<?php
// 统计订单
namespace App\Business\DB\RunBuy;

use Carbon\Carbon;
/**
 *
 */
class CountOrdersDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\CountOrders';
    public static $table_name = 'count_orders';// 表名称

    /**
     * 订单完成统计商品
     *
     * @param obj $orderGoodObj 当前订单商品对象
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_id_history 操作员工历史id
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function createCountGoods(&$orderGoodObj, $operate_staff_id , $operate_staff_id_history){
        $currentNow = Carbon::now();
        // 订单统计
        $searchConditon = [
            'city_site_id' => $orderGoodObj->city_site_id,
            'city_partner_id' => $orderGoodObj->city_partner_id,
            'seller_id' => $orderGoodObj->seller_id,
            'shop_id' => $orderGoodObj->shop_id,
            'goods_id' => $orderGoodObj->goods_id,
            'prop_price_id' => $orderGoodObj->prop_price_id,
            'count_date' => $currentNow->toDateString(),
            'count_year' => $currentNow->year,
            'count_month' => $currentNow->month,
            'count_day' => $currentNow->day,
        ];
        $updateFields = [
            'city_site_id_history' => $orderGoodObj->city_site_id_history,
            'city_partner_id_history' => $orderGoodObj->city_partner_id_history,
            'seller_id_history' =>$orderGoodObj->seller_id_history,
            'shop_id_history' =>$orderGoodObj->shop_id_history,
            'goods_id_history' =>$orderGoodObj->goods_id_history,
            'prop_price_id_history' =>$orderGoodObj->prop_price_id_history,
           // 'total_run_price' => $orderGoodObj->total_run_price,
           // 'total_amount' => $orderGoodObj->total_amount,
           // 'total_price' => $orderGoodObj->total_price,
           // 'balance_status' => 1,
            'operate_staff_id' => $operate_staff_id,
            'operate_staff_id_history' => $operate_staff_id_history,
        ];
        $mainObj = null;
        static::firstOrCreate($mainObj, $searchConditon, $updateFields );
        $mainObj->amount += $orderGoodObj->amount;
        $mainObj->total_amount += $orderGoodObj->total_price;
        $mainObj->save();

    }
}
