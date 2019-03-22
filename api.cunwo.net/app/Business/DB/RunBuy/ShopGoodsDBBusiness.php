<?php
// 店铺商品
namespace App\Business\DB\RunBuy;

use App\Services\Tool;
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

        // 是否有图片资源
        $hasResource = false;
        $resourceIds = [];
        if(isset($saveData['resourceIds'])){
            $hasResource = true;
            $resourceIds = $saveData['resourceIds'];
            unset($saveData['resourceIds']);
        }

        // 获得当前商品已有的价格属性
        $queryParams = [
            'where' => [
                ['goods_id', '=' , $id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id'
            ]
        ];
        // 获得已有的价格属性表id数组--一维
        $dataListPrice = ShopGoodsPricesDBBusiness::getList($queryParams,[]);
        $goodsPriceIds = array_column($dataListPrice->toArray(),'id');

        // 获得当前商品已有的属性
        $queryParams = [
            'where' => [
                ['goods_id', '=' , $id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id'
            ]
        ];
        // 获得已有的属性表id数组--一维
        $dataListProps = ShopGoodsPropsDBBusiness::getList($queryParams,[]);
        $goodsPropsIds = array_column($dataListProps->toArray(),'id');



        // 属性
        $propList = [];
        $hasProp = false;
        if(isset($saveData['prop_list'])){
            $hasProp = true;
            $propList = $saveData['prop_list'];
            unset($saveData['prop_list']);
        }
        // 属性值 属性价格数组 ['属性值id' => '属性值价格',....]
        $pvPrices = [];
        if(isset($saveData['pv_prices'])){
            $pvPrices = $saveData['pv_prices'];
            unset($saveData['pv_prices']);
        }

        DB::beginTransaction();
        try {
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
            // 属性
            if($hasProp ){
                $goodsProp = [];// 新加操作，要批量新加的商品属性
                $goodsPropPrice = [];// 新加操作，要批量新加的商品价格属性
                $modifyPropIds = [];// 修改操作过的商品属性id数组-- 一维数组
                $modifyPropPriceIds = [];// 修改操作过的商品属性价格id数组---一维数组

                if( !empty($propList) ){
                    // 获得属性记录
                    $propIds = array_unique(array_column($propList,'prop_id'));
                    $queryParams = [
                        'where' => [
                            // ['goods_id', '=' , $good_id],
                            // ['main_name', 'like', '' . $main_name . '%'],
                            // ['id', '&' , '16=16'],
                        ],
                        //'select' => [
                        //   'id', 'city_site_id', 'city_partner_id', 'seller_id', 'names_id', 'sort_num'
                        //],
                        // 'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],
                    ];
                    $queryParams['whereIn']['id'] = $propIds;

                    $propData = PropDBBusiness::getList($queryParams, []);
                    $propData = $propData->toArray();
                    // 格式化属性表记录
                    $formatProps = [];
                    foreach($propData as $v){
                        $formatProps[$v['id']] = $v;
                    }

                    // 获得属性值记录
                    $pvIdsArr = [];// 属性值id数组---维
                    foreach($propList as $v){
                        $temPVIds = trim($v['pv_ids']);  // 属性值id串 '1,2' 多个用,号分隔
                        if(empty($temPVIds)) continue;
                        $temArr = explode(',', $temPVIds);
                        $pvIdsArr = array_merge($pvIdsArr, $temArr);
                    }
                    $pvIdsArr = array_unique($pvIdsArr);

                    $queryParams = [
                        'where' => [
                            // ['goods_id', '=' , $good_id],
                            // ['main_name', 'like', '' . $main_name . '%'],
                            // ['id', '&' , '16=16'],
                        ],
                        // 'select' => [
                        //    'id', 'prop_id', 'names_id', 'sort_num'
                        //],
                        //'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],
                    ];
                    $queryParams['whereIn']['id'] = $pvIdsArr;
                    $propValList = PropValDBBusiness::getList($queryParams,[]);
                    $propValList = $propValList->toArray();
                    // 格式化属性值id  --一维数组
                    $formatPVList = [];
                    foreach($propValList as $v){
                        $formatPVList[$v['id']] = $v;
                    }

                    $propCount = count($propList);// 属性数量
                    $propValCount = count($pvIdsArr);// 属性值的数量

                    // 遍历属性
                    foreach($propList as $v){
                        $propId = $v['prop_id'];
                        $isPrices = $v['is_prices'] ?? 0;
                        $pvIds = $v['pv_ids'];  // 属性值id串 '1,2' 多个用,号分隔
                        if(empty($pvIds)) continue;
                        $temPropInfo = $formatProps[$propId] ?? [];
                        if(empty($temPropInfo)) continue;
                        $temProp = [
                            // 'is_prices' => $isPrices,
                            // 'is_multi' => $v['is_multi'],
                            // 'is_must' => $v['is_must'],
                            'city_site_id' => $city_site_id,
                            'city_partner_id' => $city_partner_id,
                            'seller_id' => $seller_id,
                            'shop_id' => $shop_id,
                            'goods_id' => $id,
                            'prop_id' => $propId,
                            'prop_names_id' => $temPropInfo['names_id'],
                            'operate_staff_id' => $operate_staff_id,
                            'operate_staff_id_history' => $operate_staff_id_history,
                            // 'sort_num' => $propCount--,
                        ];
                        if($isPrices == 1){// 价格属性
                            // $temProp = array_merge($temProp, [
//                            'aaaa' => $aaa,
                            // ]);
                        }else{
                            $temProp = array_merge($temProp, [
                                'is_multi' => $v['is_multi'],
                                'is_must' => $v['is_must'],
                            ]);
                        }
                        $temArr = explode(',', $pvIds);
                        if($isPrices == 1) $temArr = array_keys($pvPrices);// 价格属性 ，用价格的顺序
                        foreach($temArr as $temPVId){
                            $temPVInfo = $formatPVList[$temPVId] ?? [];
                            if(empty($temPVInfo)) continue;
                            $temPVArr = $temProp;
                            $temSortNum = $propValCount--;
                            if($temSortNum < 0) $temSortNum = 0;
                            $temPVArr = array_merge($temPVArr, [
                                'prop_val_id' => $temPVId,
                                'prop_val_names_id' => $temPVInfo['names_id'],
                                'sort_num' => $temSortNum,
                            ]);
                            if($isPrices == 1) {// 价格属性
                                $temPVArr['price'] = $pvPrices[$temPVId] ?? 0;
                                if(!$isModify) array_push($goodsPropPrice, $temPVArr); // 新加
                                if($isModify){// 更新
                                    $pvObj = null ;
                                    $searchConditon = [
                                        'goods_id' => $id,
                                        'prop_id' => $propId,
                                        'prop_val_id' => $temPVId,
                                    ];
                                    ShopGoodsPricesDBBusiness::updateOrCreate($pvObj, $searchConditon, $temPVArr);
                                    array_push($modifyPropPriceIds, $pvObj->id);
                                }
                            }else{
                                if(!$isModify) array_push($goodsProp, $temPVArr); // 新加
                                if($isModify){// 更新
                                    $pvObj = null ;
                                    $searchConditon = [
                                        'goods_id' => $id,
                                        'prop_id' => $propId,
                                        'prop_val_id' => $temPVId,
                                    ];
                                    ShopGoodsPropsDBBusiness::updateOrCreate($pvObj, $searchConditon, $temPVArr);
                                    array_push($modifyPropIds, $pvObj->id);
                                }
                            }
                        }

                    }
                }

                if($isModify){

                    // 删除多余的属性

                    // 价格属性
                    // 获得要删除的价格属性id
                    $delGoodsPriceIds = array_diff($goodsPriceIds, $modifyPropPriceIds);
                    if(!empty($delGoodsPriceIds)){
                        // 获得购物车的记录
                        $queryParams = [
                            'where' => [
                                ['goods_id', '=' , $id],
                                // ['main_name', 'like', '' . $main_name . '%'],
                                // ['id', '&' , '16=16'],
                            ],
                            'select' => [
                                'id'
                            ]
                        ];
                        $queryParams['whereIn']['prop_price_id'] = $delGoodsPriceIds;

                        // 获得已有的属性表id数组--一维
                        $dataListCarts = CartDBBusiness::getList($queryParams,[]);
                        $cartIds = array_column($dataListCarts->toArray(),'id');
                        if(!empty($cartIds)){
                            // 删除购物车商品属性
                            $queryParams = [
                                'where' => [
                                    //  ['id', '&' , '16=16'],
                                    // ['goods_id', $id],
                                    //['mobile', $keyword],
                                    //['admin_type',self::$admin_type],
                                ],
                                //    'whereIn' => [
                                //        'id' => $cityPids,
                                //    ],
                                //            'select' => [
                                //                'id','company_id','type_name','sort_num'
                                //                //,'operate_staff_id','operate_staff_id_history'
                                //                ,'created_at'
                                //            ],
                                // 'orderBy' => ['id'=>'desc'],
                            ];
                            // if(!empty($modifyPropIds))  $queryParams['whereNotIn']['id'] = $modifyPropIds;
                            $queryParams['whereIn']['cart_id'] = $cartIds;
                            CartGoodsPropsDBBusiness::del($queryParams);
                            // 删除购物车价格属性
                            $queryParams = [
                                'where' => [
                                    //  ['id', '&' , '16=16'],
                                    // ['goods_id', $id],
                                    //['mobile', $keyword],
                                    //['admin_type',self::$admin_type],
                                ],
                                //    'whereIn' => [
                                //        'id' => $cityPids,
                                //    ],
                                //            'select' => [
                                //                'id','company_id','type_name','sort_num'
                                //                //,'operate_staff_id','operate_staff_id_history'
                                //                ,'created_at'
                                //            ],
                                // 'orderBy' => ['id'=>'desc'],
                            ];
                            // if(!empty($modifyPropIds))  $queryParams['whereNotIn']['id'] = $modifyPropIds;
                            $queryParams['whereIn']['id'] = $cartIds;
                            CartDBBusiness::del($queryParams);
                        }


                        // 删除商品价格表记录
                        $queryParams = [
                            'where' => [
                                //  ['id', '&' , '16=16'],
                                ['goods_id', $id],
                                //['mobile', $keyword],
                                //['admin_type',self::$admin_type],
                            ],
                            // 'whereIn' => [
                            //     'id' => $cityPids,
                            // ],
                            //            'select' => [
                            //                'id','company_id','type_name','sort_num'
                            //                //,'operate_staff_id','operate_staff_id_history'
                            //                ,'created_at'
                            //            ],
                            // 'orderBy' => ['id'=>'desc'],
                        ];
                        // if(!empty($modifyPropPriceIds))  $queryParams['whereNotIn']['id'] = $modifyPropPriceIds;
                        $queryParams['whereIn']['id'] = $delGoodsPriceIds;
                        ShopGoodsPricesDBBusiness::del($queryParams);
                    }

                    // 商品属性操作
                    // 获得要删除的价格属性id
                    $delGoodsPropsIds = array_diff($goodsPropsIds, $modifyPropIds);
                    if(!empty($delGoodsPropsIds)){

                        // 获得购物车的记录
                        $queryParams = [
                            'where' => [
                                ['goods_id', '=' , $id],
                                // ['main_name', 'like', '' . $main_name . '%'],
                                // ['id', '&' , '16=16'],
                            ],
                            'select' => [
                                'id'
                            ]
                        ];
                        // 获得已有的属性表id数组--一维
                        $dataListCarts = CartDBBusiness::getList($queryParams,[]);
                        $cartIds = array_column($dataListCarts->toArray(),'id');
                        if(!empty($cartIds)){
                            // 删除购物车商品属性
                            $queryParams = [
                                'where' => [
                                    //  ['id', '&' , '16=16'],
                                    // ['goods_id', $id],
                                    //['mobile', $keyword],
                                    //['admin_type',self::$admin_type],
                                ],
                                //    'whereIn' => [
                                //        'id' => $cityPids,
                                //    ],
                                //            'select' => [
                                //                'id','company_id','type_name','sort_num'
                                //                //,'operate_staff_id','operate_staff_id_history'
                                //                ,'created_at'
                                //            ],
                                // 'orderBy' => ['id'=>'desc'],
                            ];
                            // if(!empty($modifyPropIds))  $queryParams['whereNotIn']['id'] = $modifyPropIds;
                            $queryParams['whereIn']['cart_id'] = $cartIds;
                            $queryParams['whereIn']['goods_props_id'] = $delGoodsPropsIds;
                            CartGoodsPropsDBBusiness::del($queryParams);
                        }

                        // 删除商品属性
                        $queryParams = [
                            'where' => [
                                //  ['id', '&' , '16=16'],
                                ['goods_id', $id],
                                //['mobile', $keyword],
                                //['admin_type',self::$admin_type],
                            ],
                           // 'whereIn' => [
                            //    'id' => $cityPids,
                           // ],
                            //            'select' => [
                            //                'id','company_id','type_name','sort_num'
                            //                //,'operate_staff_id','operate_staff_id_history'
                            //                ,'created_at'
                            //            ],
                            // 'orderBy' => ['id'=>'desc'],
                        ];
                        // if(!empty($modifyPropIds))  $queryParams['whereNotIn']['id'] = $modifyPropIds;
                        $queryParams['whereIn']['id'] = $delGoodsPropsIds;
                        ShopGoodsPropsDBBusiness::del($queryParams);
                    }
                }else{
                    if(!empty($goodsPropPrice))  ShopGoodsPricesDBBusiness::addBath($goodsPropPrice);
                    if(!empty($goodsProp))  ShopGoodsPropsDBBusiness::addBath($goodsProp);
                }
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

    /**
     * 获得商品及属性数据--根据商品id
     *
     * @param int  $company_id 企业id
     * @param int $good_id 商品id
     * @param int $operate_staff_id 操作人id
     * @return  array 商品及属性数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getPropIdsByKey($company_id, $good_id = 0, $operate_staff_id = 0){
        $goodInfo = [];
        // 获得当前商品信息
        $info = static::getInfo($good_id,['id'] );
        if(empty($info)) return $goodInfo;
        // 获得价格属性
        $queryParams = [
            'where' => [
                 ['goods_id', '=' , $good_id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'prop_id', 'prop_val_id', 'price'
            ],
            'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],
        ];
        $priceList = ShopGoodsPricesDBBusiness::getList($queryParams,[]);
        $priceList = $priceList->toArray(); // [{'prop_id': 17, 'prop_val_id': 57, 'price': 5.000, 'is_multi': 0, 'is_must': 1, 'is_price': 1 }]
        Tool::arrAppendKeys($priceList, ['is_multi' => 0, 'is_must' => 1, 'is_price' => 1]);

        // 获得属性
        $queryParams = [
            'where' => [
                ['goods_id', '=' , $good_id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'prop_id', 'prop_val_id', 'is_multi', 'is_must'
            ],
            'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],
        ];
        $propList = ShopGoodsPropsDBBusiness::getList($queryParams,[]);
        $propList = $propList->toArray();// [{'prop_id': 17, 'prop_val_id': 57, 'price': 0, 'is_multi': 0, 'is_must': 1, 'is_price': 0 }]
        Tool::arrAppendKeys($propList, ['price' => 0, 'is_price' => 0]);
        $propArr = array_merge($priceList, $propList);

        // $propIds = array_unique(array_column($propArr,'prop_id'));

        $formatProp = PropDBBusiness::getPropByIds($company_id, $propArr, '', $operate_staff_id);
        $goodInfo['propList'] = $formatProp;
        return $goodInfo;
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

        // 获得当前商品已有的价格属性
        $queryParams = [
            'where' => [
                ['goods_id', '=' , $id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id'
            ]
        ];
        // 获得已有的价格属性表id数组--一维
        $dataListPrice = ShopGoodsPricesDBBusiness::getList($queryParams,[]);
        $goodsPriceIds = array_column($dataListPrice->toArray(),'id');

        // 获得当前商品已有的属性
        $queryParams = [
            'where' => [
                ['goods_id', '=' , $id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id'
            ]
        ];
        // 获得已有的属性表id数组--一维
        $dataListProps = ShopGoodsPropsDBBusiness::getList($queryParams,[]);
        $goodsPropsIds = array_column($dataListProps->toArray(),'id');

        // 获得购物车的记录
        $queryParams = [
            'where' => [
                ['goods_id', '=' , $id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id'
            ]
        ];
        // $queryParams['whereIn']['prop_price_id'] = $goodsPriceIds;

        // 获得已有的属性表id数组--一维
        $dataListCarts = CartDBBusiness::getList($queryParams,[]);
        $cartIds = array_column($dataListCarts->toArray(),'id');


        DB::beginTransaction();
        try {

            if(!empty($cartIds)){
                // 删除购物车商品属性
                $queryParams = [
                    'where' => [
                        //  ['id', '&' , '16=16'],
                        // ['goods_id', $id],
                        //['mobile', $keyword],
                        //['admin_type',self::$admin_type],
                    ],
                    //    'whereIn' => [
                    //        'id' => $cityPids,
                    //    ],
                    //            'select' => [
                    //                'id','company_id','type_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_id_history'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => ['id'=>'desc'],
                ];
                // if(!empty($modifyPropIds))  $queryParams['whereNotIn']['id'] = $modifyPropIds;
                $queryParams['whereIn']['cart_id'] = $cartIds;
                CartGoodsPropsDBBusiness::del($queryParams);
                // 删除购物车价格属性
                $queryParams = [
                    'where' => [
                        //  ['id', '&' , '16=16'],
                        // ['goods_id', $id],
                        //['mobile', $keyword],
                        //['admin_type',self::$admin_type],
                    ],
                    //    'whereIn' => [
                    //        'id' => $cityPids,
                    //    ],
                    //            'select' => [
                    //                'id','company_id','type_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_id_history'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => ['id'=>'desc'],
                ];
                // if(!empty($modifyPropIds))  $queryParams['whereNotIn']['id'] = $modifyPropIds;
                $queryParams['whereIn']['id'] = $cartIds;
                CartDBBusiness::del($queryParams);
            }

            // 删除商品价格表记录
            if(!empty($goodsPriceIds)){
                $queryParams = [
                    'where' => [
                        //  ['id', '&' , '16=16'],
                        ['goods_id', $id],
                        //['mobile', $keyword],
                        //['admin_type',self::$admin_type],
                    ],
                    // 'whereIn' => [
                    //     'id' => $cityPids,
                    // ],
                    //            'select' => [
                    //                'id','company_id','type_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_id_history'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => ['id'=>'desc'],
                ];
                // if(!empty($modifyPropPriceIds))  $queryParams['whereNotIn']['id'] = $modifyPropPriceIds;
                $queryParams['whereIn']['id'] = $goodsPriceIds;
                ShopGoodsPricesDBBusiness::del($queryParams);
            }

            // 删除商品属性
            if(!empty($goodsPropsIds)){
                $queryParams = [
                    'where' => [
                        //  ['id', '&' , '16=16'],
                        ['goods_id', $id],
                        //['mobile', $keyword],
                        //['admin_type',self::$admin_type],
                    ],
                    // 'whereIn' => [
                    //    'id' => $cityPids,
                    // ],
                    //            'select' => [
                    //                'id','company_id','type_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_id_history'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => ['id'=>'desc'],
                ];
                // if(!empty($modifyPropIds))  $queryParams['whereNotIn']['id'] = $modifyPropIds;
                $queryParams['whereIn']['id'] = $goodsPropsIds;
                ShopGoodsPropsDBBusiness::del($queryParams);
            }
            // 删除商品
            static::deleteByIds($id);
        } catch ( \Exception $e) {
            DB::rollBack();
            throws('操作失败；信息[' . $e->getMessage() . ']');
            // throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

}
