<?php
// 店铺
namespace App\Business\DB\RunBuy;

use App\Services\Map\Map;
use Illuminate\Support\Facades\DB;
/**
 *
 */
class ShopDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Shop';
    public static $table_name = 'shop';// 表名称
    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, ShopHistoryDBBusiness::$model_name
            , ShopHistoryDBBusiness::$table_name, $historyDBObj, ['shop_id' => $mainId], []);
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
            'shop_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, ShopHistoryDBBusiness::$model_name
            , ShopHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['shop_id'], $forceIncVersion);
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

        if(isset($saveData['shop_name']) && empty($saveData['shop_name'])  ){
            throws('店铺名称不能为空！');
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

        // 如果有经纬度信息
        if(isset($saveData['latitude'])){
            $latitude = $saveData['latitude'] ?? ''; // 纬度
            $longitude = $saveData['longitude'] ?? ''; // 经度
            if($latitude == '' || $longitude == '' || ($latitude == '0' && $longitude == '0') ){
                throws('经纬度不能为空！');
            }
            $hashs = Map::getGeoHashs($latitude, $longitude);
            $saveData['geohash'] = $hashs[0] ?? '';
            $saveData['geohash3'] = $hashs[3] ?? '';
            $saveData['geohash4'] = $hashs[4] ?? '';
            $saveData['geohash5'] = $hashs[5] ?? '';
            if(!is_numeric($latitude)) $latitude = 0;
            if(!is_numeric($longitude)) $longitude = 0;
            $saveData['lat'] = $latitude;
            $saveData['lng'] = $longitude;
        }

        // 查询手机号是否已经有企业使用--账号表里查
//        if( $id <= 0 && isset($saveData['mobile']) && StaffDBBusiness::judgeFieldExist($company_id, $id ,"mobile", $saveData['mobile'], [], 1)){
//            throws('手机号已存在！');
//        }

        // 用户名
        if( $id <= 0 && isset($saveData['admin_username']) && StaffDBBusiness::judgeFieldExist($company_id, $id ,"admin_username", $saveData['admin_username'], [], 1)){
            throws('用户名已存在！');
        }


        // 商家id,获得 城市代理id 、城市分站id
        $city_site_id = $saveData['city_site_id'] ?? 0;
        $city_partner_id = $saveData['city_partner_id'] ?? 0;
        $seller_id = $saveData['seller_id'] ?? 0;
        if(is_numeric($seller_id) && $seller_id > 0 && ($city_site_id <= 0 || $city_partner_id <= 0)){
            $sellerInfo = SellerDBBusiness::getInfo($seller_id, ['city_site_id', 'city_partner_id']);
            $city_site_id = $sellerInfo['city_site_id'] ?? 0;
            $city_partner_id = $sellerInfo['city_partner_id'] ?? 0;
            $saveData['city_site_id'] = $city_site_id;
            $saveData['city_partner_id'] = $city_partner_id;
        }
        $saveData['last_update'] = date('Y-m-d H:i:s');

        // 是否有标签
        $hasLabel = false;
        $labelIds = [];
        if(isset($saveData['labelIds'])){
            $hasLabel = true;
            $labelIds = $saveData['labelIds'];
            unset($saveData['labelIds']);
        }

        // 是否有图片资源
        $hasResource = false;
        $resourceIds = [];
        if(isset($saveData['resourceIds'])){
            $hasResource = true;
            $resourceIds = $saveData['resourceIds'];
            unset($saveData['resourceIds']);
        }

        $staffInfo = [];
        if( $id <= 0 ){
            if(!isset($saveData['admin_username']) || !isset($saveData['admin_password'])){
                throws('不能没有帐户信息！');
            }
            $staffInfo = [
                'admin_type' => 16,// 16店铺
                'city_site_id' => $saveData['city_site_id'] ?? 0,
                'city_partner_id' => $saveData['city_partner_id'] ?? 0,
                'seller_id' => $saveData['seller_id'] ?? 0,
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
            if( isset($saveData['mobile']) && StaffDBBusiness::judgeFieldExist($company_id, $id ,"mobile", $saveData['mobile'], [], 1)){
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
                if($id <= 0) $staffInfo['operate_staff_id_history'] = $operate_staff_id_history;
            }
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData);
                $id = $resultDatas['id'] ?? 0;

                // 添加帐户信息
                $staffInfo['shop_id'] = $id;
                // StaffDBBusiness::create($staffInfo);
                StaffDBBusiness::replaceById($staffInfo, $company_id, $staffId, $operate_staff_id, $modifAddOprate);
            }else{// 修改
                $modelObj = null;
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);

            }

            // 同步修改关系
            if($hasLabel){
                // 加入company_id字段
                $syncLabelArr = [];
                $temArr =  [
                    // 'company_id' => $company_id,
//                    'operate_staff_id' => $operate_staff_id,
//                    'operate_staff_id_history' => $operate_staff_id_history,
                ];
                // 加入操作人员信息
                static::addOprate($temArr, $operate_staff_id,$operate_staff_id_history);

                foreach($labelIds as $labelId){
                    $syncLabelArr[$labelId] = $temArr;
                }
                $syncParams =[
                    'labels' => $syncLabelArr,//标签
                ];
                static::sync($id, $syncParams);
            }

            // 同步修改图片资源关系
            if($hasResource){
                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
            }

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
}
