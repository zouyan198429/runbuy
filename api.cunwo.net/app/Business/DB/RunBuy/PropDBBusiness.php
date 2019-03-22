<?php
// 属性
namespace App\Business\DB\RunBuy;

use Illuminate\Support\Facades\DB;
/**
 *
 */
class PropDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Prop';
    public static $table_name = 'prop';// 表名称

    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, PropHistoryDBBusiness::$model_name
            , PropHistoryDBBusiness::$table_name, $historyDBObj, ['prop_id' => $mainId], []);
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
            'prop_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, PropHistoryDBBusiness::$model_name
            , PropHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['prop_id'], $forceIncVersion);
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

        if(isset($saveData['main_name']) && empty($saveData['main_name'])  ){
            throws('属性名称不能为空！');
        }

        $propVals = $saveData['prop_vals'] ?? [];// 新加的 ['属性值',....]
        $prop_vids = $saveData['prop_vids'] ?? [];// 修改已有的 二维数组 [['pv_id' => '属性值id', 'pv_val' => '属性值名称'],....]
        if(empty($propVals) && empty($prop_vids) ) throws('属性值不能为空！');
        if(isset($saveData['prop_vals']))  unset($saveData['prop_vals']);
        if(isset($saveData['prop_vids']))  unset($saveData['prop_vids']);
        $propVals = array_unique($propVals);
        // 商家id,获得 城市代理id 、城市分站id
//        $city_site_id = $saveData['city_site_id'] ?? 0;
//        $city_partner_id = $saveData['city_partner_id'] ?? 0;
//        $seller_id = $saveData['seller_id'] ?? 0;
//        if(is_numeric($seller_id) && $seller_id > 0 && ($city_site_id <= 0 || $city_partner_id <= 0)){
//            $sellerInfo = SellerDBBusiness::getInfo($seller_id, ['city_site_id', 'city_partner_id']);
//            $city_site_id = $sellerInfo['city_site_id'] ?? 0;
//            $city_partner_id = $sellerInfo['city_partner_id'] ?? 0;
//            $saveData['city_site_id'] = $city_site_id;
//            $saveData['city_partner_id'] = $city_partner_id;
//        }

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
            $operate_staff_id_history = 0;

            $main_name = $saveData['main_name'] ?? '';
            $saveData['names_id'] = NamesDBBusiness::getNameId($main_name, $operate_staff_id, $operate_staff_id_history);
            if(isset($saveData['main_name'])) unset($saveData['main_name']);

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

                // 批量添加属性值
                if(!empty($prop_vids)){// 修改已有的 二维数组 [['pv_id' => '属性值id', 'pv_val' => '属性值名称'],....]
                    $temPropNames = array_column($prop_vids, 'pv_val');
                    $propVals = array_unique(array_merge($propVals, $temPropNames));
                }
                $pvArr = [];
                $pvCount = count($propVals);
                foreach($propVals as $pvName){
                    if($pvName == '') continue;
                    $pv_names_id= NamesDBBusiness::getNameId($pvName, $operate_staff_id, $operate_staff_id_history);
                    array_push($pvArr, [
                        'prop_id' => $id,
                        'names_id' => $pv_names_id,
                        'sort_num' => $pvCount--,
                    ]);
                }
                PropValDBBusiness::addOprate($pvArr, $operate_staff_id,$operate_staff_id_history);
                PropValDBBusiness::addBath($pvArr);


            }else{// 修改
                $propInfo = static::getInfo($id, ['id', 'names_id']);
                $modelObj = null;
                $saveBoolen = static::saveById($saveData, $id, $modelObj);
                if($propInfo['names_id'] != $saveData['names_id']){// 属性名称有变化

                    $saveQueryParams = [
                        'where' => [
                            ['seller_id', $saveData['seller_id']],
                            ['shop_id', $saveData['shop_id']],
                            ['prop_id', $id],
                        ],
//                            'select' => [
//                                'id','title','sort_num','volume'
//                                ,'operate_staff_id','operate_staff_id_history'
//                                ,'created_at' ,'updated_at'
//                            ],
                        //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                    ];
                    // 更新商品属性和商品属性值
//                    ShopGoodsPropsDBBusiness::save([
//                        'prop_names_id' => $saveData['names_id']
//                    ], $saveQueryParams);
//                    ShopGoodsPricesDBBusiness::save([
//                        'prop_names_id' => $saveData['names_id']
//                    ], $saveQueryParams);

                    ShopGoodsPropsDBBusiness::bathModifyByProp($saveData['names_id'], $seller_id, $shop_id, $id, 0, $operate_staff_id, $operate_staff_id_history);
                    ShopGoodsPricesDBBusiness::bathModifyByProp($saveData['names_id'], $seller_id, $shop_id, $id, 0, $operate_staff_id, $operate_staff_id_history);

                    // 更新购物车属性表值
//                    $saveQueryParams = [
//                        'where' => [
//                            ['prop_id', $id],
//                        ],
////                            'select' => [
////                                'id','title','sort_num','volume'
////                                ,'operate_staff_id','operate_staff_id_history'
////                                ,'created_at' ,'updated_at'
////                            ],
//                        //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
//                    ];
//                    CartGoodsPropsDBBusiness::save([
//                        'prop_names_id' => $saveData['names_id']
//                    ], $saveQueryParams);
                }
                // 获得所有的属性值id
                // 获得使用此属性值的商品
                $queryParams = [
                    'where' => [
                        ['prop_id', '=' , $id],
                        // ['main_name', 'like', '' . $main_name . '%'],
                        // ['id', '&' , '16=16'],
                    ],
                    'select' => [
                        'id'
                    ]
                ];
                // 获得已有的属性值id数组--一维
                $dataListPV = PropValDBBusiness::getList($queryParams,[]);
                $pvIds = array_column($dataListPV->toArray(),'id');


                $pvIdArr = [];
                $pvidsCount = count($prop_vids);// 修改已有的 二维数组 [['pv_id' => '属性值id', 'pv_val' => '属性值名称'],....]
                // 批量添加属性值---新加
                $pvCount = count($propVals) + $pvidsCount;
                foreach($prop_vids as $v){
                    $pv_val = trim($v['pv_val']);
                    $pv_id = trim($v['pv_id']);
                    if($pv_val == '' ) continue;// || (!is_numeric($pv_id))
                    $pv_names_id= NamesDBBusiness::getNameId($pv_val, $operate_staff_id, $operate_staff_id_history);
                    $temPv = [
                        'prop_id' => $id,
                        'names_id' => $pv_names_id,
                        'sort_num' => $pvCount--,
                    ];
                    PropValDBBusiness::addOprate($temPv, $operate_staff_id,$operate_staff_id_history);

                    $pvObj = null;
                    if($pv_id > 0){// 更新
                        $saveBoolen = PropValDBBusiness::saveById($temPv, $pv_id,$pvObj);

                        ShopGoodsPropsDBBusiness::bathModifyByProp($pv_names_id, $seller_id, $shop_id, $id, $pv_id, $operate_staff_id, $operate_staff_id_history);
                        ShopGoodsPricesDBBusiness::bathModifyByProp($pv_names_id, $seller_id, $shop_id, $id, $pv_id, $operate_staff_id, $operate_staff_id_history);

                        array_push($pvIdArr, $pv_id);
                        // 修改时，更新版本号
                        //if($isModify){
                        PropValDBBusiness::compareHistory($pv_id, 1);
                        //}
                    }else{// 新加/更新
                        $searchConditon = [
                            // 'id' => $pv_id,
                            'prop_id' => $id,
                            'names_id' => $pv_names_id,
                        ];
                        PropValDBBusiness::updateOrCreate($pvObj, $searchConditon, $temPv);
                        array_push($pvIdArr, $pvObj->id);
                        // 修改时，更新版本号
                        //if($isModify){
                        PropValDBBusiness::compareHistory($pvObj->id, 1);
                        //}
                    }
                }

                // 新加
                foreach($propVals as $pvName){
                    if($pvName == '') continue;
                    $pv_names_id= NamesDBBusiness::getNameId($pvName, $operate_staff_id, $operate_staff_id_history);
                    $temPv = [
                        'prop_id' => $id,
                        'names_id' => $pv_names_id,
                        'sort_num' => $pvCount--,
                    ];
                    PropValDBBusiness::addOprate($temPv, $operate_staff_id,$operate_staff_id_history);
                    $pvObj = null ;
                    $searchConditon = [
                        'prop_id' => $id,
                        'names_id' => $pv_names_id,
                    ];
                    PropValDBBusiness::updateOrCreate($pvObj, $searchConditon, $temPv);
                    array_push($pvIdArr, $pvObj->id);

                    // 修改时，更新版本号
                    //if($isModify){
                        PropValDBBusiness::compareHistory($pvObj->id, 1);
                    //}
                }
                // 获得要删除的属性值id
                $delPvIds = array_diff($pvIds, $pvIdArr);
                if(!empty($delPvIds)){
                    // 判断是否有商品正在使用要删除的属性值
                    foreach($delPvIds as $tem_pv_id){
                        $usedGoods = static::judgePvIdUsed( $company_id, $tem_pv_id, $operate_staff_id, 0);
                        if(!empty($usedGoods)) throws('商品Id[' . implode(',', $usedGoods) . ']正在使用此属性值，请先处理商品属性再删除。');
                    }

                    // 删除多余的属性值
                    $queryParams = [
                        'where' => [
                            //  ['id', '&' , '16=16'],
                            ['prop_id', $id],
                            //['mobile', $keyword],
                            //['admin_type',self::$admin_type],
                        ],
//                    'whereIn' => [
//                        'id' => $cityPids,
//                    ],
                        //            'select' => [
                        //                'id','company_id','type_name','sort_num'
                        //                //,'operate_staff_id','operate_staff_id_history'
                        //                ,'created_at'
                        //            ],
                        // 'orderBy' => ['id'=>'desc'],
                    ];
                    // if(!empty($pvIdArr))  $queryParams['whereNotIn']['id'] = $pvIdArr;
                    $queryParams['whereIn']['id'] = $delPvIds;
                    PropValDBBusiness::del($queryParams);
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
        DB::beginTransaction();
        try {
            // 获得所有的属性值
            // 获得所有的属性值id
            // 获得使用此属性值的商品
            $queryParams = [
                'where' => [
                    ['prop_id', '=' , $id],
                    // ['main_name', 'like', '' . $main_name . '%'],
                    // ['id', '&' , '16=16'],
                ],
                'select' => [
                    'id'
                ]
            ];
            // 获得已有的属性值id数组--一维
            $dataListPV = PropValDBBusiness::getList($queryParams,[]);
            $pvIds = array_column($dataListPV->toArray(),'id');
            if(!empty($pvIds)){
                // 判断是否有商品正在使用要删除的属性值
                foreach($pvIds as $tem_pv_id){
                    $usedGoods = static::judgePvIdUsed( $company_id, $tem_pv_id, $operate_staff_id, 0);
                    if(!empty($usedGoods)) throws('商品Id[' . implode(',', $usedGoods) . ']正在使用此属性值，请先处理商品属性再删除。');
                }

                // 删除属性值
                $queryParams = [
                    'where' => [
                        //  ['id', '&' , '16=16'],
                        ['prop_id', $id],
                        //['mobile', $keyword],
                        //['admin_type',self::$admin_type],
                    ],
    //                    'whereIn' => [
    //                        'id' => $cityPids,
    //                    ],
                    //            'select' => [
                    //                'id','company_id','type_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_id_history'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => ['id'=>'desc'],
                ];
                PropValDBBusiness::del($queryParams);
            }
            // 删除属性
            static::deleteByIds($id);
        } catch ( \Exception $e) {
            DB::rollBack();
            throws('操作失败；信息[' . $e->getMessage() . ']');
            // throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

    /**
     * 根据属性名称，获得名称id数组
     *
     * @param int  $company_id 企业id
     * @param string $main_name 属性名称
     * @param int $operate_staff_id 操作人id
     * @return  mixed 名称id数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getNameIdsByKey($company_id, $main_name = '', $operate_staff_id = 0){
        $nameIds = [];
        if(strlen($main_name) <= 0) return $nameIds;
        // 获得子级城市信息
        $queryParams = [
            'where' => [
                // ['parent_id', '=' , $id],
                ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id'
            ]
        ];
        $dataList = NamesDBBusiness::getList($queryParams,[]);
        $nameIds = array_column($dataList->toArray(),'id');
        return array_unique($nameIds);
    }

    /**
     * 根据属性值名称，获得属id数组
     *
     * @param int  $company_id 企业id
     * @param string $main_name 属性值名称
     * @param int $operate_staff_id 操作人id
     * @return  mixed 名称id数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getPropIdsByKey($company_id, $main_name = '', $operate_staff_id = 0){
        $propIds = [];
        if(strlen($main_name) <= 0) return $propIds;
        // 获得子级城市信息
        $nameIds = static::getNameIdsByKey($company_id, $main_name, $operate_staff_id);
        if(empty($nameIds) ) return  $propIds;
        // 获得子级城市信息
        $queryParams = [
            'where' => [
                // ['parent_id', '=' , $id],
               // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'prop_id'
            ]
        ];
        $queryParams['whereIn']['names_id'] = $nameIds;
        $dataList = PropValDBBusiness::getList($queryParams,[]);
        $propIds = array_column($dataList->toArray(),'prop_id');
        return array_unique($propIds);
    }

    /**
     * 获得商品及属性数据--根据属性id
     *
     * @param int  $company_id 企业id
     * @param array $propArr 商品属性 可为空,为空时 $propIds 不能为空;  二维数组 [['prop_id' => , 'prop_val_id' => , 'is_multi' => , 'is_must' => , 'is_price' => , 'price' => ]]
     * @param string  $propIds 属性id id,多个,号分隔 1,2  可为空,为空 ，从
     * @param int $operate_staff_id 操作人id
     * @return  array 商品及属性数据 -- 二维数组
    [
    [
    'id' => '', 'city_site_id' => '', 'city_partner_id' => '', 'seller_id' => '', 'names_id' => '', 'sort_num' => '', 'main_name' => '', 'now_prop' => '', 'id_history' => ''
    , 'is_multi' => '', 'is_must' => '', 'is_price' => '', 'selected_pv_ids' => []
    , 'pv_list' => ['prop_id', 'names_id', 'sort_num', 'main_name', 'selected']
    ],....
    ]
     * @author zouyan(305463219@qq.com)
     */
    public static function getPropByIds($company_id, $propArr = [], $propIds = '', $operate_staff_id = 0){
        $formatProp = [];

        $propIdArr = explode(',', $propIds);// 属性id数组:[1，2，3]
        if(empty($propIds)) {
            $propIdArr = array_unique(array_column($propArr,'prop_id'));
            $propIds = implode(',', $propIdArr);// 属性id字符串:1,2,3...
        }

        if(empty($propIds)) return $formatProp;// 没有属性id数组，则返回空

        // 已有的属性格式化
        $formatGoodProp = [];// 格式化的商品属性 {属性id:{['prop_id' => , 'prop_val_id' => , 'is_multi' => , 'is_must' => , 'is_price' => , 'price' => ],...}}
        $formatPV = [];// 属性值数组// 格式化的商品属性值 {'属性值id':{}}
        foreach($propArr as $k => $v){
            $formatGoodProp[$v['prop_id']][] = $v;
            $formatPV[$v['prop_val_id']] = $v;
        }


        // 获得属性
        $queryParams = [
            'where' => [
                // ['goods_id', '=' , $good_id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id', 'city_site_id', 'city_partner_id', 'seller_id', 'names_id', 'sort_num', 'version_num'
            ],
            // 'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],
        ];
        if(empty($propArr))  $queryParams['orderBy'] = ['sort_num'=>'desc', 'id'=>'desc'];
        // $queryParams['whereIn']['id'] = $propIds;
        if (strpos($propIds, ',') === false) { // 单条
            array_push($queryParams['where'],['id', $propIds]);
        }else{
            $queryParams['whereIn']['id'] = $propIdArr;
        }

        $propData = PropDBBusiness::getList($queryParams,['name']);
        $propData = $propData->toArray();
        // 属性: [{id:属性id,city_site_id:属性城市id,city_partner_id:属性代理id,seller_id;属性商家id,names_id:属性名称id,sort_num：序号,version_num:版本号},...]
        foreach($propData as $k => $v){
            // 属性名称
            $v['main_name'] = $v['name']['main_name'] ?? '';
            // $v['name_id'] = $v['name']['id'] ?? 0;
            if(isset($v['name'])) unset($v['name']);

            $historyVersion = $v['prop_history']['version_num'] ?? '';
            if(isset($v['prop_history'])) unset($v['prop_history']);
            $version = $v['version_num'] ?? '';
            $now_prop = 0;
//            if($version === ''){
//                $now_prop = 1;
//            }elseif($historyVersion != $version){
//                $now_prop = 2;
//            }
            $v['now_prop'] = $now_prop;//  0没有变化 ;1 已经删除  2 不同
            $temGoodArr = $formatGoodProp[$v['id']] ?? [];// 当前属性使用的属性值{['prop_id' => , 'prop_val_id' => , 'is_multi' => , 'is_must' => , 'is_price' => , 'price' => ],...}
            $propValIds = array_unique(array_column($temGoodArr,'prop_val_id'));// 属性值id数组
            $v = array_merge($v,[
                'is_multi' => $temGoodArr[0]['is_multi'] ?? 0
                , 'is_must' => $temGoodArr[0]['is_must'] ?? 0
                , 'is_price' => $temGoodArr[0]['is_price'] ?? 0
                , 'id_history' => 0
                , 'selected_pv_ids' => $propValIds // 属性值id 数组
                , 'pv_ids' => implode(',', $propValIds) // 属性值id 字符串
            ]);
            $formatProp[$v['id']] = $v; // 属性格式 {属性id：{属性数据}}
        }

        if(!empty($propArr)){// 只要已选的属性
            $temProp = [];
            // 属性id数组:[1，2，3]
            foreach($propIdArr as $t_prop_id){// 遍历属性id, 去掉没有在属性表中的属性
                $temFormatProp = $formatProp[$t_prop_id] ?? [];
                if(empty($temFormatProp)) continue;
                $temProp[$t_prop_id] = $temFormatProp;
            }
            $formatProp = $temProp;
        }

        // 获得属性值
        $queryParams = [
            'where' => [
                // ['goods_id', '=' , $good_id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id', 'prop_id', 'names_id', 'sort_num'
            ],
            // 'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],
        ];
        if(empty($propArr))  $queryParams['orderBy'] = ['sort_num'=>'desc', 'id'=>'desc'];
        // $queryParams['whereIn']['prop_id'] = $propIds;
        if (strpos($propIds, ',') === false) { // 单条
            array_push($queryParams['where'],['prop_id', $propIds]);
        }else{
            $queryParams['whereIn']['prop_id'] = $propIdArr;
        }
        $propValList = PropValDBBusiness::getList($queryParams,['name']);
        $propValList = $propValList->toArray();
        // 从属性值表，获得属性值 [{id:'属性值id', prop_id:'属性id',names_id:'属性值名称id',sort_num:'排序序号'},]

        if(!empty($propArr)){// 目的：让价格属性靠前
            $formatPVArr = [];// 格式化属性值为 {属性值id:{属性详情}}
            foreach($propValList as $v){
                $formatPVArr[$v['id']] = $v;
            }

            // 格式化已有的属性值 二维数组 [['prop_id' => , 'prop_val_id' => , 'is_multi' => , 'is_must' => , 'is_price' => , 'price' => ]]
            // 为 {属性值id:{}}
            $formatPropPVArr = [];
            foreach($propArr as $v){
                $formatPropPVArr[$v['prop_val_id']] = $v;
            }

            // 获得已有的属性值id数组---一维数据
            $pvIdArr = array_unique(array_column($propArr,'prop_val_id'));

            $temPV = [];// 价格属性的属性值数组 格式：{属性值id:属性表详情(一维)}
            // 遍历已有的属性值数组--目的，价格属性靠最前
            foreach($pvIdArr as $t_pv_id){
                $temFormatPV = $formatPVArr[$t_pv_id] ?? [];// 属性值表的数据--一维数组
                $temFormatPVIn = $formatPropPVArr[$t_pv_id] ?? [];// 已选的属性值的数据--一维数组
                if(empty($temFormatPV)) continue;// 不存在数据表，则跳过
                if($temFormatPVIn['is_price'] != 1) continue;// 已选的，不是价格属性，则跳过
                $temPV[$t_pv_id] = $temFormatPV;
                unset($formatPVArr[$t_pv_id]);

            }
            $propValList = array_merge($temPV,$formatPVArr);
        }

        // 遍历属性值表数据
        foreach($propValList as $k => $v){
            // 属性值名称
            $v['main_name'] = $v['name']['main_name'] ?? '';// 获得属性值名称
            // $v['name_id'] = $v['name']['id'] ?? 0;
            if(isset($v['name'])) unset($v['name']);
            $selected_pv_ids = $formatProp[$v['prop_id']]['selected_pv_ids'] ?? [];// 当前已选的属性值
            $v['selected'] = 0;
            // 当前已选中的属性值或，没有已选的，则默认全选
            if(in_array($v['id'], $selected_pv_ids) || empty($selected_pv_ids)) $v['selected'] = 1;
            // 价格
            $v['price'] = $formatPV[$v['id']]['price'] ?? 0;// 属性值，给价格值
            $formatProp[$v['prop_id']]['pv_list'][] = $v;// 加入属性值列表
        }
        return array_values($formatProp);
    }

    /**
     * 查询属性值id是否有商品正在使用,有在使用的抛出错误（正在使用的商品id）
     *
     * @param int  $company_id 企业id
     * @param int $prop_val_id 属性值id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 正在使用属性值的商品id
     * @author zouyan(305463219@qq.com)
     */
    public static function judgePvIdUsed( $company_id, $prop_val_id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(!is_numeric($prop_val_id) ||  $prop_val_id <= 0){
            throws('属性值ID格式有误！');
        }

        $pvInfo = PropValDBBusiness::getInfo($prop_val_id, ['id', 'prop_id'], ['prop']);
        if(empty($pvInfo)) throws('属性值记录不存在');
        $propInfo = $pvInfo['prop'] ?? [];
        if(empty($propInfo)) throws('属性记录不存在');
        $seller_id = $propInfo['seller_id'] ?? 0;
        $shop_id = $propInfo['shop_id'] ?? 0;
        $prop_id = $propInfo['id'] ?? 0;
        // 获得使用此属性值的商品
        $queryParams = [
            'where' => [
                 ['seller_id', '=' , $seller_id],
                 ['shop_id', '=' , $shop_id],
                 ['prop_id', '=' , $prop_id],
                 ['prop_val_id', '=' , $prop_val_id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            'select' => [
                'id', 'goods_id'
            ]
        ];
        $dataList = ShopGoodsPricesDBBusiness::getList($queryParams,[]);
        $goodsIds = array_column($dataList->toArray(),'goods_id');

        $dataListProp = ShopGoodsPropsDBBusiness::getList($queryParams,[]);
        $goodsIdsProp = array_column($dataListProp->toArray(),'goods_id');

        $goodsIds = array_merge($goodsIds, $goodsIdsProp);
        return $goodsIds;
    }
}
