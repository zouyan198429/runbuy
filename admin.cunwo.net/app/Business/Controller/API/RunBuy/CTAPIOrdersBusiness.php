<?php
// 订单
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIOrdersBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\OrdersAPI';

    // 状态1待支付2等待接单4取货或配送中8订单完成16作废
    public static $status_arr = [
        '1' => '待付款',
        '2' => '待接单',
        '4' => '配送中',// '取货或配送中',
        '8' => '已完成',
        '16' => '已取消',// '作废'
        '32' => '用户取消',
        '64' => '完成',
    ];
    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码
     * @param string $queryParams 条件数组/json字符
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *        'useQueryParams' => '是否用来拼接查询条件，true:用[默认];false：不用'
     *        'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ]
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getList(Request $request, Controller $controller, $oprateBit = 2 + 4, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        $company_id = $controller->company_id;

        // 获得数据
        $defaultQueryParams = [
            'where' => [
//                ['company_id', $company_id],
//                //['mobile', $keyword],
            ],
//            'select' => [
//                'id','company_id','position_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                ,'created_at'
//            ],
            'orderBy' => [ 'id'=>'desc'],// 'sort_num'=>'desc',
        ];// 查询条件参数
        if(empty($queryParams)){
            $queryParams = $defaultQueryParams;
        }
        $isExport = 0;

        $useSearchParams = $extParams['useQueryParams'] ?? true;// 是否用来拼接查询条件，true:用[默认];false：不用
        // 其它sql条件[覆盖式]
        $sqlParams = $extParams['sqlParams'] ?? [];
        $sqlKeys = array_keys($sqlParams);
        foreach($sqlKeys as $tKey){
            // if(isset($sqlParams[$tKey]) && !empty($sqlParams[$tKey]))  $queryParams[$tKey] = $sqlParams[$tKey];
            if(isset($sqlParams[$tKey]) )  $queryParams[$tKey] = $sqlParams[$tKey];
        }

        if($useSearchParams) {
            // $params = self::formatListParams($request, $controller, $queryParams);
//            $province_id = CommonRequest::getInt($request, 'province_id');
//            if($province_id > 0 )  array_push($queryParams['where'], ['city_ids', 'like', '' . $province_id . ',%']);
            $staff_id = CommonRequest::getInt($request, 'staff_id');
            if($staff_id > 0 )  array_push($queryParams['where'], ['staff_id', '=', $staff_id]);

            $send_staff_id = CommonRequest::getInt($request, 'send_staff_id');
            if($send_staff_id > 0 )  array_push($queryParams['where'], ['send_staff_id', '=', $send_staff_id]);

            $city_site_id = CommonRequest::getInt($request, 'city_site_id');
            if($city_site_id > 0 )  array_push($queryParams['where'], ['city_site_id', '=', $city_site_id]);

            $city_partner_id = CommonRequest::getInt($request, 'city_partner_id');
            if($city_partner_id > 0 )  array_push($queryParams['where'], ['city_partner_id', '=', $city_partner_id]);

            $seller_id = CommonRequest::getInt($request, 'seller_id');
            if($seller_id > 0 )  array_push($queryParams['where'], ['seller_id', '=', $seller_id]);

            $shop_id = CommonRequest::getInt($request, 'shop_id');
            if($shop_id > 0 )  array_push($queryParams['where'], ['shop_id', '=', $shop_id]);

            $order_type = CommonRequest::getInt($request, 'order_type');
            if($order_type > 0 )  array_push($queryParams['where'], ['order_type', '=', $order_type]);

            $has_son_order = CommonRequest::get($request, 'has_son_order');
            if(is_numeric($has_son_order) )  array_push($queryParams['where'], ['has_son_order', '=', $has_son_order]);

            $order_no = CommonRequest::get($request, 'order_no');
            if(!empty($order_no) )  array_push($queryParams['where'], ['order_no', '=', $order_no]);

            $parent_order_no = CommonRequest::get($request, 'parent_order_no');
            // if(!empty($parent_order_no) )  array_push($queryParams['where'], ['parent_order_no', '=', $parent_order_no]);
            if(!empty($parent_order_no)){
                if (strpos($parent_order_no, ',') === false) { // 单条
                    array_push($queryParams['where'], ['parent_order_no', $parent_order_no]);
                } else {
                    $queryParams['whereIn']['parent_order_no'] = explode(',', $parent_order_no);
                }
            }

            $province_id = CommonRequest::getInt($request, 'province_id');
            if($province_id > 0 )  array_push($queryParams['where'], ['province_id', '=', $province_id]);

            $city_id = CommonRequest::getInt($request, 'city_id');
            if($city_id > 0 )  array_push($queryParams['where'], ['city_id', '=', $city_id]);

            $area_id = CommonRequest::getInt($request, 'area_id');
            if($area_id > 0 )  array_push($queryParams['where'], ['area_id', '=', $area_id]);

            $status = CommonRequest::get($request, 'status');
            // if(is_numeric($status) )  array_push($queryParams['where'], ['status', '=', $status]);
            if(!empty($status)){

                if (strpos($status, ',') === false) { // 单条
                    array_push($queryParams['where'], ['status', $status]);
                } else {
                    $queryParams['whereIn']['status'] = explode(',', $status);
                }
                // 如果有状态 待接单，则把退款中的也去掉 2等待接单
                if(strpos(',' . $status . ',', ',2,') !== false){
                    array_push($queryParams['where'], ['has_refund', '!=', 2]); // 是否退费0未退费1已退费2待退费
                }
            }


            $pay_type = CommonRequest::get($request, 'pay_type');
            if(is_numeric($pay_type) )  array_push($queryParams['where'], ['pay_type', '=', $pay_type]);

            $pay_run_price = CommonRequest::get($request, 'pay_run_price');
            if(is_numeric($pay_run_price) )  array_push($queryParams['where'], ['pay_run_price', '=', $pay_run_price]);

            $pay_order_no = CommonRequest::get($request, 'pay_order_no');
            if(!empty($pay_order_no) )  array_push($queryParams['where'], ['pay_order_no', '=', $pay_order_no]);

            $has_refund = CommonRequest::get($request, 'has_refund');
            if(is_numeric($has_refund) )  array_push($queryParams['where'], ['has_refund', '=', $has_refund]);

            $refund_pay_order_no = CommonRequest::get($request, 'refund_pay_order_no');
            if(!empty($refund_pay_order_no) )  array_push($queryParams['where'], ['refund_pay_order_no', '=', $refund_pay_order_no]);

            $addr_id = CommonRequest::getInt($request, 'addr_id');
            if($addr_id > 0 )  array_push($queryParams['where'], ['addr_id', '=', $addr_id]);


            $field = CommonRequest::get($request, 'field');
            $keyWord = CommonRequest::get($request, 'keyword');
            if (!empty($field) && !empty($keyWord)) {
                array_push($queryParams['where'], [$field, 'like', '%' . $keyWord . '%']);
            }

            $ids = CommonRequest::get($request, 'ids');// 多个用逗号分隔,
            if (!empty($ids)) {
                if (strpos($ids, ',') === false) { // 单条
                    array_push($queryParams['where'], ['id', $ids]);
                } else {
                    $queryParams['whereIn']['id'] = explode(',', $ids);
                }
            }
            $isExport = CommonRequest::getInt($request, 'is_export'); // 是否导出 0非导出 ；1导出数据
            if ($isExport == 1) $oprateBit = 1;
        }
        // $relations = ['CompanyInfo'];// 关系
        // $relations = '';//['CompanyInfo'];// 关系
        $result = static::getBaseListData($request, $controller, '', $queryParams, $relations , $oprateBit, $notLog);

        // 格式化数据
        $data_list = $result['data_list'] ?? [];
        $parentOrderNos = [];// 有子订单的订单数组
        foreach($data_list as $k => $v){
            $order_no = $v['order_no'] ?? '';
            $parent_order_no = $v['parent_order_no'] ?? '';
            $has_son_order = $v['has_son_order'] ?? 0;// 是否有子订单0无1有
            $data_list[$k]['order_no_format'] = Tool::formatStrMiddle($order_no, ' ', 4);
            $data_list[$k]['parent_order_no_format'] = Tool::formatStrMiddle($parent_order_no, ' ', 4);
            if($has_son_order == 1 ) array_push($parentOrderNos, $order_no);

            // 收货地址
            if(isset($v['addr_history']) && !empty($v['addr_history'])){
                $addr_history = $v['addr_history'] ?? [];
                unset($data_list[$k]['addr_history']);
                $data_list[$k]['addr'] = Tool::formatArrKeys($addr_history
                    , Tool::arrEqualKeyVal(['addr_id', 'real_name', 'sex', 'sex_text', 'tel', 'mobile', 'addr_name', 'addr', 'longitude', 'latitude']), true );
                $data_list[$k]['addr']['mobile_format'] = Tool::formatStr($data_list[$k]['addr']['mobile'], [
                    ['len' => 3, 'splitStr' => ' ']
                    , ['len' => 4, 'splitStr' => ' ']
                    , ['len' => 4, 'splitStr' => ' ']
                ]);
            }
            // 买家
            if(isset($v['staff_history']) && !empty($v['staff_history'])){
                $staff_history = $v['staff_history'] ?? [];
                unset($data_list[$k]['staff_history']);
                $data_list[$k]['staff'] = Tool::formatArrKeys($staff_history
                    , Tool::arrEqualKeyVal(['staff_id', 'nickname', 'gender', 'province', 'city', 'country', 'avatar_url', 'longitude', 'latitude', 'sex_text']), true );
            }

            // 城市-省
            if(isset($v['province_history']) && !empty($v['province_history'])){
                $province_history = $v['province_history'] ?? [];
                unset($data_list[$k]['province_history']);
                $data_list[$k]['province'] = Tool::formatArrKeys($province_history
                    , Tool::arrEqualKeyVal(['city_table_id', 'city_ids', 'city_name', 'code', 'head', 'initial', 'longitude', 'latitude']), true );
            }

            // 城市--市
            if(isset($v['city_history']) && !empty($v['city_history'])){
                $city_history = $v['city_history'] ?? [];
                unset($data_list[$k]['city_history']);
                $data_list[$k]['city'] = Tool::formatArrKeys($city_history
                    , Tool::arrEqualKeyVal(['city_table_id', 'city_ids', 'city_name', 'code', 'head', 'initial', 'longitude', 'latitude']), true );
            }

            // 城市--县
            if(isset($v['area_history']) && !empty($v['area_history'])){
                $area_history = $v['area_history'] ?? [];
                unset($data_list[$k]['area_history']);
                $data_list[$k]['area'] = Tool::formatArrKeys($area_history
                    , Tool::arrEqualKeyVal(['city_table_id', 'city_ids', 'city_name', 'code', 'head', 'initial', 'longitude', 'latitude']), true );
            }

            // 城市代理
            if(isset($v['partner_history']) && !empty($v['partner_history'])){
                $partner_history = $v['partner_history'] ?? [];
                unset($data_list[$k]['partner_history']);
                $data_list[$k]['partner'] = Tool::formatArrKeys($partner_history
                    , Tool::arrEqualKeyVal(['city_partner_id', 'partner_name', 'linkman', 'mobile', 'tel', 'addr', 'longitude', 'latitude']), true );
            }

            // 商家
            if(isset($v['seller_history']) && !empty($v['seller_history'])){
                $seller_history = $v['seller_history'] ?? [];
                unset($data_list[$k]['seller_history']);
                $data_list[$k]['seller'] = Tool::formatArrKeys($seller_history
                    , Tool::arrEqualKeyVal(['seller_id', 'seller_name', 'linkman', 'mobile', 'tel', 'addr', 'longitude', 'latitude']), true );
            }

            // 店铺
            if(isset($v['shop_history']) && !empty($v['shop_history'])){
                $shop_history = $v['shop_history'] ?? [];
                unset($data_list[$k]['shop_history']);
                $data_list[$k]['shop'] = Tool::formatArrKeys($shop_history
                    , Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile', 'tel', 'addr', 'longitude', 'latitude']), true );
            }

            // 商品
            if(isset($v['orders_goods']) && !empty($v['orders_goods'])) {
                $formatGoods = [];
                $orders_goods = $v['orders_goods'] ?? [];
                unset($data_list[$k]['orders_goods']);
                foreach($orders_goods as $gK =>$goodInfo){
                    // 商品名称
                    if(isset($goodInfo['goods_history']) && !empty($goodInfo['goods_history'])) {
                        $goodsHistory = $goodInfo['goods_history'] ?? [];
                        unset($goodInfo['goods_history']);
                        $goodInfo['goods_name'] = $goodsHistory['goods_name'] ?? '';
                    }
                    // 商品图片
                    $resource_url = $goodInfo['resources_history']['resource_url'] ?? '';
                    if(!empty($resource_url))  $resource_url = url($resource_url);
                    $goodInfo['resource_url'] = $resource_url;
                    // 商品价格属性
                    $goodInfo['pricePropName'] = $goodInfo['goods_price_history']['prop_name']['main_name'] ?? '';
                    $goodInfo['pricePropValName'] = $goodInfo['goods_price_history']['prop_val_name']['main_name'] ?? '';

                    // 商品属性
                    $temProps = $goodInfo['props'] ?? [];
                    if(isset($goodInfo['props'])) unset($goodInfo['props']);
                    $formatProps = [];
                    foreach($temProps as $propInfo){
                        if(!isset($formatProps[$propInfo['prop_id']])){
                            $formatProps[$propInfo['prop_id']] = [
                                'prop_id' => $propInfo['prop_id'] ?? 0,
                                'prop_name' => $propInfo['prop_name']['main_name'] ?? '',
                            ];
                        }
                        $formatProps[$propInfo['prop_id']]['prop_val'][] = [
                            'prop_val_id' => $propInfo['prop_val_id'] ?? 0,
                            'prop_val_name' => $propInfo['prop_val_name']['main_name'] ?? '',
                        ];
                    }
                    foreach($formatProps as $t_k => $t_v){
                        $prop_vals = $t_v['prop_val'] ?? [];
                        $t_pv_names = array_column($prop_vals, 'prop_val_name');
                        $formatProps[$t_k]['pv_names'] = implode('、', $t_pv_names);
                    }
                    $goodInfo['prop'] = array_values($formatProps);

                    array_push($formatGoods, Tool::formatArrKeys($goodInfo
                        , Tool::arrEqualKeyVal(['goods_id', 'goods_name', 'price', 'amount', 'total_price', 'resource_url'
                            , 'pricePropName', 'pricePropValName', 'prop']), true ));
                    $data_list[$k]['orders_goods'] = $formatGoods;
                }

            }
            // 所属人员
//            $data_list[$k]['nickname'] = $v['staff']['nickname'] ?? '';
//            $data_list[$k]['staff_id'] = $v['staff']['id'] ?? 0;
//            if(isset($data_list[$k]['staff'])) unset($data_list[$k]['staff']);
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
        }
        $result['data_list'] = $data_list;
        $result['parent_orders'] = $parentOrderNos;// // 有子订单的订单数组
        // 导出功能
        if($isExport == 1){
//            $headArr = ['work_num'=>'工号', 'department_name'=>'部门'];
//            ImportExport::export('','excel文件名称',$data_list,1, $headArr, 0, ['sheet_title' => 'sheet名称']);
            die;
        }
        // 非导出功能
        return ajaxDataArr(1, $result, '');
    }

    /**
     * 根据id获得单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id id
     * @param array $selectParams 查询字段参数--一维数组
     * @param mixed $relations 关系
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoData(Request $request, Controller $controller, $id, $selectParams = [], $relations = '', $notLog = 0){
        $company_id = $controller->company_id;
        // $relations = '';
        // $resultDatas = APIRunBuyRequest::getinfoApi(self::$model_name, '', $relations, $company_id , $id);
        $info = static::getInfoDataBase($request, $controller,'', $id, $selectParams, $relations, $notLog);
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $info, $judgeData );
        return $info;
    }

    /**
     * 格式化列表查询条件-暂不用
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $queryParams 条件数组/json字符
     * @return  array 参数数组 一维数据
     * @author zouyan(305463219@qq.com)
     */
//    public static function formatListParams(Request $request, Controller $controller, &$queryParams = []){
//        $params = [];
//        $title = CommonRequest::get($request, 'title');
//        if(!empty($title)){
//            $params['title'] = $title;
//            array_push($queryParams['where'],['title', 'like' , '%' . $title . '%']);
//        }
//
//        $ids = CommonRequest::get($request, 'ids');// 多个用逗号分隔,
//        if (!empty($ids)) {
//            $params['ids'] = $ids;
//            if (strpos($ids, ',') === false) { // 单条
//                array_push($queryParams['where'],['id', $ids]);
//            }else{
//                $queryParams['whereIn']['id'] = explode(',',$ids);
//                $params['idArr'] = explode(',',$ids);
//            }
//        }
//        return $params;
//    }

    /**
     * 获得当前记录前/后**条数据--二维数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id 当前记录id
     * @param int $nearType 类型 1:前**条[默认]；2后**条 ; 4 最新几条;8 有count下标则是查询数量, 返回的数组中total 就是真实的数量
     * @param int $limit 数量 **条
     * @param int $offset 偏移数量
     * @param string $queryParams 条件数组/json字符
     * @param mixed $relations 关系
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据 - 二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getNearList(Request $request, Controller $controller, $id = 0, $nearType = 1, $limit = 1, $offset = 0, $queryParams = [], $relations = '', $notLog = 0)
    {
        $company_id = $controller->company_id;
        // 前**条[默认]
        $defaultQueryParams = [
            'where' => [
                //  ['company_id', $company_id],
//                ['id', '>', $id],
            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                ,'created_at'
//            ],
//            'orderBy' => ['sort_num'=>'desc','id'=>'desc'],
            'orderBy' => ['id'=>'asc'],
            'limit' => $limit,
            'offset' => $offset,
            // 'count'=>'0'
        ];
        if(($nearType & 1) == 1){// 前**条
            $defaultQueryParams['orderBy'] = ['id'=>'asc'];
            array_push($defaultQueryParams['where'],['id', '>', $id]);
        }

        if(($nearType & 2) == 2){// 后*条
            array_push($defaultQueryParams['where'],['id', '<', $id]);
            $defaultQueryParams['orderBy'] = ['id'=>'desc'];
        }

        if(($nearType & 4) == 4){// 4 最新几条
            $defaultQueryParams['orderBy'] = ['id'=>'desc'];
        }

        if(($nearType & 8) == 8){// 8 有count下标则是查询数量, 返回的数组中total 就是真实的数量
            $defaultQueryParams['count'] = 0;
        }

        if(empty($queryParams)){
            $queryParams = $defaultQueryParams;
        }
        $result = static::getList($request, $controller, 1 + 0, $queryParams, $relations, [], $notLog);
        // 格式化数据
        $data_list = $result['result']['data_list'] ?? [];
        if($nearType == 1) $data_list = array_reverse($data_list); // 相反;
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
//        $result['result']['data_list'] = $data_list;
        return $data_list;
    }

    /**
     * 导入模版
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function importTemplate(Request $request, Controller $controller)
    {
//        $headArr = ['work_num'=>'工号', 'department_name'=>'部门'];
//        $data_list = [];
//        ImportExport::export('','员工导入模版',$data_list,1, $headArr, 0, ['sheet_title' => '员工导入模版']);
        die;
    }
    /**
     * 删除单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delAjax(Request $request, Controller $controller, $notLog = 0)
    {
        $company_id = $controller->company_id;
        // $id = CommonRequest::getInt($request, 'id');
        return static::delAjaxBase($request, $controller, '', $notLog);

    }


    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $id id
     * @param boolean $modifAddOprate 修改时是否加操作人，true:加;false:不加[默认]
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById(Request $request, Controller $controller, $saveData, &$id, $modifAddOprate = false, $notLog = 0){

        $company_id = $controller->company_id;
        if($id > 0){
            // 判断权限
//            $judgeData = [
//                'company_id' => $company_id,
//            ];
//            $relations = '';
//            static::judgePower($request, $controller, $id, $judgeData, '', $company_id, $relations, $notLog);
            if($modifAddOprate) static::addOprate($request, $controller, $saveData);

        }else {// 新加;要加入的特别字段
            $addNewData = [
                //  'company_id' => $company_id,
            ];
            $saveData = array_merge($saveData, $addNewData);
            // 加入操作人员信息
            static::addOprate($request, $controller, $saveData);
        }
        // 新加或修改
        return static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);
    }

    // ***********导入***开始************************************************************
    /**
     * 批量导入
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function import(Request $request, Controller $controller, $saveData , $notLog = 0)
    {
        $company_id = $controller->company_id;
        // 参数
        $requestData = [
            'company_id' => $company_id,
            'staff_id' =>  $controller->user_id,
            'admin_type' =>  self::$admin_type,
            'save_data' => $saveData,
        ];
        $url = config('public.apiUrl') . config('apiUrl.apiPath.staffImport');
        // 生成带参数的测试get请求
        // $requestTesUrl = splicQuestAPI($url , $requestData);
        return HttpRequest::HttpRequestApi($url, $requestData, [], 'POST');
    }

    /**
     * 批量导入员工--通过文件路径
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $fileName 文件全路径
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function importByFile(Request $request, Controller $controller, $fileName = '', $notLog = 0){
        // $fileName = 'staffs.xlsx';
        $dataStartRow = 1;// 数据开始的行号[有抬头列，从抬头列开始],从1开始
        // 需要的列的值的下标关系：一、通过列序号[1开始]指定；二、通过专门的列名指定;三、所有列都返回[文件中的行列形式],$headRowNum=0 $headArr=[]
        $headRowNum = 1;//0:代表第一种方式，其它数字：第二种方式; 1开始 -必须要设置此值，$headArr 参数才起作用
        // 下标对应关系,如果设置了，则只获取设置的列的值
        // 方式一格式：['1' => 'name'，'2' => 'chinese',]
        // 方式二格式: ['姓名' => 'name'，'语文' => 'chinese',]
        $headArr = [
            '县区' => 'department',
            '归属营业厅或片区' => 'group',
            '姓名或渠道名称' => 'channel',
            //'姓名' => 'real_name',
            '工号' => 'work_num',
            '职务' => 'position',
            '手机号' => 'mobile',
            '性别' => 'sex',
        ];
//        $headArr = [
//            '1' => 'name',
//            '2' => 'chinese',
//            '3' => 'maths',
//            '4' => 'english',
//        ];
        try{
            $dataArr = ImportExport::import($fileName, $dataStartRow, $headRowNum, $headArr);
        } catch ( \Exception $e) {
            throws($e->getMessage());
        }
        return self::import($request, $controller, $dataArr, $notLog);
    }

    // ***********导入***结束************************************************************

    // ***********获得kv***开始************************************************************
    // 根据父id,获得子数据kv数组
    public static function getCityByPid(Request $request, Controller $controller, $parent_id = 0, $notLog = 0){
        $company_id = $controller->company_id;
        $kvParams = ['key' => 'id', 'val' => 'type_name'];
        $queryParams = [
            'where' => [
                // ['id', '&' , '16=16'],
                //    ['parent_id', '=', $parent_id],
                //['mobile', $keyword],
                //['admin_type',self::$admin_type],
            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//            ],
            'orderBy' => [ 'id'=>'desc'],// 'sort_num'=>'desc',
        ];
        return static::getKVCT( $request,  $controller, '', $kvParams, [], $queryParams, $company_id, $notLog);
    }

    // 根据父id,获得子数据kv数组
    public static function getListKV(Request $request, Controller $controller, $notLog = 0){
        $company_id = $controller->company_id;
        $kvParams = ['key' => 'id', 'val' => 'type_name'];
        $queryParams = [
            'where' => [
                // ['id', '&' , '16=16'],
                // ['parent_id', '=', $parent_id],
                //['mobile', $keyword],
                //['admin_type',self::$admin_type],
            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//            ],
            'orderBy' => [ 'id'=>'desc'],// 'sort_num'=>'desc',
        ];
        return static::getKVCT( $request,  $controller, '', $kvParams, [], $queryParams, $company_id, $notLog);
    }
    // ***********获得kv***结束************************************************************

    // ***********通过组织条件获得kv***开始************************************************************
    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $pid 当前父id
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据[一维的键=>值数组]
     * @author zouyan(305463219@qq.com)
     */
    public static function getChildListKeyVal(Request $request, Controller $controller, $pid, $oprateBit = 2 + 4, $notLog = 0){
        $parentData = self::getChildList($request, $controller, $pid, $oprateBit, $notLog);
        $department_list = $parentData['result']['data_list'] ?? [];
        return Tool::formatArrKeyVal($department_list, 'id', 'city_name');
    }
    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $pid 当前父id
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getChildList(Request $request, Controller $controller, $pid, $oprateBit = 2 + 4, $notLog = 0){
        $company_id = $controller->company_id;

        // 获得数据
        $queryParams = [
            'where' => [
//                ['company_id', $company_id],
                ['parent_id', $pid],
            ],
            'select' => [
                'id','city_name'// ,'sort_num'
                //,'operate_staff_id','operate_staff_history_id'
            ],
            'orderBy' => ['id'=>'asc'],// 'sort_num'=>'desc',
        ];// 查询条件参数
        // $relations = ['CompanyInfo'];// 关系
        $relations = '';//['CompanyInfo'];// 关系
        $result = static::getBaseListData($request, $controller, '', $queryParams, $relations , $oprateBit, $notLog);
        // 格式化数据
//        $data_list = $result['data_list'] ?? [];
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
//        $result['data_list'] = $data_list;
        return ajaxDataArr(1, $result, '');
    }
    // ***********通过组织条件获得kv***结束************************************************************

    /**
     * 根据状态，统计订单数量
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $status 订单状态,多个用逗号分隔, 可为空：所有的
     * @param array $otherWhere 其它条件[['company_id', '=', $company_id],...]
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function getStatusCount(Request $request, Controller $controller, $status, $otherWhere = [], $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;

        // 调用新加或修改接口
        $apiParams = [
            'status' => $status,// 订单状态,多个用逗号分隔, 可为空：所有的
            'company_id' => $company_id,
            'otherWhere' => $otherWhere,// 其它条件[['company_id', '=', $company_id],...]
            'operate_staff_id' => $user_id,
        ];
        $statusCountList = static::exeDBBusinessMethodCT($request, $controller, '', 'getGroupCount', $apiParams, $company_id, $notLog);
        return $statusCountList;
    }

    /**
     * 根据 订单号获得订单详情
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $order_no 订单号
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getOrderInfoByOrderNo(Request $request, Controller $controller, $order_no, $notLog = 0){
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        // 根据订单号查询订单

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
        return static::getInfoByQuery($request, $controller, '', $company_id, $queryParams);
    }
}