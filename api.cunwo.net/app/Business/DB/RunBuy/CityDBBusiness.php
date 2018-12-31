<?php
// 城市[三级分类]
namespace App\Business\DB\RunBuy;

use Illuminate\Support\Facades\DB;

/**
 *
 */
class CityDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\City';
    public static $table_name = 'city';// 表名称

    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, CityHistoryDBBusiness::$model_name
            , CityHistoryDBBusiness::$table_name, $historyDBObj, ['city_table_id' => $mainId], []);
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
            'city_table_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, CityHistoryDBBusiness::$model_name
            , CityHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['city_table_id'], $forceIncVersion);
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['city_name']) && empty($saveData['city_name'])  ){
            throws('名称不能为空！');
        }

        if(isset($saveData['code']) && empty($saveData['code'])  ){
            throws('城市代码不能为空！');
        }

        $parent_id = 0;
        $city_ids = [];
        // 处理所所地区
        $province_id = $saveData['province_id'] ?? 0;
        if(isset($saveData['province_id'])) unset($saveData['province_id']);
        if($province_id > 0){
            $parent_id = $province_id;
            array_push($city_ids, $province_id);
        }

        $city_id = $saveData['city_id'] ?? 0;
        if(isset($saveData['city_id'])) unset($saveData['city_id']);
        if($city_id > 0){
            $parent_id = $city_id;
            array_push($city_ids, $city_id);
        }

        $cityNamePinyin = strtoupper(pinyin_abbr($saveData['city_name']));
        $initial = substr($cityNamePinyin,0,1);
        $saveData = array_merge($saveData, ['parent_id' => $parent_id, 'head' => $cityNamePinyin, 'initial' => $initial]);

        DB::beginTransaction();
        try {
            $childList = [];// 需要修改子级城市父级关系的子级城市
            if($id > 0){
                array_push($city_ids, $id);
                $newCityIds = implode(',', $city_ids) . ',';
                $saveData = array_merge($saveData, ['city_ids' => $newCityIds]);
                // 获得详情
                $info = static::getInfo($id);
                $oldCityIds = $info['city_ids'];
                // 有子级城市，则不能修改所属
                if($info['parent_id'] != $parent_id ){
                    if($parent_id == $id) throws('所属不能选择自己');
                    // throws($newCityIds . '--- ' . $oldCityIds);
                    if (strpos(',' . $newCityIds, ',' . $oldCityIds) === 0) { // 当前记录移动到它的子级了 //  && strpos(',' . $newCityIds, ',' . $oldCityIds) !== false
                        throws('所属不能选择自己的子级所属');
                    }

                    // 获得子级城市信息
                    $queryParams = [
                        'where' => [
                            // ['parent_id', '=' , $id],
                            ['city_ids', 'like', '' . $oldCityIds . '%'],
                            // ['id', '&' , '16=16'],
                        ],
                         'select' => [
                             'id','city_ids','city_name'
                         ]
                    ];
                    $childList = static::getAllList($queryParams,[]);
                    if(count($childList) > 0){
                        $cityPidCount = count($city_ids);// 当前记录级数
                        $oldPIds = explode(',', $oldCityIds);
                        $oldPidCount = count($oldPIds) - 1; // 老记录级数

                        //判断子级是否会超过三级
                        foreach($childList as $temChildCity){
                            $childCityIds = $temChildCity['city_ids'];
                            $childPIds = explode(',', $childCityIds);
                            $childCountPIds = count($childPIds) - 1;// 子记录级数
                            if($cityPidCount + ($childCountPIds - $oldPidCount) > 3){
                                throws('修改所属，子级城市[' . $temChildCity['city_name'] . ']级数超过三级，请先处理子级城市，再修改所属！');
                                break;
                            }
                        }
                    }
                }

                // 判断权限
    //            $judgeData = [
    //                'company_id' => $company_id,
    //            ];
    //            $relations = '';
    //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                if($modifAddOprate) static::addOprate($saveData, $operate_staff_id);

            }else {// 新加;要加入的特别字段
    //            $addNewData = [
    //                'company_id' => $company_id,
    //            ];
    //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                static::addOprate($saveData, $operate_staff_id);
            }

            $saveData['parent_id_history'] = 0;
            if($parent_id > 0) {
                $saveData['parent_id_history'] = static::getIdHistory($parent_id);
            }
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$cityObj);
                $id = $resultDatas['id'] ?? 0;
                // 保存父id串
                if($id > 0) {
                    array_push($city_ids, $id);
                    $modifyData = [
                        'city_ids' => implode(',', $city_ids) . ',',
                    ];
                    $saveBoolen = static::saveById($modifyData, $id, $cityObj);
                }

            }else{// 修改
                $modelObj = null;
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // 更新子级城市所属
                foreach($childList as $temChildCity) {
                    $childCityId = $temChildCity['id'];
                    $childCityIds = $temChildCity['city_ids'];
                    //$temCityIds = $city_ids;
                    //array_push($temCityIds, $childCityId);
                    $modifyData = [
                        'city_ids' => str_ireplace($oldCityIds, $newCityIds, $childCityIds),
                    ];
                    static::saveById($modifyData, $childCityId, $temChildCity);
                }
                // 修改数据，是否当前版本号 + 1
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
