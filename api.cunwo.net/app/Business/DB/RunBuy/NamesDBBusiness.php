<?php
// 主名称词
namespace App\Business\DB\RunBuy;

/**
 *
 */
class NamesDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Names';
    public static $table_name = 'names';// 表名称

    /**
     * 根据名称，返回名称表id,没有则返回0
     *
     * @param string $main_name 名称
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     * @return int 名称表id
     * @author zouyan(305463219@qq.com)
     */
    public static function getNameId($main_name, $operate_staff_id = 0, &$operate_staff_id_history = 0){
        if(!empty($main_name)){
            $nameObj = null ;
            $searchConditon = [
                'main_name' => $main_name
            ];
            $updateFields = [];
            static::addOprate($updateFields, $operate_staff_id,$operate_staff_id_history);
            static::firstOrCreate($nameObj, $searchConditon, $updateFields);
            return $nameObj->id;
        }
        return 0;
    }
}
