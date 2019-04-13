<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPIOrdersBusiness;
use App\Business\Controller\API\RunBuy\CTAPIOrdersDoingBusiness;
use App\Business\Controller\API\RunBuy\CTAPIStaffBusiness;
use App\Services\Request\CommonRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends BaseController
{

    // 生成订单
    public function create(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    // 订单作废
    public function cancel(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    // 更新订单状态
    public function chState(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    //  订单--列表--有分页
    public function getList(Request $request){
        $this->InitParams($request);
        $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request){
        $this->InitParams($request);
        $user_id = $this->user_id;

        $relations = [
            'addrHistory', 'staffHistory', 'partnerHistory', 'sendHistory'
            ,'provinceHistory','cityHistory','areaHistory'
            , 'sellerHistory', 'shopHistory'
            ,'ordersGoods.goodsHistory'
            ,'ordersGoods.resourcesHistory'
            ,'ordersGoods.goodsPriceHistory.propName'
            ,'ordersGoods.goodsPriceHistory.propValName'
            ,'ordersGoods.props.propName'
            ,'ordersGoods.props.propValName'
        ];

        $status = CommonRequest::get($request, 'status');

        $getType = CommonRequest::get($request, 'getType');// 数据类型 1用户的订单2 待接单的订单 3派送人员接的单 4 已完成 派送人员接的单
        if(empty($getType) || !is_numeric($getType)) $getType = 1;

        // 数字或单条
        $statusArr = [];
        if(!empty($status)){
            if( is_numeric($status) ||  (is_string($status) && strpos($status, ',') === false) ){
                if($status != '') array_push($statusArr, $status);
            }else{// 其它的转为数组
                if(is_string($status)) $statusArr = explode(',', $status);
                if(!is_array($statusArr)) $statusArr = [];
            }
        }
        // if(empty($status)) throws('参数[status]不能为空');

        $requestParams = [
            'order_type' => 1,// 订单类型1普通订单/父订单4子订单
            // 'staff_id' => $user_id,
        ];
        $oprateBit = 2 + 4;
        switch ($getType)
        {
            case 1:// 1用户的订单
                $requestParams['staff_id'] = $user_id;
                break;
            case 2:// 2 待接单的订单-- 所有的订单
                $oprateBit = 1;

                $staffInfo = CTAPIStaffBusiness::getInfoData($request, $this, $user_id, ['city_site_id'],'' );// , ['city']

                $requestParams['city_site_id'] = $staffInfo['city_site_id'] ?? 0;
                $requestParams['send_staff_id'] = 0;
                break;
            case 3:// 3派送人员接的单
            case 4:// 4 已完成 派送人员接的单
                $requestParams['send_staff_id'] = $user_id;
                break;
            default:

        }
        // 8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]-- 从历史表
        if(empty($statusArr) || in_array(8, $statusArr) || in_array(16, $statusArr) || in_array(32, $statusArr) || in_array(64, $statusArr)){
            //  显示到定位点的距离
            CTAPIOrdersBusiness::mergeRequest($request, $this, $requestParams);
            $result = CTAPIOrdersBusiness::getList($request, $this, $oprateBit, [], $relations);
            $data_list = $result['result']['data_list'] ?? [];
            $parent_orders = $result['result']['parent_orders'] ?? [];
            $childList = [];
            if(!empty($parent_orders)){
                CTAPIOrdersBusiness::mergeRequest($request, $this, [
                    'order_type' => 4,// 订单类型1普通订单/父订单4子订单
                    'parent_order_no' => implode(',', $parent_orders),
                ]);
                $childResult = CTAPIOrdersBusiness::getList($request, $this, 1, [], $relations);
                $childList = $childResult['result']['data_list'] ?? [];
            }
        } else {
            //  显示到定位点的距离
            CTAPIOrdersDoingBusiness::mergeRequest($request, $this, $requestParams);
            $result = CTAPIOrdersDoingBusiness::getList($request, $this, $oprateBit, [], $relations);
            $data_list = $result['result']['data_list'] ?? [];
            $parent_orders = $result['result']['parent_orders'] ?? [];
            $childList = [];
            if(!empty($parent_orders)){
                CTAPIOrdersDoingBusiness::mergeRequest($request, $this, [
                    'order_type' => 4,// 订单类型1普通订单/父订单4子订单
                    'parent_order_no' => implode(',', $parent_orders),
                ]);
                $childResult = CTAPIOrdersDoingBusiness::getList($request, $this, 1, [], $relations);
                $childList = $childResult['result']['data_list'] ?? [];
            }
        }


        $formatChildList = [];
        foreach ($childList as $k => $v){
            $formatChildList[$v['parent_order_no']][] = $v;
        }

        foreach($data_list as $k => $v){
            $parent_order_no = $v['order_no'] ?? '';
            $has_son_order = $v['has_son_order'] ?? 0;// 是否有子订单0无1有
            $childOrder = $formatChildList[$parent_order_no] ?? [];
            if($has_son_order == 1 ){// 有子订单
                $data_list[$k]['shopList'] = $childOrder;
            }else{
                $data_list[$k]['shopList'][] = $v;
                if(isset($v['orders_goods'])) unset($data_list[$k]['orders_goods']);
            }
        }

        $data_list = array_values($data_list);
        $result['result']['data_list'] = $data_list;
        return $result;
    }


    /**
     * ajax获得统计数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_getCountByStatus(Request $request)
    {
        $this->InitParams($request);
        $user_id = $this->user_id;

        $getType = CommonRequest::get($request, 'getType');// 数据类型 1用户的订单2 待接单的订单 3派送人员接的单
        if(empty($getType) || !is_numeric($getType)) $getType = 1;

        $otherWhere = [
            ['order_type', '=', 1]// // 订单类型1普通订单/父订单4子订单
           // ,['staff_id', '=', $user_id]
        ];//  其它条件[['company_id', '=', $company_id],...]

        switch ($getType)
        {
            case 1:// 1用户的订单
                array_push($otherWhere, ['staff_id', '=', $user_id]);
                $orderStatus = [
                    [
                        'text' => '全部',
                        'status' => '',
                        'count' => 0,
                    ],
                    [
                        'text' => '待付款',
                        'status' => 1,
                        'count' => 0,
                    ],
                    [
                        'text' => '待接单',
                        'status' => 2,
                        'count' => 0,
                    ],
                    [
                        'text' => '配送中',
                        'status' => 4,
                        'count' => 0,
                    ],
                    [
                        'text' => '已完成',
                        'status' => '8,64',
                        'count' => 0,
                    ],
                    [
                        'text' => '已取消',
                        'status' => '16,32',
                        'count' => 0,
                    ],
                ];
                $status = '1,2,4';// 订单状态,多个用逗号分隔, 可为空：所有的
                break;
            case 2:// 2 待接单的订单

                $staffInfo = CTAPIStaffBusiness::getInfoData($request, $this, $user_id, ['city_site_id'],'' );// , ['city']

                $city_site_id = $staffInfo['city_site_id'] ?? 0;
                array_push($otherWhere, ['city_site_id', '=', $city_site_id]);
                array_push($otherWhere, ['send_staff_id', '=', 0]);
                $orderStatus = [
                     [
                        'text' => '待接单',
                        'status' => 2,
                        'count' => 0,
                    ],
//                    [
//                        'text' => '配送中',
//                        'status' => 4,
//                        'count' => 0,
//                    ],
//                    [
//                        'text' => '已完成',
//                        'status' => '8,64',
//                        'count' => 0,
//                    ]
                ];
                $status = '2';// 订单状态,多个用逗号分隔, 可为空：所有的
                break;
            case 3:// 3派送人员接的单
                array_push($otherWhere, ['send_staff_id', '=', $user_id]);
                $orderStatus = [
                    [
                        'text' => '配送中',
                        'status' => 4,
                        'count' => 0,
                    ],
//                    [
//                        'text' => '已完成',
//                        'status' => '8,64',
//                        'count' => 0,
//                    ]
                ];
                $status = '4';// 订单状态,多个用逗号分隔, 可为空：所有的
                break;
            case 4:// 4派送人员接的单--已完成
                array_push($otherWhere, ['send_staff_id', '=', $user_id]);
                $orderStatus = [
//                    [
//                        'text' => '配送中',
//                        'status' => 4,
//                        'count' => 0,
//                    ],
                    [
                        'text' => '已完成',
                        'status' => '8,64',
                        'count' => 0,
                    ]
                ];
                $status = '';// 订单状态,多个用逗号分隔, 可为空：所有的
                break;
            default:

        }
        if(!empty($status)){
            $statusCountList = CTAPIOrdersBusiness::getStatusCount($request, $this, $status, $otherWhere, 1);
            foreach($orderStatus as $k => $v){
                $t_status = $v['status'] ?? '';
                if($t_status == '') continue;
                if(isset($statusCountList[$t_status]) && is_numeric($statusCountList[$t_status])){
                    $orderStatus[$k]['count'] = $statusCountList[$t_status];
                }
            }
        }
        return ajaxDataArr(1, $orderStatus, '');
    }


    // 订单详情--根据订单号
    public function getInfoByOrderNoDoing(Request $request){
        $this->InitParams($request);

        $order_no = CommonRequest::get($request, 'order_no');
        // 根据订单号查询订单

        $user_id = $this->user_id;

        $queryParams = [
            'where' => [
                ['order_type', '=', 1],
                ['staff_id', '=', $user_id],
                ['order_no', '=', $order_no],
                // ['id', '&' , '16=16'],
                // ['company_id', $company_id],
                // ['admin_type',self::$admin_type],
            ],
            // 'whereIn' => [
            //   'id' => $subjectHistoryIds,
            //],
//            'select' => [
//                'id'
//            ],
            // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
        ];
        $resultDatas = CTAPIOrdersDoingBusiness::getInfoByQuery($request, $this, '', $this->company_id, $queryParams);
        // $resultDatas = [];
        return ajaxDataArr(1, $resultDatas, '');
    }

    //

    /**
     * ajax抢单
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function grabOrder(Request $request)
    {
        $this->InitParams($request);
        $send_staff_id = $this->user_id;
        $order_no = CommonRequest::get($request, 'order_no');// 订单号,多个用逗号分隔
        $result = CTAPIOrdersDoingBusiness::grabOrder($request, $this, $order_no, $send_staff_id);
        return ajaxDataArr(1, $result, '');
    }

    /**
     * ajax 订单完成
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function finishOrder(Request $request)
    {
        $this->InitParams($request);

        $send_staff_id = $this->user_id;
        $order_no = CommonRequest::get($request, 'order_no');// 订单号,多个用逗号分隔
        $result = CTAPIOrdersDoingBusiness::finishOrder($request, $this, $order_no, $send_staff_id);
        return ajaxDataArr(1, $result, '');
    }

    /**
     * ajax 每30秒或1分钟去执行一次的方法,获得这段时间内的待接订单
     *   参数 city_site_id 城市id
     *        order_id 订单id
     *            第一次为：0： 直接返回当前最大的订单id
     *            最大订单id :  1：获得大于当前订单id的待接订单及数量，同时获得当前最大的订单id
     * @param Request $request
     * @return mixed 这段时间内的待接订单数量
     * @author zouyan(305463219@qq.com)
     */
    public function eachDoOrder(Request $request)
    {
        $this->InitParams($request);
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        $order_id = CommonRequest::getInt($request, 'order_id');
        $operate_type = CommonRequest::getInt($request, 'operate_type');// 操作类型 1 商家 或者 店铺 2 非商家 或者 店铺
        $status = CommonRequest::get($request, 'status');// 状态, 多个用逗号,分隔 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
        if(empty($status)) $status = 2;
        $send_staff_id = $this->user_id;
        $other_where = [];
        $result = CTAPIOrdersDoingBusiness::getWaitOrder($request, $this, $operate_type, $status, $city_site_id, $order_id, $other_where, $send_staff_id);
        return ajaxDataArr(1, $result, '');
    }
}
