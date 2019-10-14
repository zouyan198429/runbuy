<?php

// 数据关系处理
namespace App\Services\DBRelation;

use App\Services\Tool;

class RelationDB
{
    /**
     * $relations = [
     *   [
     *       'relation_key' => ['city', 'partner'],// 关系key数组，注意都是小写，转为关系时：第一个首字母不大小，后面的都首字母大写  cityPartner
     *       // 'dimension' => 1,// 返回数据维数1一维数组2二维数组 ---代码中自行判断
     *       // 执行顺序 一维数组[] ,谁优先，谁放前面,解析数据时，优先的有数据就看后面的了- -1:代表源数据[为空或无下标默认] ；2：代表doing数据:4：代表history数据
     *       'sequence' => [1,2,4],
     *       'return_data'=>[
     *            'old_data' => [// --只能一维数组 原数据的处理及说明
     *               'ubound_operate' => 2,// 原数据的处理1 保存原数据及下标-如果下级有新字段，会自动更新;2不保存原数据[默认]---是否用新的下标由下面的 'ubound_name' 决定
     *               // 第一次缩小范围，需要的字段  -- 要获取的下标数组 -维 [ '新下标名' => '原下标名' ]  ---为空，则按原来的返回
     *               // 如果新下标和原下标相同，则可以用这个方法去转  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile']), true )
     *               'ubound_name' => 'city',// 新数据的下标--可为空，则不返回,最好不要和原数据下标相同，如果相同，则原数据会把新数据覆盖
     *               'fields_arr' => [],
     *           ],
     *           // 一/二维数组 键值对 可为空或没有此下标：不需要 Tool::formatArrKeyVal($areaCityList, $kv['key'], $kv['val'])
     *           'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
     *           // 一/二维数组 只要其中的某一个字段：
     *           'one_field' => ['key' => 'id', 'return_type' => '返回类型1原数据['字段值'][一维返回一维数组，二维返回一维数组];2按分隔符分隔的字符', 'ubound_name' => '下标名称', 'split' => '、'],
     *           'child'=> [],
     *       ],
     *       'return_data_history'=> [],// 有历史时，的配置--同return_data下标；
     *       'return_data_doing'=> [],// 有正在处理时，的配置--同return_data下标；
     *   ],
     * ];
     *
     */
    /**
     * 获得关系数组
     *
     * @param array $relationConfigArr 关系配置 -- 二维数组
     * @return array 请求接口可用的关系数组 --- 一维数组
     *    $relations = [
     *        'addrHistory', 'staffHistory', 'partnerHistory', 'sendHistory'
     *       ,'provinceHistory','cityHistory','areaHistory'
     *       , 'sellerHistory', 'shopHistory'
     *        ,'ordersGoods.goodsHistory'
     *       ,'ordersGoods.resourcesHistory'
     *       ,'ordersGoods.goodsPriceHistory.propName'
     *       ,'ordersGoods.goodsPriceHistory.propValName'
     *        ,'ordersGoods.props.propName'
     *       ,'ordersGoods.props.propValName'
     *   ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getDBRelation($relationConfigArr = []){
        // 为空，或不是数据，则原返回
        if(empty($relationConfigArr) || !is_array($relationConfigArr)) return $relationConfigArr;
        // 如果是一维数组，则是以前的格式，直接返回-- 兼容以前的
        if(!Tool::isMultiArr($relationConfigArr)) return $relationConfigArr;
        $recordRelation = [];// 当前的关系
        if(empty($relationConfigArr) && !is_array($relationConfigArr)) return $recordRelation;
        foreach($relationConfigArr as $relactionConfigInfo){
            // ['city', 'partner'],// 关系key数组，注意都是小写，转为关系时：第一个首字母不大小，后面的都首字母大写  cityPartner
            $relation_key = $relactionConfigInfo['relation_key'] ?? [];
            if(empty($relation_key) && !is_array($relation_key)) continue;
            $operationSequence = $relactionConfigInfo['sequence'] ?? [1];// 执行顺序 一维数组[] ,谁优先，谁放前面,解析数据时，优先的有数据就看后面的了- -1:代表源数据[为空或无下标默认] ；2：代表doing数据:4：代表history数据
            $relationName = '';
            $i = 1;
            foreach($relation_key as $relactionKey){
                $relactionKey = strtolower($relactionKey);// 先都转为小写
                if($i > 1) $relactionKey = ucfirst($relactionKey);// 第二个开始首字母大写
                $relationName .= $relactionKey;
                $i++;
            }
            if(empty($relationName)) continue;

            $relationNameTem = $relationName;
            $child = $relactionConfigInfo['return_data']['child'] ?? [];
            $hasChildRelation = false;
            if(!empty($child) && is_array($child)) {
                $temRelation = static::getDBRelation($child);
                if (!empty($temRelation) && is_array($temRelation)) $hasChildRelation = true;
                foreach ($temRelation as $temRelationName) {
                    array_push($recordRelation, $relationNameTem . '.' . $temRelationName);

                }
            }
            if(!$hasChildRelation) array_push($recordRelation, $relationNameTem);

            if(in_array(4, $operationSequence)){
                $relationNameTem = $relationName . 'History';
                $history_child = $relactionConfigInfo['return_data_history']['child'] ?? [];
                $hasChildRelation = false;
                if(!empty($history_child) && is_array($history_child)) {
                    $temRelation = static::getDBRelation($history_child);
                    if (!empty($temRelation) && is_array($temRelation)) $hasChildRelation = true;
                    foreach ($temRelation as $temRelationName) {
                        array_push($recordRelation, $relationNameTem . '.' . $temRelationName);

                    }
                }
                if(!$hasChildRelation) array_push($recordRelation, $relationNameTem);
            }

            if(in_array(2, $operationSequence)){
                $relationNameTem = $relationName . 'Doing';
                $history_child = $relactionConfigInfo['return_data_doing']['child'] ?? [];
                $hasChildRelation = false;
                if(!empty($history_child) && is_array($history_child)) {
                    $temRelation = static::getDBRelation($history_child);
                    if (!empty($temRelation) && is_array($temRelation)) $hasChildRelation = true;
                    foreach ($temRelation as $temRelationName) {
                        array_push($recordRelation, $relationNameTem . '.' . $temRelationName);

                    }
                }
                if(!$hasChildRelation) array_push($recordRelation, $relationNameTem);
            }

        }

        return $recordRelation;

    }


    /**
     * 解析源数据  一/二维数组
     *
     * @param array $dataList 源数据 一/二维数组
     * @param array $relationConfigArr 关系配置 -- 二维数组
     * @return array dataList 源数据 一/二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function resolvingRelationData(&$dataList = [], $relationConfigArr = [])
    {
        // 为空，或不是数据，则原返回
        if(empty($relationConfigArr) || !is_array($relationConfigArr)) return $dataList;

        // 如果是一维数组，则是以前的格式，直接返回-- 兼容以前的
        if (!Tool::isMultiArr($relationConfigArr)) return $dataList;
        if(empty($relationConfigArr) && !is_array($relationConfigArr)) return $dataList;
        $isMultiArr = Tool::isMultiArr($dataList, true);// 源数据是一维(false)--则转为二维，还是二维数组(true)
        foreach($dataList as $k => $info){
            // 开始解析单条数据
//            static::resolvingRelationInfo($info, $relationConfigArr);
//            $dataList[$k] = $info;
            static::resolvingRelationInfo($dataList[$k], $relationConfigArr);
        }
        if(!$isMultiArr) $dataList = $dataList[0] ?? [];// 是一维数组
        return $dataList;
    }
    /**
     * 解析单条数据  一维数组  ---仅类内调用
     *
     * @param array $info 源数据 一维数组
     * @param array $relationConfigArr 关系配置 -- 二维数组
     * @return array  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    protected static function resolvingRelationInfo(&$info = [], $relationConfigArr = [])
    {
        $returnFields = [];
        // 如果是一维数组，则是以前的格式，直接返回-- 兼容以前的
        // if (!Tool::isMultiArr($relationConfigArr)) return $info;

        foreach($relationConfigArr as $relactionConfigInfo){
            // ['city', 'partner'],// 关系key数组，注意都是小写，转为关系时：第一个首字母不大小，后面的都首字母大写  cityPartner
            $relation_key = $relactionConfigInfo['relation_key'] ?? [];
            if(empty($relation_key) && !is_array($relation_key)) continue;

            // 数据下标名称
            $relationNameArr = [];
            foreach($relation_key as $relactionKey){
                $relactionKey = strtolower($relactionKey);// 先都转为小写
                array_push($relationNameArr, $relactionKey);
            }
            if(empty($relationNameArr)) continue;
            $relationUbound = implode('_', $relationNameArr);// 原数据下标
            $relationUboundHistory = $relationUbound . '_history';// 历史数据下标
            $relationUboundDoing = $relationUbound . '_doing';// 正在处理数据下标

            $relationData = $info[$relationUbound] ?? [];// 源数据
            $relationDataHistory = $info[$relationUboundHistory] ?? [];// 历史数据
            $relationDataDoing = $info[$relationUboundDoing] ?? [];// 正在处理数据
            // 都为空，则不处理
            if(empty($relationData) && empty($relationDataHistory) && empty($relationDataDoing)) continue;

            $operationSequence = $relactionConfigInfo['sequence'] ?? [1];// 执行顺序 一维数组[] ,谁优先，谁放前面,解析数据时，优先的有数据就看后面的了- -1:代表源数据[为空或无下标默认] ；2：代表doing数据:4：代表history数据

            $findedData = false;// 是否找到要处理的源数据
            $recordDataUbound = '';// 当参数处理的源数据的下标
            $recordData = [];// 当参数处理的源数据
            $recordDataConfig = [];// 处理当前数据的配置
            foreach($operationSequence as $sequenceNum){
                switch($sequenceNum) {
                    case 1://  1:代表源数据
                        if(!$findedData && !empty($relationData) && is_array($relationData)) {
                            $findedData = true;
                            $recordDataUbound = $relationUbound;
                            $recordData = $relationData;
                            $recordDataConfig = $relactionConfigInfo['return_data'] ?? [];
                        }else{

                            // 原数据的处理1 保存原数据及下标;2不保存原数据--是否用新的下标由下面的 'ubound_name'
                            $tem_ubound_operate = $relactionConfigInfo['return_data']['old_data']['ubound_operate'] ?? 2;
                            $tem_ubound_name = $relactionConfigInfo['return_data']['old_data']['ubound_name'] ?? '';
                            $tem_ubound_old = $relationUbound;

//                            if(strlen($tem_ubound_name) > 0  && $tem_ubound_old != $tem_ubound_name && isset($info[$tem_ubound_old])) $info[$tem_ubound_name] = $info[$tem_ubound_old];
//                            if( $tem_ubound_operate == 2 && $tem_ubound_old != $tem_ubound_name && isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
//                            if( $tem_ubound_operate == 2  && isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
                            if( isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
                        }

                        break;
                    case 2:// 2：代表doing数据:
                        if(!$findedData && !empty($relationDataDoing) && is_array($relationDataDoing)) {
                            $findedData = true;
                            $recordDataUbound = $relationUboundDoing;
                            $recordData = $relationDataDoing;
                            $recordDataConfig = $relactionConfigInfo['return_data_doing'] ?? [];
                        }else{

                            // 原数据的处理1 保存原数据及下标;2不保存原数据--是否用新的下标由下面的 'ubound_name'
                            $tem_ubound_operate = $relactionConfigInfo['return_data_doing']['old_data']['ubound_operate'] ?? 2;
                            $tem_ubound_name = $relactionConfigInfo['return_data_doing']['old_data']['ubound_name'] ?? '';
                            $tem_ubound_old = $relationUboundDoing;

//                            if(strlen($tem_ubound_name) > 0  && $tem_ubound_old != $tem_ubound_name && isset($info[$tem_ubound_old])) $info[$tem_ubound_name] = $info[$tem_ubound_old];
//                            if( $tem_ubound_operate == 2 && $tem_ubound_old != $tem_ubound_name && isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
//                            if( $tem_ubound_operate == 2  && isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
                            if( isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
                        }
                        break;
                    case 4:// 4：代表history数据
                        if(!$findedData && !empty($relationDataHistory) && is_array($relationDataHistory)) {
                            $findedData = true;
                            $recordDataUbound = $relationUboundHistory;
                            $recordData = $relationDataHistory;
                            $recordDataConfig = $relactionConfigInfo['return_data_history'] ?? [];
                        }else{

                            // 原数据的处理1 保存原数据及下标;2不保存原数据--是否用新的下标由下面的 'ubound_name'
                            $tem_ubound_operate = $relactionConfigInfo['return_data_history']['old_data']['ubound_operate'] ?? 2;
                            $tem_ubound_name = $relactionConfigInfo['return_data_history']['old_data']['ubound_name'] ?? '';
                            $tem_ubound_old = $relationUboundHistory;

//                            if(strlen($tem_ubound_name) > 0  && $tem_ubound_old != $tem_ubound_name && isset($info[$tem_ubound_old])) $info[$tem_ubound_name] = $info[$tem_ubound_old];
//                            if( $tem_ubound_operate == 2 && $tem_ubound_old != $tem_ubound_name && isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
//                            if( $tem_ubound_operate == 2  && isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
                            if( isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
                        }
                        break;
                    default:
                        break;
                }
            }
            if(!$findedData) continue;// 没有需要处理的
            // 对数据进行处理
            $recordDataOld = $recordData;// 原数据
            $tem_ubound_old = $recordDataUbound;
            // 原数据的处理1 保存原数据及下标;2不保存原数据--是否用新的下标由下面的 'ubound_name'
            $tem_ubound_operate = $recordDataConfig['old_data']['ubound_operate'] ?? 2;
            $tem_ubound_name = $recordDataConfig['old_data']['ubound_name'] ?? '';// $tem_ubound_old;
            // 第一次缩小范围，需要的字段  -- 要获取的下标数组 -维 [ '新下标名' => '原下标名' ]  ---为空，则按原来的返回
            // 如果新下标和原下标相同，则可以用这个方法去转  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile']), true )
            $tem_fields_arr = $recordDataConfig['old_data']['fields_arr'] ?? [];

            // 有新字段
            if($tem_ubound_name != $tem_ubound_old && strlen($tem_ubound_name) > 0) array_push($returnFields, $tem_ubound_name);
            // 对子数据进行处理
            $tem_child = $recordDataConfig['child'] ?? [];
            $tem_returnFields = [];
            if(!empty($tem_child) && is_array($tem_child)) {
                $tem_returnFields = static::resolvingRelationInfo($recordData, $tem_child);
            }

            // 对数据进行处理
            if(!empty($tem_fields_arr) && is_array($tem_fields_arr)) {
                if(!empty($tem_returnFields) && is_array($tem_returnFields)){// 把下一级中新加的字段也加上
                    $tem_fields_arr = array_merge($tem_fields_arr,Tool::arrEqualKeyVal($tem_returnFields));
                }
                Tool::formatArrKeys($recordData, $tem_fields_arr , true );
            }

            // 删除原记录
//            if( $tem_ubound_operate == 2 && $tem_ubound_old != $tem_ubound_name && isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);
            if( $tem_ubound_operate == 2 && isset($info[$tem_ubound_old])) unset($info[$tem_ubound_old]);

            // 更新原记录
            if( $tem_ubound_operate == 1 && isset($info[$tem_ubound_old]) ){
                if(!empty($tem_returnFields) && is_array($tem_returnFields)) {// 把下一级中新加的字段也加上
                    foreach($tem_returnFields as $t_f){
                        $recordDataOld[$t_f] = $recordData[$t_f] ?? null;
                    }
                }
                // 不放到上面if内，是因为，可能它的下级没有更新字段，但它的下下级更新了。
                $info[$tem_ubound_old] = $recordDataOld;
            }

            // if(strlen($tem_ubound_name) > 0  && $tem_ubound_old != $tem_ubound_name && isset($info[$tem_ubound_old])) $info[$tem_ubound_name] = $recordData;
            if(strlen($tem_ubound_name) > 0 ) $info[$tem_ubound_name] = $recordData;

            // 键值对 可为空或没有此下标：不需要 Tool::formatArrKeyVal($areaCityList, $kv['key'], $kv['val'])
//            'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
            $tem_k_v = $recordDataConfig['k_v'] ?? [];
            if(!empty($tem_k_v)  && is_array($tem_k_v)){
                Tool::isMultiArr($tem_k_v, true);// 如果是一维数组，转为二维数组
                foreach($tem_k_v as $tem_kv_info){
                    $tem_kv_key = $tem_kv_info['key'] ?? '';
                    $tem_kv_val = $tem_kv_info['val'] ?? '';
                    $tem_kv_ubound_name = $tem_kv_info['ubound_name'] ?? '';
                    if(strlen($tem_kv_key) > 0 && strlen($tem_kv_val) > 0 && strlen($tem_kv_ubound_name) > 0){
                        $info[$tem_kv_ubound_name] = Tool::formatArrKeyVal($recordData, $tem_kv_key, $tem_kv_val);
                        array_push($returnFields, $tem_kv_ubound_name);
                    }

                }
            }

            // 只要其中的某一个字段：
//            'one_field' => ['key' => 'id', 'return_type' => '返回类型1原数据[一维返回一维数组，二维返回一维数组];2按分隔符分隔的字符', 'ubound_name' => '下标名称', 'split' => '、'],

            $tem_one_field = $recordDataConfig['one_field'] ?? [];
            if(!empty($tem_one_field)  && is_array($tem_one_field)) {
                Tool::isMultiArr($tem_one_field, true);// 如果是一维数组，转为二维数组
                foreach($tem_one_field as $tem_one_info){
                    $tem_one_key = $tem_one_info['key'] ?? '';

                    // 返回类型1原数据[一维返回一维数组，二维返回一维数组];2按分隔符分隔的字符
                    $tem_one_return_type = $tem_one_info['return_type'] ?? 1;
                    $tem_one_ubound_name = $tem_one_info['ubound_name'] ?? '';
                    $tem_one_split = $tem_one_info['split'] ?? '';
                    $tem_one_fieldArr = Tool::getArrFields($recordData, $tem_one_key);
                    if($tem_one_return_type == 2){// 2按分隔符分隔的字符
                        $info[$tem_one_ubound_name] = implode($tem_one_split, $tem_one_fieldArr);
                    }else{
                        $info[$tem_one_ubound_name] = $tem_one_fieldArr;
                    }
                    array_push($returnFields, $tem_one_ubound_name);
                }
            }

        }
        return array_values(array_unique($returnFields));

    }


}