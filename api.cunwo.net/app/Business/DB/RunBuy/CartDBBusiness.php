<?php
// 购物车
namespace App\Business\DB\RunBuy;


use Illuminate\Support\Facades\DB;

/**
 *
 */
class CartDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Cart';
    public static $table_name = 'cart';// 表名称

    /**
     * 如果id<=0 ；则必须有 参数 staff_id  city_site_id  goods_id  prop_price_id
     * id > 0; 则必须有 参数 id staff_id  city_site_id  goods_id  prop_price_id
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组  必要参数 ower_type , ower_id
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['staff_id']) && empty($saveData['staff_id'])  ){
            throws('用户id不能为空！');
        }

        if(isset($saveData['city_site_id']) && empty($saveData['city_site_id'])  ){
            throws('城市id不能为空！');
        }

//        if(isset($saveData['shop_id']) && empty($saveData['shop_id'])  ){
//            throws('店铺id不能为空！');
//        }

        if(isset($saveData['goods_id']) && empty($saveData['goods_id'])  ){
            throws('商品id不能为空！');
        }

        if(isset($saveData['prop_price_id']) && !is_numeric($saveData['prop_price_id'])  ){
            throws('价格属性id不能为空！');
        }

        if(isset($saveData['amount']) && !is_numeric($saveData['amount']) ){
            throws('商品数量不能为空！');
        }

        DB::beginTransaction();
        $amount = $saveData['amount'];
        if(!is_numeric($amount)) $amount = 0;

        try {
            $queryParams = [
                'where' => [
                    ['staff_id', $saveData['staff_id']],
                    ['city_site_id', $saveData['city_site_id']],
                    ['goods_id', $saveData['goods_id']],
                    ['prop_price_id', $saveData['prop_price_id'] ],
                ],
//            'select' => [
//                'id','title','sort_num','volume'
//                ,'operate_staff_id','operate_staff_id_history'
//                ,'created_at' ,'updated_at'
//            ],
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            if($id <= 0){
                $queryParams['select'] = ['id'];
                // 查询记录
                $info = static::getInfoByQuery(1, $queryParams, []);
                $id = $info['id'] ?? 0;
            }
            if($amount <= 0){// 删除记录
                if($id > 0){
//                    if(isset($queryParams['select'])) unset($queryParams['select']);
//                    static::del($queryParams);
                    // $id = 0;
                    // 删除属性表记录
                    $propQueryParams = [
                        'where' => [
                            ['cart_id', $id],
                        ],
                     ];
                    CartGoodsPropsDBBusiness::del($propQueryParams);
                    // 删除主表记录
                    static::deleteByIds($id);
                }
            }else{
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
                    // 根据商品id,获得相关信息
                    $goods_id = $saveData['goods_id'];
                    $shopInfo = ShopGoodsDBBusiness::getInfo($goods_id, ['city_site_id', 'city_partner_id', 'seller_id', 'shop_id', 'price_type', 'price']);
                    if(empty($shopInfo)) throws('商品信息不存在');
                    $saveData = array_merge($saveData,[
                        'city_site_id' => $shopInfo['city_site_id']
                        , 'city_partner_id' => $shopInfo['city_partner_id']
                        , 'seller_id' => $shopInfo['seller_id']
                        , 'shop_id' => $shopInfo['shop_id']
                    ]);
                    if($shopInfo['price_type'] == 1){
                        $saveData['prop_price_id'] = 0;
                        $saveData['price'] = $shopInfo['price'];
                    }else{
                        $prop_price_id = $saveData['prop_price_id'];
                        $goodPriceInfo = ShopGoodsPricesDBBusiness::getInfo($prop_price_id, ['price']);
                        if(empty($goodPriceInfo)) throws('商品价格属性不存在');
                        $saveData['price'] = $goodPriceInfo['price'];
                    }
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
                    $saveBoolen = static::saveById($saveData, $id,$modelObj);
                    // $resultDatas = static::getInfo($id);
                    // 修改数据，是否当前版本号 + 1
                    // static::compareHistory($id, 1);
                }
                if($isModify){
                    // static::compareHistory($id, 1);
                }
            }

        } catch ( \Exception $e) {
            DB::rollBack();
            throws('操作失败；信息[' . $e->getMessage() . ']');
            // throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

    /**
     * 如果shop_id <=0 ；则为清空购物车，必须有 参数 staff_id  city_site_id
     * 如果shop_id > 0; 则为清空指定的店铺，必须有 参数 id staff_id  city_site_id
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组  必要参数 ower_type , ower_id
     * @param int  $company_id 企业id
     * @param int $shop_id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function delByShopId($saveData, $company_id, &$shop_id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['staff_id']) && empty($saveData['staff_id'])  ){
            throws('用户id不能为空！');
        }

        if(isset($saveData['city_site_id']) && empty($saveData['city_site_id'])  ){
            throws('城市id不能为空！');
        }

        DB::beginTransaction();

        try {
            $queryParams = [
                'where' => [
                    ['staff_id', $saveData['staff_id']],
                    ['city_site_id', $saveData['city_site_id']],
                ],
//            'select' => [
//                'id','title','sort_num','volume'
//                ,'operate_staff_id','operate_staff_id_history'
//                ,'created_at' ,'updated_at'
//            ],
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            $queryParams['select'] = ['id'];
            if($shop_id > 0 )  array_push($queryParams['where'], ['shop_id', '=', $shop_id]);
            $cartList = static::getAllList($queryParams, '')->toArray();
            if( !empty($cartList) ){
                $cartIdsArr = array_column($cartList,'id');

                // 删除属性表记录
                $propQueryParams = [
                    'whereIn' => [
                        'cart_id' => $cartIdsArr,
                    ]
                ];
                CartGoodsPropsDBBusiness::del($propQueryParams);
                // 删除主表记录
                static::deleteByIds(implode(',',$cartIdsArr));
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws('操作失败；信息[' . $e->getMessage() . ']');
            // throws($e->getMessage());
        }
        DB::commit();
        return $shop_id;
    }

    /**
     * 如果 good_prop_table_id 0 ；则为清空当前属性及值，必须有 参数 staff_id  city_site_id cart_id
     * 如果 good_prop_table_id 多个逗号分隔; 则为设置为指定的属性值，必须有 参数 id staff_id  city_site_id  cart_id
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组  必要参数 ower_type , ower_id
     * @param int  $company_id 企业id
     * @param int $prop_id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function saveProps($saveData, $company_id, &$prop_id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['staff_id']) && empty($saveData['staff_id'])  ){
            throws('用户id不能为空！');
        }

        if(isset($saveData['city_site_id']) && empty($saveData['city_site_id'])  ){
            throws('城市id不能为空！');
        }

        if (isset($saveData['cart_id']) && empty($saveData['cart_id'])) {
            throws('购物车编号不能为空！');
        }

        $good_prop_table_id = $saveData['good_prop_table_id'] ?? 0;


        DB::beginTransaction();

        try {
            if($good_prop_table_id == 0 || $good_prop_table_id == '0'){// 清除所有属性下的，属性值
                $delParams = [
                    'where' => [
                        ['cart_id', $saveData['cart_id']],
                        ['prop_id', $prop_id],
                    ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
                CartGoodsPropsDBBusiness::del($delParams);
            }else{
                // 获得购物车记录
                $cartInfo = static::getInfo($saveData['cart_id'], ['goods_id']);
                if(empty($cartInfo)) throws('购物车中商品记录不存在');
                $goods_id = $cartInfo['goods_id'];

                // 获得所有的商品属性值表-- 前端给过来的
                $queryParams = [
                    'where' => [
                        ['goods_id', $goods_id],
                        ['prop_id', $prop_id],
                    ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
                if (strpos($good_prop_table_id, ',') === false) { // 单条
                    array_push($queryParams['where'], ['id', $good_prop_table_id]);
                } else {
                    $queryParams['whereIn']['id'] = explode(',', $good_prop_table_id);
                }
                $goodPropsList = ShopGoodsPropsDBBusiness::getAllList($queryParams, '')->toArray();

                // 获得当前已有的属性值
                $queryParams = [
                    'where' => [
                        ['cart_id', $saveData['cart_id']],
                        ['prop_id', $prop_id],
                    ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
//                if (strpos($good_prop_table_id, ',') === false) { // 单条
//                    array_push($queryParams['where'], ['goods_props_id', $good_prop_table_id]);
//                } else {
//                    $queryParams['whereIn']['goods_props_id'] = explode(',', $good_prop_table_id);
//                }
                $cartPropsList = CartGoodsPropsDBBusiness::getAllList($queryParams, '')->toArray();
                $hasCartGoodPropTableIds = array_column($cartPropsList,'goods_props_id');

                $addCartProps = [];// 要新加的商品属性
                $needOldGoodPropTableIds = [];// 表中已有的商品属性
                // 遍历商品属性
                $operate_staff_id_history = 0;
                foreach($goodPropsList as $k => $v){
                    $tem_goods_props_id = $v['id'];
                    $temSaveData = [
                        'cart_id' => $saveData['cart_id'],
                        'goods_props_id' => $tem_goods_props_id,
                        'prop_id' => $v['prop_id'],
                        'prop_val_id' => $v['prop_val_id'],
                        'prop_names_id' => $v['prop_names_id'],
                        'prop_val_names_id' => $v['prop_val_names_id'],
                        'operate_staff_id' => $operate_staff_id,
                        // 'operate_staff_id_history' => $v['aaa'],
                    ];
                    if(in_array($tem_goods_props_id, $hasCartGoodPropTableIds)){ // 已经存在
                        // 更新
                        $saveQueryParams = [
                            'where' => [
                                ['cart_id', $saveData['cart_id']],
                                ['prop_id', $prop_id],
                                ['goods_props_id', $tem_goods_props_id],
                            ],
//                            'select' => [
//                                'id','title','sort_num','volume'
//                                ,'operate_staff_id','operate_staff_id_history'
//                                ,'created_at' ,'updated_at'
//                            ],
                            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                        ];
                        CartGoodsPropsDBBusiness::save($temSaveData, $saveQueryParams);
                        array_push($needOldGoodPropTableIds, $tem_goods_props_id);
                        continue;
                    }
                    static::addOprate($temSaveData, $operate_staff_id,$operate_staff_id_history);
                    array_push($addCartProps, $temSaveData);
                }
                if(!empty($addCartProps))  CartGoodsPropsDBBusiness::addBath($addCartProps);// 批量添加
                // 删除多余记录
                $delCartGoodPropTableIDS = array_diff($hasCartGoodPropTableIds, $needOldGoodPropTableIds);
                if(!empty($delCartGoodPropTableIDS)){
                    $delParams = [
                        'where' => [
                            ['cart_id', $saveData['cart_id']],
                            ['prop_id', $prop_id],
                        ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                        //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                    ];
                    $delParams['whereIn']['goods_props_id'] = $delCartGoodPropTableIDS;
                    CartGoodsPropsDBBusiness::del($delParams);
                }

            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws('操作失败；信息[' . $e->getMessage() . ']');
            // throws($e->getMessage());
        }
        DB::commit();
        return $prop_id;
    }
}
