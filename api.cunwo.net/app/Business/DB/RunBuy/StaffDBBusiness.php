<?php
// 人员
namespace App\Business\DB\RunBuy;

use App\Services\Map\Map;
use Illuminate\Support\Facades\DB;
/**
 *
 */
class StaffDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Staff';
    public static $table_name = 'staff';// 表名称


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
            'staff_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, StaffHistoryDBBusiness::$model_name
            , StaffHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['staff_id'], $forceIncVersion);
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
            throws('真实姓名不能为空！');
        }

//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }

        if(isset($saveData['admin_username']) && empty($saveData['admin_username'])  ){
            throws('用户名不能为空！');
        }

        // 如果有经纬度信息
        if(isset($saveData['latitude'])){
            $latitude = $saveData['latitude'] ?? ''; // 纬度
            $longitude = $saveData['longitude'] ?? ''; // 经度
//            if($latitude == '' || $longitude == '' || ($latitude == '0' && $longitude == '0') ){
//                throws('经纬度不能为空！');
//            }
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
        // if( isset($saveData['mobile']) && (!empty($saveData['mobile'])) && static::judgeFieldExist($company_id, $id ,"mobile", $saveData['mobile'], [], 1)){
        //     throws('手机号已存在！');
        // }
        // 用户名
        if( isset($saveData['admin_username']) && static::judgeFieldExist($company_id, $id ,"admin_username", $saveData['admin_username'], [],1)){
            throws('用户名已存在！');
        }
        DB::beginTransaction();
        try {

            // 省id历史
            if( isset($saveData['province_id']) && $saveData['province_id'] > 0 ){
                $saveData['province_id_history'] = CityDBBusiness::getIdHistory($saveData['province_id']);
            }
            // 市id历史
            if( isset($saveData['city_id']) && $saveData['city_id'] > 0 ){
                $saveData['city_id_history'] = CityDBBusiness::getIdHistory($saveData['city_id']);
            }
            // 区县id历史
            if( isset($saveData['area_id']) && $saveData['area_id'] > 0 ){
                $saveData['area_id_history'] = CityDBBusiness::getIdHistory($saveData['area_id']);
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
                if($modifAddOprate) static::addOprate($saveData, $operate_staff_id);

            }else {// 新加;要加入的特别字段
    //            $addNewData = [
    //                'company_id' => $company_id,
    //            ];
    //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                static::addOprate($saveData, $operate_staff_id);
            }
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData);
                $id = $resultDatas['id'] ?? 0;
                $resultDatas = static::getInfo($id);
            }else{// 修改
                $modelObj = null;
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                $resultDatas = static::getInfo($id);

            }

            if($isModify){
                static::compareHistory($id, 1);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return $resultDatas;
    }

    /**
     * 小程序  根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceByIdWX($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0)
    {
        if (isset($paramsData['mini_openid']) && empty($paramsData['mini_openid'])) {
            throws('小程序 openid不能为空！');
        }

        // 查询存在的 mini_openid
        if(is_numeric($id) &&  $id <= 0 &&  isset($saveData['mini_openid']) ){
            $otherWhere = [];
            if( isset($saveData['wx_unionid']) ) array_push($otherWhere, ['wx_unionid', $saveData['wx_unionid']]);
            $info = static::judgeFieldExist($company_id, 0 ,"mini_openid", $saveData['mini_openid']
                , $otherWhere,2);
            if(!empty($info)) $id = $info['id'];
        }

        $res = static::replaceById($saveData, $company_id,$id, $operate_staff_id, $modifAddOprate);
        return $res;
    }
}
