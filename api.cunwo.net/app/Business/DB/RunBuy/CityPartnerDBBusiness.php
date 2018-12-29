<?php
// 城市合伙人
namespace App\Business\DB\RunBuy;

use Illuminate\Support\Facades\DB;
/**
 *
 */
class CityPartnerDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\CityPartner';
    public static $table_name = 'city_partner';// 表名称

    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, CityPartnerHistoryDBBusiness::$model_name
            , CityPartnerHistoryDBBusiness::$table_name, $historyDBObj, ['city_partner_id' => $mainId], []);
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
            'city_partner_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, CityPartnerHistoryDBBusiness::$model_name
            , CityPartnerHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['city_partner_id'], $forceIncVersion);
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

        if(isset($saveData['partner_name']) && empty($saveData['partner_name'])  ){
            throws('名称不能为空！');
        }

        if(isset($saveData['linkman']) && empty($saveData['linkman'])  ){
            throws('联系人不能为空！');
        }

        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
            throws('手机不能为空！');
        }

        if($id <= 0 && isset($saveData['admin_username']) && empty($saveData['admin_username'])  ){
            throws('用户名不能为空！');
        }

        // 查询手机号是否已经有企业使用--账号表里查
//        if( $id <= 0 && isset($saveData['mobile']) && StaffDBBusiness::judgeFieldExist($company_id, $id ,"mobile", $saveData['mobile'], [])){
//            throws('手机号已存在！');
//        }

        // 用户名
        if( $id <= 0 && isset($saveData['admin_username']) && StaffDBBusiness::judgeFieldExist($company_id, $id ,"admin_username", $saveData['admin_username'], [])){
            throws('用户名已存在！');
        }

        $staffInfo = [];
        if( $id <= 0 ){
           if(!isset($saveData['admin_username']) || !isset($saveData['admin_password'])){
               throws('不能没有帐户信息！');
           }
            $staffInfo = [
               'admin_type' => 4,// 4城市代理
               'city_site_id' => $saveData['city_site_id'] ?? 0,
               'city_partner_id' => 0,
               'seller_id' => 0,
               'shop_id' => 0,
               'admin_username' => $saveData['admin_username'] ?? '',
               'admin_password' => $saveData['admin_password'] ?? '',
               'issuper' => 1,
               'account_status' => 0,
               'real_name' => $saveData['linkman'] ?? '',
               'sex' => $saveData['sex'] ?? '0',
               'tel' => $saveData['tel'] ?? '',
                'mobile' => $saveData['mobile'] ?? '',
                'province_id' => $saveData['province_id'] ?? 0,
                'city_id' => $saveData['city_id'] ?? 0,
                'area_id' => $saveData['area_id'] ?? 0,
                'addr' => $saveData['addr'] ?? '',
                'operate_staff_id' => $operate_staff_id,
           ];
           // 电话已存在，则为空
            if( isset($saveData['mobile']) && StaffDBBusiness::judgeFieldExist($company_id, $id ,"mobile", $saveData['mobile'], [])){
                $staffInfo['mobile'] = '';
//                throws('手机号已存在！');
            }
            if(isset($saveData['admin_username'])) unset($saveData['admin_username']);
            if(isset($saveData['admin_password'])) unset($saveData['admin_password']);

        }
        DB::beginTransaction();
        try {

            // 省id历史
            if( isset($saveData['province_id']) && $saveData['province_id'] > 0 ){
                if($id <= 0) $staffInfo['province_id_history'] = CityDBBusiness::getIdHistory($saveData['province_id']);
    //            $saveData['province_id_history'] = CityDBBusiness::getIdHistory($saveData['province_id']);
            }

            // 市id历史
            if( isset($saveData['city_id']) && $saveData['city_id'] > 0 ){
                if($id <= 0) $staffInfo['city_id_history'] = CityDBBusiness::getIdHistory($saveData['city_id']);
    //            $saveData['city_id_history'] = CityDBBusiness::getIdHistory($saveData['city_id']);
            }

            // 区县id历史
            if( isset($saveData['area_id']) && $saveData['area_id'] > 0 ){
                if($id <= 0) $staffInfo['area_id_history'] = CityDBBusiness::getIdHistory($saveData['area_id']);
    //            $saveData['area_id_history'] = CityDBBusiness::getIdHistory($saveData['area_id']);
            }


            $isModify = false;
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
                if($id <= 0) $staffInfo['operate_staff_id_history'] = $operate_staff_id_history;
            }
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData);
                $id = $resultDatas['id'] ?? 0;

                // 添加帐户信息
                $staffInfo['city_partner_id'] = $id;
                // StaffDBBusiness::create($staffInfo);
                StaffDBBusiness::replaceById($staffInfo, $company_id, $staffId, $operate_staff_id, $modifAddOprate);
            }else{// 修改
                $saveBoolen = static::saveById($saveData, $id);
                // $resultDatas = static::getInfo($id);

            }

            if($isModify){
                static::compareHistory($id, 1);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws('操作失败；信息[' . $e->getMessage() . ']');
            // throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }
}
