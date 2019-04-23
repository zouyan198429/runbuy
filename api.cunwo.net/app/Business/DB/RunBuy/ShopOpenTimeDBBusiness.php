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
        if(!is_array($shopIdsOnLine)) $shopIdsOnLine = [];
        // 获得所有的店铺id
        // $shopIdsOperateAll = array_values(array_unique(array_merge($shopIdsNeed,$shopIdsOnLine)));

        // 获得真正需要上线的店铺id
        $shopIdsOperateOnLine = array_values(array_diff($shopIdsNeed, $shopIdsOnLine));
        // 获得需要下线的店铺id
        $shopIdsOperateDownLine = array_values(array_diff($shopIdsOnLine, $shopIdsNeed));
        // 获得还在线上的店铺id
        $shopIdsInLine = array_values(array_diff($shopIdsOnLine, $shopIdsOperateDownLine));

        // 还在线上的和将要上线的店铺id，判断是否有非营业中的，有则要进行下线
        // 将要上线的店铺id
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
                // 营业中的,保留,不上线
                if($v['status_business'] == 1  ){
                    continue;
                }
                unset($shopList[$k]);

            }
            $shopList = array_values($shopList);
            if(!empty($shopList)){
                $temShopIds = array_values(array_unique(array_column($shopList, 'id')));
                $shopIdsOperateOnLine = array_values(array_diff($shopIdsOperateOnLine, $temShopIds));
            }
        }
        // 还在线上的
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
            // 去掉未审核通过和是营业中的
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
