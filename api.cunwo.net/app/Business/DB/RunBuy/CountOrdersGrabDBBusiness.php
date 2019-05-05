<?php
// 统计订单
namespace App\Business\DB\RunBuy;
use App\Models\RunBuy\CountOrdersGrab;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    // ********统计相关的*****开始**************************************************************************
    /**
     * 统计 --总量统计、按日期统计
     *
     * @param int $company_id 公司id
     * @param int $operate_no 统计类型
     *      1 总量统计-时间段; 2 按日期统计;4 按月统计;8 按年统计;16 按其它统计;
     *      32 数量统计-今日;64 数量统计-昨日;
     *      128 数量统计-本周;256 数量统计-上周;
     *      512 数量统计-本月;1024 数量统计-上月;
     *     2048 数量统计-本年;4096 数量统计-上年
     * @param string $begin_date 开始日期 YYYY-MM-DD
     * @param string $end_date 结束日期 YYYY-MM-DD
     * @param int $city_site_id 城市分站id
     * @param int $city_partner_id 城市合伙人id
     * @param int $send_staff_id 派送用户id
     * @param int $staff_id 下单用户id
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function getCountData($company_id = 0, $operate_no = 0, $begin_date = '', $end_date = '',  $city_site_id = 0, $city_partner_id = 0, $send_staff_id = 0, $staff_id = 0){

        $listData = [];
        //统计-总量统计-1 时间段
        if(($operate_no & 1) == 1) {
            $listData['count'] = static::getCountAmount($company_id, 0, $begin_date, $end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id);
        }
        //统计-2 按日期统计
        if(($operate_no & 2) == 2 ) {
            $listData['countDay'] = static::getCountAmount($company_id, 1, $begin_date, $end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id);
        }
        //统计-4 按月统计
        if(($operate_no & 4) == 4 ) {
            $listData['countMonth'] = static::getCountAmount($company_id, 2, $begin_date, $end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id);
        }
        //统计-8 按年统计
        if(($operate_no & 8) == 8 ) {
            $listData['countYear'] = static::getCountAmount($company_id, 3, $begin_date, $end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id);
        }
        //统计-16 按其它统计
        if(($operate_no & 16) == 16 ) {
            $listData['countSelf'] = static::getCountAmount($company_id, 4, $begin_date, $end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id);
        }


        // 32 数量统计-今日
        if(($operate_no & 32) == 32 ) {
            $tem_begin_date = date("Y-m-d");
            $tem_end_date = date("Y-m-d");
            $listData['sumToday'] = [
                'begin_date' => $tem_begin_date,
                'end_date' => $tem_end_date,
                'amount' => static::getCountAmount($company_id, 0, $tem_begin_date, $tem_end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id),
            ];
        }

        // 64 数量统计-昨日
        if(($operate_no & 64) == 64 ) {
            $tem_begin_date = Tool::addMinusDate('', ['-1 day'], 'Y-m-d', 1, '日期');
            $tem_end_date = Tool::addMinusDate('', ['-1 day'], 'Y-m-d', 1, '日期');
            $listData['sumYesterday'] = [
                'begin_date' => $tem_begin_date,
                'end_date' => $tem_end_date,
                'amount' => static::getCountAmount($company_id, 0, $tem_begin_date, $tem_end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id),
            ];
        }

        // 128 数量统计-本周
        if(($operate_no & 128) == 128 ) {
            $tem_begin_date = Tool::getDateByType(1);// 1本周一
            $tem_end_date = Tool::getDateByType(2);// 2 本周日;
            $listData['sumCurrentWeek'] = [
                'begin_date' => $tem_begin_date,
                'end_date' => $tem_end_date,
                'amount' => static::getCountAmount($company_id, 0, $tem_begin_date, $tem_end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id),
            ];
        }
        // 256 数量统计-上周
        if(($operate_no & 256) == 256 ) {
            $tem_begin_date = Tool::getDateByType(3);//3 上周一;
            $tem_end_date = Tool::getDateByType(4);// 4 上周日;
            $listData['sumPreWeek'] = [
                'begin_date' => $tem_begin_date,
                'end_date' => $tem_end_date,
                'amount' => static::getCountAmount($company_id, 0, $tem_begin_date, $tem_end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id),
            ];
        }
        // 512 数量统计-本月
        if(($operate_no & 512) == 512 ) {
            $tem_begin_date = Tool::getDateByType(5);// 5 本月一日;
            $tem_end_date = Tool::getDateByType(6);// 6 本月最后一日;
            $listData['sumCurrentMonth'] = [
                'begin_date' => $tem_begin_date,
                'end_date' => $tem_end_date,
                'amount' => static::getCountAmount($company_id, 0, $tem_begin_date, $tem_end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id),
            ];
        }
        // 1024 数量统计-上月
        if(($operate_no & 1024) == 1024 ) {
            $tem_begin_date = Tool::getDateByType(7);// 7 上月一日;
            $tem_end_date = Tool::getDateByType(8);// 8 上月最后一日
            $listData['sumPreMonth'] = [
                'begin_date' => $tem_begin_date,
                'end_date' => $tem_end_date,
                'amount' => static::getCountAmount($company_id, 0, $tem_begin_date, $tem_end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id),
            ];
        }
        // 2048 数量统计-本年
        if(($operate_no & 2048) == 2048 ) {
            $tem_begin_date = Tool::getDateByType(9);// 9 本年一日
            $tem_end_date = Tool::getDateByType(10);// 10 本年最后一日
            $listData['sumCurrentYear'] = [
                'begin_date' => $tem_begin_date,
                'end_date' => $tem_end_date,
                'amount' => static::getCountAmount($company_id, 0, $tem_begin_date, $tem_end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id),
            ];
        }

        // 4096 数量统计-上年
        if(($operate_no & 4096) == 4096 ) {
            $tem_begin_date = Tool::getDateByType(11);//11 上年一日
            $tem_end_date = Tool::getDateByType(12);// 12 上年最后一日
            $listData['sumPreYear'] = [
                'begin_date' => $tem_begin_date,
                'end_date' => $tem_end_date,
                'amount' => static::getCountAmount($company_id, 0, $tem_begin_date, $tem_end_date, $city_site_id, $city_partner_id, $send_staff_id, $staff_id),
            ];
        }

        return $listData;
    }
    /**
     * 统计 --总量统计、按日期统计
     *
     * @param int $company_id 公司id
     * @param int $count_type 统计类型 0 总量统计, 1 日期统计[按日] ;2日期统计[按月];3日期统计[按年],4 其它统计[自己处理]
     * @param string $begin_date 开始日期 YYYY-MM-DD 为空且有数据：则以第一条记录的日期为开始日期
     * @param string $end_date 结束日期 YYYY-MM-DD
     * @param int $city_site_id 城市分站id
     * @param int $city_partner_id 城市合伙人id
     * @param int $send_staff_id 派送用户id
     * @param int $staff_id 下单用户id
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function getCountAmount($company_id = 0, $count_type = 0, $begin_date = '', $end_date = '', $city_site_id = 0, $city_partner_id = 0, $send_staff_id = 0, $staff_id = 0){
        $selectArr = [];// company_id,city_site_id,count_date,SUM(amount) AS amount
        $otherWhere = [];
        $inWhereArr = [];
        $betweenWhereArr = [];
        $groupByArr = [];
        $havingRaw = '';
        $orderByArr = [];

        $temDataArr = [
            'total_run_price' => Tool::formatMoney(0.00, 2, ''),// 总跑腿费[扣除退款的]
            'total_price' => Tool::formatMoney(0.00, 2, ''),// 商品总价
            'total_amount' => 0,// 商品数量
            'record_num' => 0// 订单数据量
        ];

//        if($company_id > 0){
//            array_push($otherWhere, ['company_id', '=', $company_id]);
//            if($count_type !== 0){
//                array_push($selectArr, 'company_id');
//                array_push($groupByArr, 'company_id');
//                $temDataArr['company_id'] = $company_id;
//            }
//        }

        if($city_site_id > 0){// 城市分站统计
            array_push($otherWhere, ['city_site_id', '=', $city_site_id]);
            if($count_type !== 0) {
                array_push($selectArr, 'city_site_id');
                array_push($groupByArr, 'city_site_id');
                $temDataArr['city_site_id'] = $city_site_id;
            }
        }

        if($city_partner_id > 0){// 城市合伙人统计
            array_push($otherWhere, ['city_partner_id', '=', $city_partner_id]);
            if($count_type !== 0) {
                array_push($selectArr, 'city_partner_id');
                array_push($groupByArr, 'city_partner_id');
                $temDataArr['city_partner_id'] = $city_partner_id;
            }
        }

        if($send_staff_id > 0){// 派送用户统计
            array_push($otherWhere, ['send_staff_id', '=', $send_staff_id]);
            if($count_type !== 0) {
                array_push($selectArr, 'send_staff_id');
                array_push($groupByArr, 'send_staff_id');
                $temDataArr['send_staff_id'] = $send_staff_id;
            }
        }

        if($staff_id > 0){// 用户统计
            array_push($otherWhere, ['staff_id', '=', $staff_id]);
            if($count_type !== 0) {
                array_push($selectArr, 'staff_id');
                array_push($groupByArr, 'staff_id');
                $temDataArr['staff_id'] = $staff_id;
            }
        }

        // 开始日期
//        if(!empty($begin_date)){
//            array_push($otherWhere, ['count_date', '>=', $begin_date]);
//        }

        // 结束日期
        if(in_array($count_type, [1,2,3]) && empty($end_date)) $end_date = date("Y-m-d");

//        if(!empty($end_date)){
//            array_push($otherWhere, ['count_date', '<=', $end_date]);
//        }

//        if(!empty($begin_date) && $begin_date == $end_date){
//            array_push($otherWhere, ['count_date', '=', $begin_date]);
//        }

        if(!empty($begin_date) && !empty($end_date)){
            $betweenWhereArr['count_date'] = [$begin_date, $end_date];
        }else{
            // 开始日期
            if(!empty($begin_date)){
                array_push($otherWhere, ['count_date', '>=', $begin_date]);
            }
            // 结束日期
            if(!empty($end_date)){
                array_push($otherWhere, ['count_date', '<=', $end_date]);
            }
        }

        switch ($count_type)
        {
            case 1:// 按日统计
                array_push($selectArr, 'count_date');
                array_push($groupByArr, 'count_date');
                $orderByArr['count_date'] = 'asc';
                $temDataArr['count_date'] = '';
                break;
            case 2:// 2日期统计[按月]
                array_push($selectArr, 'count_year', 'count_month');
                array_push($groupByArr, 'count_year', 'count_month');
                $orderByArr['count_year'] = 'asc';
                $orderByArr['count_month'] = 'asc';
                $temDataArr['count_year'] = '';
                $temDataArr['count_month'] = '';
                break;
            case 3:// 3日期统计[按年]
                array_push($selectArr, 'count_year');
                array_push($groupByArr, 'count_year');
                $orderByArr['count_year'] = 'asc';
                $temDataArr['count_year'] = '';
                break;
            default:
        }
        if($count_type !== 0) {
            $countList = static::getCount($company_id, $selectArr, $otherWhere, $inWhereArr, $betweenWhereArr, $groupByArr, $havingRaw, $orderByArr);
        }else{// 所有的
            $countList = static::getSumCount($company_id, $otherWhere, $inWhereArr, $betweenWhereArr);
        }
        //直接返回
        if( !in_array($count_type, [1,2,3]) ) return $countList;

        // 没有开始日期
        if(empty($begin_date)){
            $begin_date = $countList[0]['count_date'] ?? '';
            // 没有数据
            if(empty($begin_date)){
                if(empty($end_date)) return $countList;
                $begin_date = $end_date;
            }
        }

        $formatCountList = [];
        switch ($count_type)
        {
            case 1:// 按日统计
                $formatCountList = Tool::arrUnderReset($countList, 'count_date', 1);
                $dataRange = Tool::dateRange($begin_date, $end_date);
                break;
            case 2:// 2日期统计[按月]
                foreach($countList as $v){
                    $count_year = $v['count_year'];
                    $count_month = sprintf('%02s', $v['count_month']);// 2位，不够左补0
                    $v['count_month'] = $count_month;
                    $formatCountList[$count_year . $count_month] = $v;
                }
                $dataRange = Tool::showMonthRange($begin_date, $end_date);
                break;
            case 3:// 3日期统计[按年]
                $formatCountList = Tool::arrUnderReset($countList, 'count_year', 1);
                $dataRange = Tool::showYearRange($begin_date, $end_date);
                break;
            default:
        }

        $returnData = [];
        foreach($dataRange as $temDate){
            if(isset($formatCountList[$temDate])){
                $returnData[] = $formatCountList[$temDate];
            }else{
                $temDatas = $temDataArr;
                switch ($count_type)
                {
                    case 1:// 按日统计
                        $temDatas['count_date'] = $temDate;
                        break;
                    case 2:// 2日期统计[按月]
                        $temDatas['count_year'] = substr($temDate,0,4);// 前4位
                        $temDatas['count_month'] = substr($temDate,-2);;// 后2位
                        break;
                    case 3:// 3日期统计[按年]
                        $temDatas['count_year'] = $temDate;
                        break;
                    default:
                }
                $returnData[] = $temDatas;
            }
        }
        $countList = $returnData;
        return $countList;
    }

    /**
     * 统计
     *
     * @param int $company_id 公司id
     * @param array $selectArr 返回字段数组 一维
     * @param array $otherWhere 其它条件[['company_id', '=', $company_id],...]
     * @param array $inWhereArr in条件 一维数组 ['字段'->[数组值]]
     * @param array $betweenWhereArr between条件 一维维数组 ['字段'->[数组值1,数组值2]]
     * @param array $groupByArr 分组字段数组 一维
     * @param string $havingRaw 分组过滤条件
     * @param array $orderByArr in条件 一维数组 ['字段'->'asc/desc']
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function getCount($company_id, $selectArr = [] , $otherWhere = [], $inWhereArr = [], $betweenWhereArr = [], $groupByArr = [], $havingRaw = '', $orderByArr = [])
    {
        $where = [
//            ['company_id', '=', $company_id],
            // ['send_staff_id', '=', $staff_id],
        ];
        if (!empty($otherWhere)) {
            $where = $otherWhere;// array_merge($where, $otherWhere);
        }

        $select = 'sum(total_run_price) as total_run_price';
        array_push($selectArr, $select);
        $select = 'sum(total_price) as total_price';
        array_push($selectArr, $select);
        $select = 'sum(total_amount) as total_amount';
        array_push($selectArr, $select);
        $select = 'count(*) as record_num';
        array_push($selectArr, $select);

        $obj = CountOrdersGrab::where($where)
            ->select(DB::raw(implode(',', $selectArr)));
        foreach($inWhereArr as $field => $inWhere){
            $obj->whereIn($field,$inWhere);
        }

        foreach($betweenWhereArr as $betweenField => $rangeValsArr){
            if(!is_array($rangeValsArr) || count($rangeValsArr) != 2) continue;
            $obj->whereBetween($betweenField, $rangeValsArr);// ->whereBetween('votes', [1, 100])
        }

        foreach($groupByArr as $group){
            $obj->groupBy($group);
        }

        if(!empty($havingRaw)){
            $obj->havingRaw($havingRaw);
        }

        foreach($orderByArr as $field => $order){
            $obj->orderBy($field, $order);
        }

        $dataList = $obj->get()->toArray();

        // 有空的或null的数据，需要格式化数据
        foreach($dataList as $k => $v){
            foreach($v as $k_f => $k_v){
                if(empty($k_v) || is_null($k_v)) $k_v = 0;
                if(in_array($k_f, ['total_run_price', 'total_price'])){
                    $k_v = Tool::formatMoney($k_v, 2, '');
                }
                $v[$k_f] = $k_v;
            }
            $dataList[$k] = $v;
        }
        return $dataList;
    }

    /**
     * 统计
     *
     * @param int $company_id 公司id
     * @param array $otherWhere 其它条件[['company_id', '=', $company_id],...]
     * @param array $inWhereArr in条件 一维数组 ['字段'->[数组值]]
     * @param array $betweenWhereArr between条件 一维维数组 ['字段'->[数组值1,数组值2]]
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function getSumCount($company_id, $otherWhere = [], $inWhereArr = [], $betweenWhereArr = [])
    {
        $where = [
//            ['company_id', '=', $company_id],
            // ['send_staff_id', '=', $staff_id],
        ];
        if (!empty($otherWhere)) {
            $where = $otherWhere;// array_merge($where, $otherWhere);
        }
        $selectArr = [];
        $select = 'sum(total_run_price) as total_run_price';
        array_push($selectArr, $select);
        $select = 'sum(total_price) as total_price';
        array_push($selectArr, $select);
        $select = 'sum(total_amount) as total_amount';
        array_push($selectArr, $select);
        $select = 'count(*) as record_num';
        array_push($selectArr, $select);

        $obj = CountOrdersGrab::where($where)
            ->select(DB::raw(implode(',', $selectArr)));
        foreach($inWhereArr as $field => $inWhere){
            $obj->whereIn($field,$inWhere);
        }

        foreach($betweenWhereArr as $betweenField => $rangeValsArr){
            if(!is_array($rangeValsArr) || count($rangeValsArr) != 2) continue;
            $obj->whereBetween($betweenField, $rangeValsArr);// ->whereBetween('votes', [1, 100])
        }

//        $sumAmount = $obj->sum('total_run_price');
//        return $sumAmount;

        $dataList = $obj->get()->toArray();

        // 有空的或null的数据，需要格式化数据
        foreach($dataList as $k => $v){
            foreach($v as $k_f => $k_v){
                if(empty($k_v) || is_null($k_v)) $k_v = 0;
                if(in_array($k_f, ['total_run_price', 'total_price'])){
                    $k_v = Tool::formatMoney($k_v, 2, '');
                }
                $v[$k_f] = $k_v;
            }
            $dataList[$k] = $v;
        }
        return $dataList;
    }
    // ********统计相关的*****结束**************************************************************************
}
