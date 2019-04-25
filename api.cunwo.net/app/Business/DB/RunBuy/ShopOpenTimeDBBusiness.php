<?php
// 店铺营业时间
namespace App\Business\DB\RunBuy;

use App\Services\Tool;
use Illuminate\Support\Facades\DB;
/**
 *
 */
class ShopOpenTimeDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\ShopOpenTime';
    public static $table_name = 'shop_open_time';// 表名称

    /**
     * 保存营业时间
     *
     * @param int $city_site_id 城市分站id
     * @param int $city_partner_id 城市合伙人id
     * @param int $seller_id 商家ID
     * @param int $shop_id 店铺ID
     * @param array $open_time_list 需要保存的营业时间 二维数组
     * [
            [
            'id' => $tId,// 0 或具体的id[修改]
            'open_time' => $open_time[$k],
            'close_time' => $close_time[$k],
            'is_open' => $is_open[$k],
            'sort_num' => $pCount--,
            ]
     * ]
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人id历史 默认 0
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function saveData($city_site_id, $city_partner_id, $seller_id, $shop_id, $open_time_list, $operate_staff_id = 0, $operate_staff_id_history = 0){
        // 获得当前店铺所有的时间
        $queryParams = [
            'where' => [
                ['city_site_id', $city_site_id],
                ['city_partner_id', $city_partner_id],
                ['seller_id', $seller_id],
                ['shop_id', $shop_id],
            ],
            'select' => ['id'],
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        $openTimeList = static::getAllList($queryParams, '')->toArray();
        // 数据表中已经有的id
        $inDbIds = array_column($openTimeList, 'id');
        $updateIds = [];
        foreach($open_time_list as $k => $v){
            if($v['id'] > 0) array_push($updateIds, $v['id']);
        }
        $delIds = array_values(array_diff($inDbIds, $updateIds));
        // 保存数据
        $temArr = [
            'city_site_id' => $city_site_id,
            'city_partner_id' => $city_partner_id,
            'seller_id' => $seller_id,
            'shop_id' => $shop_id,
           // 'operate_staff_id' => $operate_staff_id,
           // 'operate_staff_id_history' => $operate_staff_id_history,
        ];
        static::addOprate($temArr, $operate_staff_id,$operate_staff_id_history);
        // 需要新加的数据
        $addDataArr = [];
        // 需要修改的数据
        $modifyDataArr = [];
        foreach($open_time_list as $k => $v){
            $tId = $v['id'];
            if($tId <= 0 && !empty($delIds)){
                $tId = array_pop($delIds);
                $v['id'] = $tId;
            }
            $v = array_merge($v, $temArr);
            if($tId > 0){
                array_push($modifyDataArr, $v);
            }else{
                unset($v['id']);
                array_push($addDataArr, $v);
            }
        }
        DB::beginTransaction();
        try {
            // 修改
            if(!empty($modifyDataArr)){
                static::saveBathById($modifyDataArr, 'id');
            }
            // 新加
            if(!empty($addDataArr)){
                static::addBath($addDataArr);
            }
            // 删除
            if(!empty($delIds)){
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
                $queryParams['whereIn']['id'] = $delIds;
                static::del($queryParams);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
        return true;
    }

    /**
     * 跑店铺营业中脚本
     *
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function autoShopOnLine($city_site_id = 0){
        $nowTime =  date('H:i:s');
        $queryParams = [
            'where' => [
                ['open_time', '<', $nowTime],
                ['close_time', '>', $nowTime],
                ['is_open', '=', 2],
            ],
//            'select' => [
//                'id','title','sort_num','volume'
//            ],
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        $queryParams['select'] = ['id', 'shop_id'];
        if($city_site_id > 0 )  array_push($queryParams['where'], ['city_site_id', '=', $city_site_id]);
        $openTimeList = static::getAllList($queryParams, '')->toArray();
        // if(empty($openTimeList)) return true;
        // 获得需要上线的店铺id
        $shopIdsNeed = array_values(array_unique(array_column($openTimeList, 'shop_id')));
        // 获得已上线的店铺id
        $shopIdsOnLine = Tool::getRedis('shop:online' . $city_site_id, 1);
        // if(!empty($shopIdsOnLine)) pr($shopIdsOnLine);
        if(!is_array($shopIdsOnLine)){//  || true
            $shopIdsOnLine = ShopDBBusiness::getBusinessShopIds($city_site_id, 1, 1);
            // 缓存起来
            Tool::setRedis('shop:', 'online' . $city_site_id , $shopIdsOnLine, 0 , 1);
        }
        if(!is_array($shopIdsOnLine)) $shopIdsOnLine = [];
        // 获得所有的店铺id
        // $shopIdsOperateAll = array_values(array_unique(array_merge($shopIdsNeed,$shopIdsOnLine)));

        // 获得真正需要上线的店铺id
        $shopIdsOperateOnLine = array_values(array_diff($shopIdsNeed, $shopIdsOnLine));

        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // 获得需要下线的店铺id
        $shopIdsOperateDownLine = array_values(array_diff($shopIdsOnLine, $shopIdsNeed));

        // 获得还在线上的店铺id
        $shopIdsInLine = array_values(array_diff($shopIdsOnLine, $shopIdsOperateDownLine));

        // 还在线上的,如果未审或非营业中的，要下线操作
        if(!empty($shopIdsInLine)){
            // 获得非营业中的店铺记录
            $queryParams = [
                'where' => [
                    // ['status_business', '!=', 1],
                ],
//            'select' => [
//                'id','title','sort_num','volume'
//            ],
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            $queryParams['select'] = ['id', 'status_business', 'status'];
            $queryParams['whereIn']['id'] = $shopIdsInLine;
            if($city_site_id > 0 )  array_push($queryParams['where'], ['city_site_id', '=', $city_site_id]);
            $shopList = ShopDBBusiness::getAllList($queryParams, '')->toArray();
            // 去掉未审核通过和非营业中的
            foreach($shopList as $k => $v){
                // 未审核通过,保留,下线
                if($v['status'] != 1){
                    continue;
                }
                // 非营业中的,保留，下线
                if($v['status_business'] != 1  ){
                    continue;
                }
                unset($shopList[$k]);
            }
            $shopList = array_values($shopList);
            if(!empty($shopList)){
                $temShopIds = array_values(array_unique(array_column($shopList, 'id')));
                $shopIdsOperateDownLine = array_values(array_merge($shopIdsOperateDownLine, $temShopIds));
                $shopIdsInLine = array_values(array_diff($shopIdsInLine, $temShopIds));
            }
        }

        // 还在线上的和将要上线的店铺id，判断是否有非营业中的，有则要进行下线
        // 将要上线的店铺id--处理不能上线的
        if(!empty($shopIdsOperateOnLine)){
            // 获得非营业中的店铺记录
            $queryParams = [
                'where' => [
                   //  ['status_business', '!=', 1],
                ],
//            'select' => [
//                'id','title','sort_num','volume'
//            ],
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            $queryParams['select'] = ['id', 'status_business', 'status'];
            $queryParams['whereIn']['id'] = $shopIdsOperateOnLine;
            if($city_site_id > 0 )  array_push($queryParams['where'], ['city_site_id', '=', $city_site_id]);
            $shopList = ShopDBBusiness::getAllList($queryParams, '')->toArray();
            // 去掉未审核通过和是营业中的
            foreach($shopList as $k => $v){
                // 未审核通过,保留,不上线
                if($v['status'] != 1){
                    continue;
                }
                // 营业中的且在线的,保留,不上线
                if($v['status_business'] == 1 ){// 1营业中
                    if( in_array($v['id'],$shopIdsInLine)){
                        continue;
                    }
                }else{// 非营业中 2 歇业中 4 停业[店铺人工操作 8  关业[店铺平台操作]
                    // 非 1营业中 2 歇业中
                    // 非营业中的 经营状态  1营业中 2 歇业中 4 停业[店铺人工操作 8  关业[店铺平台操作]
                    if(!in_array($v['status_business'], [1,2])){
                        continue;
                    }
                }
                unset($shopList[$k]);
            }
            $shopList = array_values($shopList);
            if(!empty($shopList)){
                $temShopIds = array_values(array_unique(array_column($shopList, 'id')));
                $shopIdsOperateOnLine = array_values(array_diff($shopIdsOperateOnLine, $temShopIds));
            }
        }
        if(empty($shopIdsOperateOnLine) && empty($shopIdsOperateDownLine)) return true;
        $shopIdsAllOnLine = array_values(array_unique(array_merge($shopIdsInLine, $shopIdsOperateOnLine)));

        DB::beginTransaction();
        try {
            // 对歇业中的审核通过的进行上线操作
            if(!empty($shopIdsOperateOnLine)){
                $saveQueryParams = [
                    'where' => [
                        // ['order_type', 4],
                        // ['staff_id', $operate_staff_id],
                        ['status', 1],
                        ['status_business', 2],
                        // ['status_business', '!=', 1],
                    ],
                    /*
                     *
                    'select' => [
                        'id','title','sort_num','volume'
                    ],
                     *
                     */
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
                $saveQueryParams['whereIn']['id'] = $shopIdsOperateOnLine;
                if($city_site_id > 0 )  array_push($saveQueryParams['where'], ['city_site_id', '=', $city_site_id]);
                $saveDate = [
                    'status_business' => 1,
                ];
                ShopDBBusiness::save($saveDate, $saveQueryParams);
            }

            // 对营业中的进行下线操作
            if(!empty($shopIdsOperateDownLine)){
                $saveQueryParams = [
                    'where' => [
                        // ['order_type', 4],
                        // ['staff_id', $operate_staff_id],
                        // ['status', 1],
                        ['status_business', 1],
                        // ['status_business', '!=', 1],
                    ],
                    /*
                     *
                    'select' => [
                        'id','title','sort_num','volume'
                    ],
                     *
                     */
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
                $saveQueryParams['whereIn']['id'] = $shopIdsOperateDownLine;
                if($city_site_id > 0 )  array_push($saveQueryParams['where'], ['city_site_id', '=', $city_site_id]);
                $saveDate = [
                    'status_business' => 2,
                ];
                ShopDBBusiness::save($saveDate, $saveQueryParams);
            }
            // 缓存在营业中的店铺id
             Tool::setRedis('shop:', 'online' . $city_site_id , $shopIdsAllOnLine, 0 , 1);

        } catch ( \Exception $e) {
            DB::rollBack();
            throws('店铺上线操作失败；信息[' . $e->getMessage() . ']');
            // throws($e->getMessage());
        }
        DB::commit();
    }
}
