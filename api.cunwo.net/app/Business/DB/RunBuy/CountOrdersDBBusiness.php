<?php
// 统计订单
namespace App\Business\DB\RunBuy;

use App\Models\RunBuy\CountOrders;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    /**
     * 按分组统计订单商品销量 -最近30天脚本
     *
     * @param array $selectArr 需要在返回值中的字段数组 如 ['shop_id'] 或者 ['city_site_id', 'city_partner_id', 'seller_id', 'shop_id']
     * @param array/string $groupByField groupBy的排序字段 多个字段时用数组  如 ['shop_id']，一个字段时用字符  shop_id'
     * @param array $otherWhere 其它条件[['company_id', '=', $company_id],...]
     * @param string $beginDateTime 开始时间 格式 'Y-m-d'
     * @param string $endDateTime 结束时间 格式 'Y-m-d'
     * @return  array 状态统计数组 --  二维
     * @author zouyan(305463219@qq.com)
     */
    public static function getCountByGroupBy($selectArr = [], $groupByField = [], $otherWhere = [], $beginDateTime = '', $endDateTime = ''){

        $where = [
            // ['company_id', '=', $company_id],
            // ['send_staff_id', '=', $staff_id],
            // ['status', '=', $status],
        ];
        if(!empty($otherWhere)){
            $where = array_merge($where, $otherWhere);
        }
        if(!empty($beginDateTime) && empty($endDateTime)) array_push($countOrderWhere, ['count_date', '>=', $beginDateTime]);
        if(empty($beginDateTime) && !empty($endDateTime)) array_push($countOrderWhere, ['count_date', '<=', $endDateTime]);

        $raw = [
            'sum(amount) as amount_count'
            , 'sum(total_amount) as total_amount'
        ];
        if(!empty($selectArr)) $raw = array_merge($selectArr, $raw);
        $countOrderObj = CountOrders::where($where);
        if(!empty($beginDateTime) && !empty($endDateTime)) $countOrderObj->whereBetween('count_date', [$beginDateTime, $endDateTime]);

        // 'sum(amount) as amount_count, sum(total_amount) as total_amount, status'

        $dataList = $countOrderObj->select(DB::raw(implode(',', $raw)))
            ->groupBy($groupByField)
            ->get();
        // ->groupBy('status')// 多个字段时用数组，一个字段时用字符
        return $dataList;
    }

}
