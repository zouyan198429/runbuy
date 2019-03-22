<?php
// 购物车商品属性
namespace App\Business\DB\RunBuy;

use Illuminate\Support\Facades\DB;

/**
 *
 */
class CartGoodsPropsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\CartGoodsProps';
    public static $table_name = 'cart_goods_props';// 表名称

    /**
     * 根据属性id/属性值更新属性信息--名称id, $prop_val_id>0是修改属性值的
     *
     * @param array $saveData 要修改的数组 --一维数组
    [
    'prop_names_id' => $saveData['names_id']
    ]
     * @param int $names_id 名称id
     * @param int $prop_id 属性id
     * @param int $prop_val_id 属性值id , 可为0
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function bathModifyByProp($names_id, $prop_id, $prop_val_id = 0, $operate_staff_id = 0, &$operate_staff_id_history = 0)
    {
        DB::beginTransaction();
        try {
            $cartQueryParams = [
                'where' => [
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
                array_push($cartQueryParams['where'], ['prop_val_id', $prop_val_id]);
            }
            $saveData[$updateFeild] = $names_id;

            // 更新商品属性和商品属性值
            static::save($saveData, $cartQueryParams);
            // 更新版本号
//            $saveQueryParams['select'] = ['id'];// 只查询id字段
//            $listObj = static::getList($saveQueryParams, []);
//            foreach($listObj as $infoObj){
//                static::compareHistory($infoObj->id, 1);
//            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws('操作失败；信息[' . $e->getMessage() . ']');
            // throws($e->getMessage());
        }
        DB::commit();
        return true;
    }
}
