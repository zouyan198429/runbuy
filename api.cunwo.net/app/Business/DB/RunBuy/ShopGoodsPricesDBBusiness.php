<?php
// 店铺商品价格
namespace App\Business\DB\RunBuy;

use Illuminate\Support\Facades\DB;

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


    /**
     * 根据属性id/属性值更新属性信息--名称id, $prop_val_id>0是修改属性值的
     *
     * @param array $saveData 要修改的数组 --一维数组
    [
    'prop_names_id' => $saveData['names_id']
    ]
     * @param int $names_id 名称id
     * @param int $seller_id 商家ID
     * @param int $shop_id 店铺ID
     * @param int $prop_id 属性id
     * @param int $prop_val_id 属性值id , 可为0
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function bathModifyByProp($names_id, $seller_id, $shop_id, $prop_id, $prop_val_id = 0, $operate_staff_id = 0, &$operate_staff_id_history = 0)
    {
        DB::beginTransaction();
        try {
            $saveQueryParams = [
                'where' => [
                    ['seller_id', $seller_id],
                    ['shop_id', $shop_id],
                    ['prop_id', $prop_id],
                ],
    //                            'select' => [
    //                                'id','title','sort_num','volume'
    //                                ,'operate_staff_id','operate_staff_id_history'
    //                                ,'created_at' ,'updated_at'
    //                            ],
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            $saveData = [];
            static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history);

            $updateFeild = 'prop_names_id';
            if($prop_val_id > 0){
                $updateFeild = 'prop_val_names_id';
                array_push($saveQueryParams['where'], ['prop_val_id', $prop_val_id]);
            }
            $saveData[$updateFeild] = $names_id;

            // 更新商品属性和商品属性值
            static::save($saveData, $saveQueryParams);
            // 更新版本号
            $saveQueryParams['select'] = ['id'];// 只查询id字段
            $listObj = static::getList($saveQueryParams, []);
            foreach($listObj as $infoObj){
                static::compareHistory($infoObj->id, 1);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return true;
    }

    /**
     * 订单完成统计商品
     *
     * @param int $prop_price_id 商品价格id
     * @param int $amount 商品的数量
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_id_history 操作员工历史id
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function updateSaleVolume($prop_price_id, $amount, $operate_staff_id , $operate_staff_id_history){
        $infoObj = static::getInfo($prop_price_id);
        if(!empty($infoObj)){
            $infoObj->sales_volume += $amount;
            $infoObj->operate_staff_id = $operate_staff_id;
            $infoObj->operate_staff_id_history = $operate_staff_id_history;
            $infoObj->save();
        }
    }

    /**
     * 跑城市店铺商品价格月销量最近30天脚本
     *
     * @param string $beginDateTime 开始时间 格式 'Y-m-d'
     * @param string $endDateTime 结束时间 格式 'Y-m-d'
     * @param int $city_site_id 城市id
    //     * @param string $status 多个用逗号分隔 状态0待审核1审核通过2审核未通过4冻结(禁用)
    //     * @param string $status_business   多个用逗号分隔 经营状态  1营业中 2 歇业中 4 停业[店铺人工操作 8  关业[店铺平台操作]
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function autoShopGoodsPriceSalesVolume($beginDateTime = '', $endDateTime = '', $city_site_id = 0){ //, $status = '1', $status_business = '1,2'

        $selectArr = ['prop_price_id'];// ['city_site_id', 'city_partner_id', 'seller_id', 'shop_id'];
        $countOrderWhere = [
            ['city_site_id', '=', $city_site_id],
            // ['city_partner_id', '=', $v['city_partner_id']],
            // ['seller_id', '=', $v['seller_id']],
            //  ['shop_id', '=', $shop_id],
        ];
        $countOrderList = CountOrdersDBBusiness::getCountByGroupBy($selectArr, 'prop_price_id', $countOrderWhere, $beginDateTime, $endDateTime)->toArray();
        // 返回二维数组 [ ['prop_price_id' => 3, 'amount_count' => 4, 'total_amount' => 32.000] ,...]
        $formatCountOrders = [];
        foreach($countOrderList as $k => $v){
            if($v['prop_price_id'] <= 0) continue;
            $formatCountOrders[$v['prop_price_id']] = $v;
        }
        // 获得当前城市的店铺商品信息
        $queryParams = [
            'where' => [
                //  ['city_site_id', '=', $city_site_id],
            ],
            'select' => ['id', 'mon_sales_volume', 'mon_total_price'],// ['id', 'city_site_id', 'city_partner_id', 'seller_id'],
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        if($city_site_id > 0 )  array_push($queryParams['where'], ['city_site_id', '=', $city_site_id]);
//        if(!empty($status)){
//            if (strpos($status, ',') === false) { // 单条
//                array_push($queryParams['where'], ['status', $status]);
//            } else {
//                $queryParams['whereIn']['status'] = explode(',', $status);
//            }
//        }
//
//        if(!empty($status_business)){
//            if (strpos($status_business, ',') === false) { // 单条
//                array_push($queryParams['where'], ['status_business', $status_business]);
//            } else {
//                $queryParams['whereIn']['status_business'] = explode(',', $status_business);
//            }
//
//        }

        $dataList = static::getAllList($queryParams, '')->toArray();

        $dataUdateMon = [];
        foreach($dataList as $v){
            $prop_price_id = $v['id'];
            $temShopCountInfo =$formatCountOrders[$prop_price_id] ?? [
                    'prop_price_id' => $prop_price_id,
                    'amount_count' => 0,
                    'total_amount' => 0
                ];

            if($v['mon_sales_volume'] != $temShopCountInfo['amount_count'] || $v['mon_total_price'] != $temShopCountInfo['total_amount']){
                array_push($dataUdateMon, [
                    'id' => $prop_price_id,
                    'mon_sales_volume' => $temShopCountInfo['amount_count'],
                    'mon_total_price' => $temShopCountInfo['total_amount']
                ]);
            }
        }
        if(empty($dataUdateMon)) return true;
        // 批量更新店铺销量
        static::saveBathById($dataUdateMon, 'id');
    }

}
