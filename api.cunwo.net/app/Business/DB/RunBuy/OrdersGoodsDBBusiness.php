<?php
// 订单商品
namespace App\Business\DB\RunBuy;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class OrdersGoodsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\OrdersGoods';
    public static $table_name = 'orders_goods';// 表名称

    /**
     * 订单完成时订单商品处理
     *
     * @param $order_no  订单号,多个用逗号分隔
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_id_history 操作员工历史id
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function finishGoods($order_no, $operate_staff_id, $operate_staff_id_history){
        // 获得订单商品
        $queryGoodsParams = [
            'where' => [
                //  ['order_type', 4],// 订单类型1普通订单/父订单4子订单
                //  ['parent_order_no', $order_no],
                //  ['order_no', $orderObj->order_no],
            ],
            // 'select' => ['id']
        ];
        if (strpos($order_no, ',') === false) { // 单条
            array_push($queryGoodsParams['where'], ['order_no', $order_no]);
        } else {
            $queryGoodsParams['whereIn']['order_no'] = explode(',', $order_no);
        }

        $goodsList = OrdersGoodsDoingDBBusiness::getAllList($queryGoodsParams, []);

        DB::beginTransaction();
        try {
            $shopCountArr = [];// 店铺数量
            $goodCountArr = [];// 商品数量
            $goodPriceCountArr = [];// 商品价格数量
            foreach($goodsList as $goodInfoObj){
                // 商品统计
                CountOrdersDBBusiness::createCountGoods($goodInfoObj, $operate_staff_id , $operate_staff_id_history);
                // 修改总销量[店铺的、商品的、商品价格的]
                if(isset($shopCountArr[$goodInfoObj->shop_id])){
                    $shopCountArr[$goodInfoObj->shop_id] += $goodInfoObj->amount;
                }else{
                    $shopCountArr[$goodInfoObj->shop_id] = $goodInfoObj->amount;
                }

                if(isset($goodCountArr[$goodInfoObj->goods_id])){
                    $goodCountArr[$goodInfoObj->goods_id] += $goodInfoObj->amount;
                }else{
                    $goodCountArr[$goodInfoObj->goods_id] = $goodInfoObj->amount;
                }

                if($goodInfoObj->prop_price_id > 0){
                    if(isset($goodPriceCountArr[$goodInfoObj->prop_price_id])){
                        $goodPriceCountArr[$goodInfoObj->prop_price_id] += $goodInfoObj->amount;
                    }else{
                        $goodPriceCountArr[$goodInfoObj->prop_price_id] = $goodInfoObj->amount;
                    }
                }

            }
            // 店铺商品数量
            foreach($shopCountArr as $shop_id => $amount){
                ShopDBBusiness::updateSaleVolume($shop_id, $amount, $operate_staff_id , $operate_staff_id_history);
            }

            // 商品数量
            foreach($goodCountArr as $good_id => $amount){
                ShopGoodsDBBusiness::updateSaleVolume($good_id, $amount, $operate_staff_id , $operate_staff_id_history);
            }

            // 商品价格数量
            foreach($goodPriceCountArr as $prop_price_id => $amount){
                ShopGoodsPricesDBBusiness::updateSaleVolume($prop_price_id, $amount, $operate_staff_id , $operate_staff_id_history);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
    }
}
