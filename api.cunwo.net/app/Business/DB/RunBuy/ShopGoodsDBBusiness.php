<?php
// 店铺商品
namespace App\Business\DB\RunBuy;

use Illuminate\Support\Facades\DB;
/**
 *
 */
class ShopGoodsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\ShopGoods';
    public static $table_name = 'shop_goods';// 表名称
    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, ShopGoodsHistoryDBBusiness::$model_name
            , ShopGoodsHistoryDBBusiness::$table_name, $historyDBObj, ['goods_id' => $mainId], []);
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
            'goods_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, ShopGoodsHistoryDBBusiness::$model_name
            , ShopGoodsHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['goods_id'], $forceIncVersion);
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

        if(isset($saveData['goods_name']) && empty($saveData['goods_name'])  ){
            throws('商品名称不能为空！');
        }

        // 店铺id,获得 商家id
        $city_site_id = $saveData['city_site_id'] ?? 0;
        $city_partner_id = $saveData['city_partner_id'] ?? 0;
        $seller_id = $saveData['seller_id'] ?? 0;
        $shop_id = $saveData['shop_id'] ?? 0;
        if(is_numeric($shop_id) && $shop_id > 0 && ($city_site_id <= 0 || $city_partner_id <= 0 || $seller_id <= 0)){
            $shopInfo = ShopDBBusiness::getInfo($shop_id, ['city_site_id', 'city_partner_id', 'seller_id']);
            $city_site_id = $shopInfo['city_site_id'] ?? 0;
            $city_partner_id = $shopInfo['city_partner_id'] ?? 0;
            $seller_id = $shopInfo['seller_id'] ?? 0;
            $saveData['city_site_id'] = $city_site_id;
            $saveData['city_partner_id'] = $city_partner_id;
            $saveData['seller_id'] = $seller_id;
        }

        DB::beginTransaction();
        try {
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
            }
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $saveBoolen = static::saveById($saveData, $id);
                // $resultDatas = static::getInfo($id);

            }
            // 修改时，更新版本号
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
