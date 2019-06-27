<?php
// 收费标准
namespace App\Business\DB\RunBuy;
use App\Business\API\RunBuy\CityAPIBusiness;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class FeeScaleTimeDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\FeeScaleTime';
    public static $table_name = 'fee_scale_time';// 表名称


    /**
     * 根据城市id新加或修改时间段价格
     *
     * @param array $saveData 要保存或修改的数组  必要参数 ower_type , ower_id
     * @param int  $company_id 企业id
     * @param int $city_site_id 城市id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function saveTimesByCityId($saveData, $company_id, &$city_site_id, $operate_staff_id = 0, $modifAddOprate = 0){

        if( empty($city_site_id) || !is_numeric($city_site_id)) throws('城市id参数不能为空！');

        if(isset($saveData['city_site_id']) && empty($saveData['city_site_id'])  ){
            throws('城市id不能为空！');
        }
        if(isset($saveData['city_site_id']) &&  $saveData['city_site_id'] != $city_site_id)  throws('城市id参数有误！');


        if (isset($saveData['price_distance_every']) && empty($saveData['price_distance_every'])) {
            throws('超出2公里，每公里费用不能为空！');
        }

        if (isset($saveData['price_shop_every']) && empty($saveData['price_shop_every'])) {
            throws('每多跑一商家费用不能为空！');
        }

        $time_list = $saveData['time_list'] ?? [];
        if( isset($saveData['time_list']) ){
            unset($saveData['time_list']);
        }
        if( empty($time_list) ) throws('时间段价格有误！');
        /*
        $temTime = [
            'id' => $tId, // 时间段价格id 0或具体数字
            'time_num' => $tem_time_num,// 时间编号
            'begin_time' => $tem_arr['begin_time'] ?? '',// 开始时间
            'end_time' => $tem_arr['end_time'] ?? '',// 结束时间
            'init_price' => $init_prices[$k],// 价格
            'sort_num' => $pCount--,// 排序[降序]
        ];
        */

        DB::beginTransaction();
        $operate_staff_id_history = 0;
        try {
            // 处理城市表相关的

            // 获得城市记录
            $cityInfo = CityDBBusiness::getInfo($city_site_id, ['id']);
            if(empty($cityInfo)) throws('城市记录不存在');
            $db_city_site_id = $cityInfo['id'];
            //
            if( isset($saveData['city_site_id']) ) unset($saveData['city_site_id']);
            if(!empty($saveData)){
                static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history);
                // 修改城市记录
                $modelObj = null;
                $saveBoolen = CityDBBusiness::saveById($saveData, $city_site_id,$modelObj);
                // 修改数据，是否当前版本号 + 1
                CityDBBusiness::compareHistory($city_site_id, 1);
            }

            // 处理时间段价格记录

            // 获得当前已有的时间段价格值
            $queryParams = [
                'where' => [
                    ['city_site_id', $city_site_id],
                ],
                    'select' => [
                        'id'
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
                    ],
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            $timePriceList = static::getAllList($queryParams, '')->toArray();
            $hasTimeTableIds = array_column($timePriceList,'id');

            $addTimePrices = [];// 要新加的时间段价格
            $needOldTimeTableIds = [];// 表中已有的时间段价格
            // 遍历商品属性
            foreach($time_list as $k => $v){
                $tem_time_price_id = $v['id'];

                $temSaveData = $v;
                if( !isset($temSaveData['city_site_id']) || empty($temSaveData['city_site_id']) ) $temSaveData['city_site_id'] = $city_site_id;

                /*
                 * [
                    'cart_id' => $saveData['cart_id'],
                    'id' => $tem_time_price_id,
                    'prop_id' => $v['prop_id'],
                    'prop_val_id' => $v['prop_val_id'],
                    'prop_names_id' => $v['prop_names_id'],
                    'prop_val_names_id' => $v['prop_val_names_id'],
                    'operate_staff_id' => $operate_staff_id,
                    // 'operate_staff_id_history' => $v['aaa'],
                ];
                 */
                static::addOprate($temSaveData, $operate_staff_id,$operate_staff_id_history);

                if(in_array($tem_time_price_id, $hasTimeTableIds)){ // 已经存在
                    // 更新
                    $saveQueryParams = [
                        'where' => [
                            ['city_site_id', $city_site_id],
                            ['id', $tem_time_price_id],
                        ],
//                            'select' => [
//                                'id','title','sort_num','volume'
//                                ,'operate_staff_id','operate_staff_id_history'
//                                ,'created_at' ,'updated_at'
//                            ],
                        //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                    ];
                    static::save($temSaveData, $saveQueryParams);
                    array_push($needOldTimeTableIds, $tem_time_price_id);
                    continue;
                }
                if( isset($temSaveData['id']) ) unset($temSaveData['id']);
                array_push($addTimePrices, $temSaveData);
            }
            if(!empty($addTimePrices))  static::addBath($addTimePrices);// 批量添加

            // 删除多余记录
            $delTimePriceTableIDS = array_diff($hasTimeTableIds, $needOldTimeTableIds);
            if(!empty($delTimePriceTableIDS)){
                $delParams = [
                    'where' => [
                        ['city_site_id', $city_site_id],
                    ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
                $delParams['whereIn']['id'] = $delTimePriceTableIDS;
                static::del($delParams);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
        return $city_site_id;
    }

}
