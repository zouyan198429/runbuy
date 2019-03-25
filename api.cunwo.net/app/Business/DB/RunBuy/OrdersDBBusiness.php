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
}
