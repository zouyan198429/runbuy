<?php
// 店铺商品价格
namespace App\Business\DB\RunBuy;

/**
 *
 */
class ShopGoodsPricesDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\ShopGoodsPrices';
    public static $table_name = 'shop_goods_prices';// 表名称
    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, ShopGoodsPricesHistoryDBBusiness::$model_name
            , ShopGoodsPricesHistoryDBBusiness::$table_name, $historyDBObj, ['prop_price_id' => $mainId], []);
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
            'prop_price_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, ShopGoodsPricesHistoryDBBusiness::$model_name
            , ShopGoodsPricesHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['prop_price_id'], $forceIncVersion);
    }
}
