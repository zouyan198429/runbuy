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

        $propVals = $saveData['prop_vals'] ?? [];
        if(empty($propVals)  ) throws('属性值不能为空！');
        if(isset($saveData['prop_vals']))  unset($saveData['prop_vals']);
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
            if(!empty($main_name)){
                 $nameObj = null ;
                $searchConditon = [
                    'main_name' => $main_name
                ];
                $updateFields = [];
                 NamesDBBusiness::addOprate($updateFields, $operate_staff_id,$operate_staff_id_history);
                 NamesDBBusiness::firstOrCreate($nameObj, $searchConditon, $updateFields);
                $saveData['names_id'] = $nameObj->id;
            }
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
                $pvArr = [];
                $pvCount = count($propVals);
                foreach($propVals as $pvName){
                    if($pvName == '') continue;
                    $nameObj = null ;
                    $searchConditon = [
                        'main_name' => $pvName
                    ];
                    $updateFields = [];
                    NamesDBBusiness::addOprate($updateFields, $operate_staff_id,$operate_staff_id_history);
                    NamesDBBusiness::firstOrCreate($nameObj, $searchConditon, $updateFields);
                    array_push($pvArr, [
                        'prop_id' => $id,
                        'names_id' => $nameObj->id,
                        'sort_num' => $pvCount--,
                    ]);
                }
                PropValDBBusiness::addOprate($pvArr, $operate_staff_id,$operate_staff_id_history);
                PropValDBBusiness::addBath($pvArr);


            }else{// 修改
                $modelObj = null;
                $saveBoolen = static::saveById($saveData, $id, $modelObj);
                // $resultDatas = static::getInfo($id);
                // 批量添加属性值
                $pvIdArr = [];
                $pvCount = count($propVals);
                foreach($propVals as $pvName){
                    if($pvName == '') continue;
                    $nameObj = null ;
                    $searchConditon = [
                        'main_name' => $pvName
                    ];
                    $updateFields = [];
                    NamesDBBusiness::addOprate($updateFields, $operate_staff_id,$operate_staff_id_history);
                    NamesDBBusiness::firstOrCreate($nameObj, $searchConditon, $updateFields);
                    $temPv = [
                        'prop_id' => $id,
                        'names_id' => $nameObj->id,
                        'sort_num' => $pvCount--,
                    ];
                    PropValDBBusiness::addOprate($temPv, $operate_staff_id,$operate_staff_id_history);
                    $pvObj = null ;
                    $searchConditon = [
                        'prop_id' => $id,
                        'names_id' => $nameObj->id,
                    ];
                    $updateFields = [];
                    PropValDBBusiness::updateOrCreate($pvObj, $searchConditon, $temPv);
                    array_push($pvIdArr, $pvObj->id);

                    // 修改时，更新版本号
                    //if($isModify){
                        PropValDBBusiness::compareHistory($pvObj->id, 1);
                    //}
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
                if(!empty($pvIdArr))  $queryParams['whereNotIn']['id'] = $pvIdArr;
                PropValDBBusiness::del($queryParams);
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

        $propIdArr = explode(',', $propIds);
        if(empty($propIds)) {
            $propIdArr = array_unique(array_column($propArr,'prop_id'));
            $propIds = implode(',', $propIdArr);
        }

        if(empty($propIds)) return $formatProp;

        $formatGoodProp = [];
        $formatPV = [];// 属性值数组
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
                'id', 'city_site_id', 'city_partner_id', 'seller_id', 'names_id', 'sort_num'
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
            $temGoodArr = $formatGoodProp[$v['id']] ?? [];
            $propValIds = array_unique(array_column($temGoodArr,'prop_val_id'));
            $v = array_merge($v,[
                'is_multi' => $temGoodArr[0]['is_multi'] ?? 0
                , 'is_must' => $temGoodArr[0]['is_must'] ?? 0
                , 'is_price' => $temGoodArr[0]['is_price'] ?? 0
                , 'id_history' => 0
                , 'selected_pv_ids' => $propValIds // 属性值id 数组
                , 'pv_ids' => implode(',', $propValIds) // 属性值id 字符串
            ]);
            $formatProp[$v['id']] = $v;
        }
        if(!empty($propArr)){
            $temProp = [];
            foreach($propIdArr as $t_prop_id){
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
        if(!empty($propArr)){
            $formatPVArr = [];
            foreach($propValList as $v){
                $formatPVArr[$v['id']] = $v;
            }
            $formatPropPVArr = [];
            foreach($propArr as $v){
                $formatPropPVArr[$v['prop_val_id']] = $v;
            }
            $pvIdArr = array_unique(array_column($propArr,'prop_val_id'));

            $temPV = [];
            foreach($pvIdArr as $t_pv_id){
                $temFormatPV = $formatPVArr[$t_pv_id] ?? [];
                $temFormatPVIn = $formatPropPVArr[$t_pv_id] ?? [];
                if(empty($temFormatPV)) continue;
                if($temFormatPVIn['is_price'] != 1) continue;
                $temPV[$t_pv_id] = $temFormatPV;
                unset($formatPVArr[$t_pv_id]);

            }
            $propValList = array_merge($temPV,$formatPVArr);
        }
        foreach($propValList as $k => $v){
            // 属性值名称
            $v['main_name'] = $v['name']['main_name'] ?? '';
            // $v['name_id'] = $v['name']['id'] ?? 0;
            if(isset($v['name'])) unset($v['name']);
            $selected_pv_ids = $formatProp[$v['prop_id']]['selected_pv_ids'] ?? [];
            $v['selected'] = 0;
            if(in_array($v['id'], $selected_pv_ids) || empty($selected_pv_ids)) $v['selected'] = 1;
            // 价格
            $v['price'] = $formatPV[$v['id']]['price'] ?? 0;
            $formatProp[$v['prop_id']]['pv_list'][] = $v;
        }
        return array_values($formatProp);
    }

}
