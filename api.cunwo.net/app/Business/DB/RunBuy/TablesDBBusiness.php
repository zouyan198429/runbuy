<?php
// 桌位
namespace App\Business\DB\RunBuy;

use App\Models\RunBuy\Tables;
use Illuminate\Support\Facades\DB;


/**
 *
 */
class TablesDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Tables';
    public static $table_name = 'tables';// 表名称
    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, TablesHistoryDBBusiness::$model_name
            , TablesHistoryDBBusiness::$table_name, $historyDBObj, ['table_id' => $mainId], []);
    }

    /**
     * 对比主表和历史表是否相同，相同：不更新版本号，不同：版本号+1
     *
     * @param mixed $mId 主表对象主键值
     * @param int $forceIncVersion 如果需要主表版本号+1,是否更新主表 1 更新 ;0 不更新
     * @return array 不同字段的内容 数组 [ '字段名' => ['原表中的值','历史表中的值']]; 空数组：不用版本号+1;非空数组：版本号+1
     * @author zouyan(305463219@qq.com)
     */
    public static function compareHistory($id = 0, $forceIncVersion = 0, &$mainDBObj = null, &$historyDBObj = null){
        // 判断版本号是否要+1
        $historySearch = [
            //  'company_id' => $company_id,
            'table_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, TablesHistoryDBBusiness::$model_name
            , TablesHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['table_id'], $forceIncVersion);
    }


    /**
     * 根据id新加或修改单条数据-id 为0 新加，  > 0 ：修改对应的记录，返回记录id值
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['table_name']) && empty($saveData['table_name'])  ){
            throws('桌位号/包间名称不能为空！');
        }

        // 店铺id,获得 商家id
        $city_site_id = $saveData['city_site_id'] ?? 0;
        $city_partner_id = $saveData['city_partner_id'] ?? 0;
        $seller_id = $saveData['seller_id'] ?? 0;
        $shop_id = $saveData['shop_id'] ?? 0;
        if(is_numeric($shop_id) && $shop_id > 0 && ($city_site_id <= 0 || $city_partner_id <= 0 || $seller_id <= 0)){
            $shopInfo = ShopDBBusiness::getInfo($shop_id, ['city_site_id', 'city_partner_id', 'seller_id']);
            // $seller_id = $shopInfo['seller_id'] ?? 0;
            // $saveData['seller_id'] = $seller_id;

            $city_site_id = $shopInfo['city_site_id'] ?? 0;
            $city_partner_id = $shopInfo['city_partner_id'] ?? 0;
            $seller_id = $shopInfo['seller_id'] ?? 0;
            $saveData['city_site_id'] = $city_site_id;
            $saveData['city_partner_id'] = $city_partner_id;
            $saveData['seller_id'] = $seller_id;
        }

        $table_person_id = $saveData['table_person_id'] ?? 0;

        // 是否有图片资源
        $hasResource = false;
        $resourceIds = [];
        if(isset($saveData['resourceIds'])){
            $hasResource = true;
            $resourceIds = $saveData['resourceIds'];
            unset($saveData['resourceIds']);
        }
        // 判断是否可以关闭--已占桌和确认占桌的不可关闭
        $temIsOpen = $saveData['is_open'] ?? 0;
        if($id > 0 && $temIsOpen == 1){
            $info = static::getInfo($id, ['id', 'status', 'is_open']);
            if(empty($info)) throws('记录不存在');
            $temStatus = $info['status'];
            $temIsOpen = $info['is_open'];
            if(in_array($temStatus, [2, 4]) ) throws('当前记录状态非待占桌，不可进行关闭操作！');
        }

        DB::beginTransaction();
        try {

            $shop_id_history = ( $shop_id > 0) ? ShopDBBusiness::getIdHistory($shop_id) : 0;// 店铺历史ID
            if($shop_id_history > 0) $saveData['shop_id_history'] = $shop_id_history;
            $table_person_id_history = ( $table_person_id > 0) ? TablePersonDBBusiness::getIdHistory($table_person_id) : 0;// 店铺历史ID
            if($table_person_id_history > 0) $saveData['table_person_id_history'] = $table_person_id_history;

            $isModify = false;
            $operate_staff_id_history = 0;
            if($id > 0){
                $isModify = true;
                // 判断权限
                //            $judgeData = [
                //                'company_id' => $company_id,
                //            ];
                //            $relations = '';
                //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                if($modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history);

            }else {// 新加;要加入的特别字段
                //            $addNewData = [
                //                'company_id' => $company_id,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history);
            }
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $modelObj = null;
                $saveBoolen = static::saveById($saveData, $id, $modelObj);
                // $resultDatas = static::getInfo($id);

            }

            // 同步修改图片资源关系
            if($hasResource){
                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
            }

            // 修改时，更新版本号
            if($isModify){
                static::compareHistory($id, 1);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

    /**
     * 根据id生成二维码
     *
     * @param int  $company_id 企业id
     * @param int $id 记录id
     * @param string $files_names  文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function createQrCode($company_id, $id, $files_names, $operate_staff_id = 0, $modifAddOprate = 0){

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }
        if(strlen($files_names) <= 0){
            throws('二维码图片地址不能为空！');
        }
        $info = static::getInfo($id, ['id', 'city_site_id', 'city_partner_id', 'seller_id', 'shop_id']);
        if(empty($info)) throws('记录不存在');
        $saveData = [
            'city_site_id' => $info['city_site_id'] ?? 0,
            'city_partner_id' => $info['city_partner_id'] ?? 0,
            'seller_id' => $info['seller_id'] ?? 0,
            'shop_id' => $info['shop_id'] ?? 0,
            'qrcode_url' => $files_names,
            'has_qrcode' => 2,
        ];
        return static::replaceById($saveData, $company_id, $id, $operate_staff_id, $modifAddOprate);
    }


    /**
     * 根据id删除
     *
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }

        // 判断是否可以删除--已占桌和确认占桌的不可删除
        $info = static::getInfo($id, ['id', 'status', 'is_open']);
        if(empty($info)) throws('记录不存在');
        $temStatus = $info['status'];
        // $temIsOpen = $info['is_open'];
        if(in_array($temStatus, [2, 4]) ) throws('当前记录状态非待占桌，不可进行删除操作！');


        DB::beginTransaction();
        try {
            // 删除图片资源关系
            static::delResourceDetach($id, []);
            // 删除商品
            static::deleteByIds($id);
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

    /**
     * 按状态分组统计数量 -- 只处理状态 状态1待占桌2已占桌3确认占桌[从进行表]   8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]-- 从历史表
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
//        if(strpos(',' . $status . ',', ',2,') !== false){
//            array_push($where, ['has_refund', '!=', 2]); // 是否退费0未退费1已退费2待退费
//            array_push($where, ['refund_price_frozen', '<=', 0]);
//        }

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
//        if(empty($statusArr) || in_array(8, $statusArr) || in_array(16, $statusArr) || in_array(32, $statusArr) || in_array(64, $statusArr)){
//            $orderObj = Orders::where($where);
//        } else {
//            $orderObj = OrdersDoing::where($where);
//        }
        $orderObj = Tables::where($where);
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
//        if(strpos(',' . $status . ',', ',2,') !== false){
//            array_push($where, ['has_refund', '!=', 2]); // 是否退费0未退费1已退费2待退费
//        }

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
//        if(empty($statusArr) || in_array(8, $statusArr) || in_array(16, $statusArr) || in_array(32, $statusArr) || in_array(64, $statusArr)){
//            $orderObj = Orders::where($where);
//        } else {
//            $orderObj = OrdersDoing::where($where);
//        }
        $orderObj = Tables::where($where);
        // 是数组
        if(!empty($status) && is_array($status)){
            $orderObj = $orderObj->whereIn('status',$status);
        }
        $dataCount = $orderObj->count();

        return $dataCount;
    }


}
