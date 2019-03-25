<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPIOrdersBusiness;
use App\Business\Controller\API\RunBuy\CTAPIOrdersDoingBusiness;
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
            'addrHistory', 'staffHistory', 'partnerHistory'
            ,'provinceHistory','cityHistory','areaHistory'
            , 'sellerHistory', 'shopHistory'
            ,'ordersGoods.goodsHistory'
            ,'ordersGoods.resourcesHistory'
            ,'ordersGoods.goodsPriceHistory.propName'
            ,'ordersGoods.goodsPriceHistory.propValName'
            ,'ordersGoods.props.propName'
            ,'ordersGoods.props.propValName'
        ];
        //  显示到定位点的距离
        CTAPIOrdersBusiness::mergeRequest($request, $this, [
            'order_type' => 1,// 订单类型1普通订单/父订单4子订单
            'staff_id' => $user_id,
        ]);
        $result = CTAPIOrdersBusiness::getList($request, $this, 2 + 4, [], $relations);
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
        $otherWhere = [
            ['order_type', '=', 1]// // 订单类型1普通订单/父订单4子订单
            ,['staff_id', '=', $user_id]
        ];//  其它条件[['company_id', '=', $company_id],...]
        $statusCountList = CTAPIOrdersBusiness::getStatusCount($request, $this, $status, $otherWhere, 1);
        foreach($orderStatus as $k => $v){
            $t_status = $v['status'] ?? '';
            if($t_status == '') continue;
            if(isset($statusCountList[$t_status]) && is_numeric($statusCountList[$t_status])){
                $orderStatus[$k]['count'] = $statusCountList[$t_status];
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
}
