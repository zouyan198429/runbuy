<?php
// 桌位
namespace App\Business\Controller\API\RunBuy;


use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\MiniProgram\QRCode;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPITablesBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\TablesAPI';
    public static $table_name = 'tables';// 表名称

    // 是否开启1未开启2已开启
    public static $isOpenArr = [
        '1' => '未开启',
        '2' => '已开启',
    ];

    // 状态1待占桌2已占桌4确认占桌
    public static $statusArr = [
        '1' => '待占桌',
        '2' => '已占桌',
        '4' => '确认占桌',
    ];

    /**
     * 获得扩展配置信息
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $pageKey  配置关键字下标
     * @param string $funKey  功能关键字下标 -- 可为空:不返回功能配置，返回页面配置
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 配置数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getExtendParamsConfig(Request $request, Controller $controller, $pageKey = '', $funKey = '', $notLog = 0){
        if(empty($pageKey)) return [];
        $extParams = [
            'list_page_admin' => [
                'relationsArr' => [// 列表用 $relations = ['city', 'cityPartner', 'seller', 'shop', 'shopHistory', 'tablePerson', 'tablePersonHistory'];
                    [
                        'relation_key' => ['site', 'resources'], 'sequence' => [1],
                        'return_data'=>[
                            'old_data' => ['ubound_operate' => 1, 'ubound_name' => '', 'fields_arr' => [],],
                            // 'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                            // 'one_field' => ['key' => 'city_name', 'return_type' => 2,'ubound_name' => 'site_name', 'split' => ''],
                            // 'child'=> [],
                        ],
                    ],
                    [
                        'relation_key' => ['city'], 'sequence' => [1],
                        'return_data'=>[
                            'old_data' => ['ubound_operate' => 2, 'ubound_name' => '', 'fields_arr' => [],],
                            // 'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                            'one_field' => ['key' => 'city_name', 'return_type' => 2,'ubound_name' => 'site_name', 'split' => ''],
                            // 'child'=> [],
                        ],
                    ],
                    [
                        'relation_key' => ['city', 'partner'], 'sequence' => [1],
                        'return_data'=>[
                            'old_data' => ['ubound_operate' => 2, 'ubound_name' => '', 'fields_arr' => [],],
                            // 'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                            'one_field' => ['key' => 'partner_name', 'return_type' => 2,'ubound_name' => 'partner_name', 'split' => ''],
                            // 'child'=> [],
                        ],
                    ],
                    [
                        'relation_key' => ['seller'], 'sequence' => [1],
                        'return_data'=>[
                            'old_data' => ['ubound_operate' => 2, 'ubound_name' => '', 'fields_arr' => [],],
                            // 'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                            'one_field' => ['key' => 'seller_name', 'return_type' => 2,'ubound_name' => 'seller_name', 'split' => ''],
                            // 'child'=> [],
                        ],
                    ],
                    [
                        'relation_key' => ['shop'], 'sequence' => [1,4],
                        'return_data'=>[
                            'old_data' => ['ubound_operate' => 2, 'ubound_name' => '', 'fields_arr' => [],],
                            // 'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                            'one_field' => ['key' => 'shop_name', 'return_type' => 2,'ubound_name' => 'shop_name', 'split' => ''],
                            // 'child'=> [],
                        ],
                    ],
                    [
                        'relation_key' => ['table', 'person'], 'sequence' => [1,4],
                        'return_data'=>[
                            'old_data' => ['ubound_operate' => 2, 'ubound_name' => '', 'fields_arr' => [],],
                            // 'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                            'one_field' => [
                                    ['key' => 'person_name', 'return_type' => 2,'ubound_name' => 'person_name', 'split' => ''],
                                    ['key' => 'prefix_name', 'return_type' => 2,'ubound_name' => 'prefix_name', 'split' => ''],
                                ],
                             'child'=> [
                                 [
                                     'relation_key' => ['num', 'prefix'], 'sequence' => [1],
                                     'return_data'=>[
                                         'old_data' => ['ubound_operate' => 2, 'ubound_name' => '', 'fields_arr' => [],],
                                         // 'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                                         'one_field' => [
                                             ['key' => 'prefix_name', 'return_type' => 2,'ubound_name' => 'prefix_name', 'split' => ''],
                                         ],
                                         // 'child'=> [],
                                     ],
                                 ],
                             ],
                        ],
                    ],
                ],
                'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
                    'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
                    'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
                    'exceptUboundArr' => ['version_num'], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
                ]
            ]
        ];
        $pageConfig = $extParams[$pageKey] ?? [];
        if(empty($funKey)) return $pageConfig;

        return $pageConfig[$funKey] ?? [];
    }

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
     *       ],
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
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
            'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],//
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

            $city_site_id = CommonRequest::getInt($request, 'city_site_id');
            if($city_site_id > 0 )  array_push($queryParams['where'], ['city_site_id', '=', $city_site_id]);

            $city_partner_id = CommonRequest::getInt($request, 'city_partner_id');
            if($city_partner_id > 0 )  array_push($queryParams['where'], ['city_partner_id', '=', $city_partner_id]);

            $seller_id = CommonRequest::getInt($request, 'seller_id');
            if($seller_id > 0 )  array_push($queryParams['where'], ['seller_id', '=', $seller_id]);

            $shop_id = CommonRequest::getInt($request, 'shop_id');
            if($shop_id > 0 )  array_push($queryParams['where'], ['shop_id', '=', $shop_id]);

            $shop_id_history = CommonRequest::getInt($request, 'shop_id_history');
            if($shop_id_history > 0 )  array_push($queryParams['where'], ['shop_id_history', '=', $shop_id_history]);

            $is_open = CommonRequest::get($request, 'is_open');
            if(is_numeric($is_open) && $is_open >= 0 )  array_push($queryParams['where'], ['is_open', '=', $is_open]);

            $table_person_id = CommonRequest::get($request, 'table_person_id');
            if(is_numeric($table_person_id) && $table_person_id > 0 )  array_push($queryParams['where'], ['table_person_id', '=', $table_person_id]);

            $table_person_id_history = CommonRequest::get($request, 'table_person_id_history');
            if(is_numeric($table_person_id_history) && $table_person_id_history > 0 )  array_push($queryParams['where'], ['table_person_id_history', '=', $table_person_id_history]);

            $table_stream_no = CommonRequest::get($request, 'table_stream_no');
            if(strlen($table_stream_no) > 0 )  array_push($queryParams['where'], ['table_stream_no', '=', $table_stream_no]);

            $has_qrcode = CommonRequest::get($request, 'has_qrcode');
            if(is_numeric($has_qrcode) && $has_qrcode >= 0 )  array_push($queryParams['where'], ['has_qrcode', '=', $has_qrcode]);


            $status = CommonRequest::get($request, 'status');
            // if(is_numeric($status) )  array_push($queryParams['where'], ['status', '=', $status]);
            if(!empty($status)){

                if (strpos($status, ',') === false) { // 单条
                    array_push($queryParams['where'], ['status', $status]);
                } else {
                    $queryParams['whereIn']['status'] = explode(',', $status);
                }
            }

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
        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据
        foreach($data_list as $k => $v){
            // 城市分站名称
//            $data_list[$k]['site_name'] = $v['city']['city_name'] ?? '';
//            // $data_list[$k]['site_id'] = $v['city']['id'] ?? 0;
//            if(isset($data_list[$k]['city'])) unset($data_list[$k]['city']);
//            // 城市城市合伙人
//            $data_list[$k]['partner_name'] = $v['city_partner']['partner_name'] ?? '';
//            // $data_list[$k]['partner_id'] = $v['city_partner']['id'] ?? 0;
//            if(isset($data_list[$k]['city_partner'])) unset($data_list[$k]['city_partner']);
//
//            // 商家
//            $data_list[$k]['seller_name'] = $v['seller']['seller_name'] ?? '';
//            // $data_list[$k]['seller_id'] = $v['seller']['id'] ?? 0;
//            if(isset($data_list[$k]['seller'])) unset($data_list[$k]['seller']);
//            // 铺店
//            $data_list[$k]['shop_name'] = $v['shop']['shop_name'] ?? '';
//            // $data_list[$k]['shop_id'] = $v['shop']['id'] ?? 0;
//            if(isset($data_list[$k]['shop'])) unset($data_list[$k]['shop']);
//
//            // 桌位分类
//            $data_list[$k]['person_name'] = $v['table_person']['person_name'] ?? '';
//            // $data_list[$k]['shop_id'] = $v['shop']['id'] ?? 0;
//            if(isset($data_list[$k]['table_person'])) unset($data_list[$k]['table_person']);

            // 资源url
            $resource_list = [];
            if(isset($v['site_resources'])){
                Tool::resourceUrl($v, 2);
                $resource_list = Tool::formatResource($v['site_resources'], 2);
                unset($data_list[$k]['site_resources']);
            }
            $data_list[$k]['resource_list'] = $resource_list;
            // 生成的小程序二信码网络地址
            $has_qrcode = $v['has_qrcode'] ?? 1;
            $qrcode_url = $v['qrcode_url'] ?? '';
            if($has_qrcode == 2 && !empty($qrcode_url)){
                $data_list[$k]['qrcode_url_old'] = $qrcode_url;
                $data_list[$k]['qrcode_url'] = url($qrcode_url);
            }
        }
        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($data_list, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        $result['data_list'] = $data_list;
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
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoData(Request $request, Controller $controller, $id, $selectParams = [], $relations = '', $extParams = [], $notLog = 0){
        $company_id = $controller->company_id;
        // $relations = '';
        // $resultDatas = APIRunBuyRequest::getinfoApi(self::$model_name, '', $relations, $company_id , $id);
        $info = static::getInfoDataBase($request, $controller,'', $id, $selectParams, $relations, $notLog);
        RelationDB::resolvingRelationData($info, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $info, $judgeData );
        // 店铺
        $shop_name = $info['shop_history']['shop_name'] ?? '';
        if(empty($shop_name)) $shop_name = $info['shop']['shop_name'] ?? '';
        $info['shop_name'] = $shop_name;
        $now_shop_state = 0;// 最新的商家 0没有变化 ;1 已经删除  2 试卷不同
        if(isset($info['shop_history']) && isset($info['shop'])){
            $history_version_num = $info['shop_history']['version_num'] ?? '';
            $version_num = $info['shop']['version_num'] ?? '';
            if(empty($info['shop'])){
                $now_shop_state = 1;
            }elseif($version_num != '' && $history_version_num != $version_num){
                $now_shop_state = 2;
            }
        }
        if(isset($info['shop_history'])) unset($info['shop_history']);
        if(isset($info['shop'])) unset($info['shop']);
        $info['now_shop_state'] = $now_shop_state;
        // 资源url
        $resource_list = [];
        if(isset($info['site_resources'])){
            Tool::resourceUrl($info, 2);
            $resource_list = Tool::formatResource($info['site_resources'], 2);
            unset($info['site_resources']);
        }
        $info['resource_list'] = $resource_list;
        // 生成的小程序二信码网络地址
        $has_qrcode = $info['has_qrcode'] ?? 1;
        $qrcode_url = $info['qrcode_url'] ?? '';
        if($has_qrcode == 2 && !empty($qrcode_url)){
            $info['qrcode_url_old'] = $qrcode_url;
            $info['qrcode_url'] = url($qrcode_url);
        }
        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($info, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        return $info;
    }


    /**
     * 根据条件获得一条详情记录 - 一维
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $company_id 企业id
     * @param array $queryParams 条件数组/json字符
     *   $queryParams = [
     *       'where' => [
     *           ['order_type', '=', 1],
     *           // ['staff_id', '=', $user_id],
     *           ['order_no', '=', $order_no],
     *           // ['id', '&' , '16=16'],
     *           // ['company_id', $company_id],
     *           // ['admin_type',self::$admin_type],
     *       ],
     *       // 'whereIn' => [
     *           //   'id' => $subjectHistoryIds,
     *       //],
     *       'select' => ['id', 'status'],
     *       // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
     *   ];
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoDataByQuery(Request $request, Controller $controller, $company_id, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        // $company_id = $controller->company_id;
        // $relations = '';
        // $resultDatas = APIRunBuyRequest::getinfoApi(self::$model_name, '', $relations, $company_id , $id);
        $info = static::getInfoByQuery($request, $controller,'', $company_id, $queryParams, $relations, $notLog);
        RelationDB::resolvingRelationData($info, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $info, $judgeData );
        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($info, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        return $info;
    }


    /**
     * 根据条件获得一条详情记录 - pagesize 1:返回一维数组,>1 返回二维数组  -- 推荐有这个按条件查询详情
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $company_id 企业id
     * @param int $pagesize 想获得的记录数量 1 , 2 。。 默认1
     * @param array $queryParams 条件数组/json字符
     *   $queryParams = [
     *       'where' => [
     *           ['order_type', '=', 1],
     *           // ['staff_id', '=', $user_id],
     *           ['order_no', '=', $order_no],
     *           // ['id', '&' , '16=16'],
     *           // ['company_id', $company_id],
     *           // ['admin_type',self::$admin_type],
     *       ],
     *       // 'whereIn' => [
     *           //   'id' => $subjectHistoryIds,
     *       //],
     *       'select' => ['id', 'status'],
     *       // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
     *   ];
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getLimitDataQuery(Request $request, Controller $controller, $company_id, $pagesize = 1, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        // $company_id = $controller->company_id;
        // $relations = '';
        $infoList = static::getInfoQuery($request, $controller,'', $company_id, $pagesize, $queryParams, $relations, $notLog);
        RelationDB::resolvingRelationData($infoList, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $infoList, $judgeData );
        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($infoList, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        return $infoList;
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
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据 - 二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getNearList(Request $request, Controller $controller, $id = 0, $nearType = 1, $limit = 1, $offset = 0, $queryParams = [], $relations = '', $extParams = [], $notLog = 0)
    {
        $company_id = $controller->company_id;
        // 前**条[默认]
        $defaultQueryParams = [
            'where' => [
                // ['company_id', $company_id],
//                ['id', '>', $id],
            ],
//            'select' => [
//                'id','company_id','person_name','sort_num'
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
        $temFormatData = [
            'formatDataUbound' => $extParams['formatDataUbound'] ?? [],// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法
        ];
        $result = static::getList($request, $controller, 1 + 0, $queryParams, $relations, $temFormatData, $notLog);
        // 格式化数据
        $data_list = $result['result']['data_list'] ?? [];
//        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据 -- 已经在getList方法中处理过
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
        $user_id = $controller->user_id;
        $id = CommonRequest::getInt($request, 'id');

        // 调用删除接口
        $apiParams = [
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        static::exeDBBusinessMethodCT($request, $controller, '',  'delById', $apiParams, $company_id, $notLog);
        return ajaxDataArr(1, $id, '');

//        $company_id = $controller->company_id;
//        // $id = CommonRequest::getInt($request, 'id');
//        return static::delAjaxBase($request, $controller, '', $notLog);

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
        $user_id = $controller->user_id;
        if(isset($saveData['person_name']) && empty($saveData['person_name'])  ){
            throws('人数名称不能为空！');
        }

        // 调用新加或修改接口
        $apiParams = [
            'saveData' => $saveData,
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $id = static::exeDBBusinessMethodCT($request, $controller, '',  'replaceById', $apiParams, $company_id, $notLog);
        return $id;
//        if($id > 0){
//            // 判断权限
////            $judgeData = [
////                'company_id' => $company_id,
////            ];
////            $relations = '';
////            static::judgePower($request, $controller, $id, $judgeData, '', $company_id, $relations, $notLog);
//            if($modifAddOprate) static::addOprate($request, $controller, $saveData);
//
//        }else {// 新加;要加入的特别字段
//            $addNewData = [
//             //   'company_id' => $company_id,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//            // 加入操作人员信息
//            static::addOprate($request, $controller, $saveData);
//        }
//        // 新加或修改
//        return static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);
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
        $kvParams = ['key' => 'id', 'val' => 'city_name'];
        $queryParams = [
            'where' => [
                // ['id', '&' , '16=16'],
                ['parent_id', '=', $parent_id],
                //['mobile', $keyword],
                //['admin_type',self::$admin_type],
            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
//            'select' => [
//                'id','company_id','person_name','sort_num'
//            ],
            'orderBy' => ['sort_num'=>'desc', 'id'=>'asc'],
        ];
        return static::getKVCT( $request,  $controller, '', $kvParams, [], $queryParams, $company_id, $notLog);
    }

    // 根据父id,获得子数据kv数组
    public static function getListKV(Request $request, Controller $controller, $notLog = 0){
        $company_id = $controller->company_id;

        $kvParams = ['key' => 'id', 'val' => 'person_name'];
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
//                'id','company_id','person_name','sort_num'
//            ],
            'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],
        ];
        $seller_id = CommonRequest::getInt($request, 'seller_id');
        if($seller_id > 0 )  array_push($queryParams['where'], ['seller_id', '=', $seller_id]);

        $shop_id = CommonRequest::getInt($request, 'shop_id');
        if($shop_id > 0 )  array_push($queryParams['where'], ['shop_id', '=', $shop_id]);

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
                'id','city_name','sort_num'
                //,'operate_staff_id','operate_staff_history_id'
            ],
            'orderBy' => ['sort_num'=>'desc','id'=>'asc'],
        ];// 查询条件参数
        // $relations = ['CompanyInfo'];// 关系
        $relations = '';//['CompanyInfo'];// 关系
        $result = static::getBaseListData($request, $controller, '', $queryParams, $relations , $oprateBit, $notLog);
        // 格式化数据
        $data_list = $result['data_list'] ?? [];
        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
        $result['data_list'] = $data_list;
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
            'status' => $status,// 状态,多个用逗号分隔, 可为空：所有的
            'company_id' => $company_id,
            'otherWhere' => $otherWhere,// 其它条件[['company_id', '=', $company_id],...]
            'operate_staff_id' => $user_id,
        ];
        $statusCountList = static::exeDBBusinessMethodCT($request, $controller, '', 'getGroupCount', $apiParams, $company_id, $notLog);
        return $statusCountList;
    }

    /**
     * 生成二维码
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id 记录id
     * @param  string  $block 配置标签  'default'
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  string 生成的二维码网络地址 "http://runbuy.admin.cunwo.net/resource/company/1/images/qrcode/tables/1.png"
     * @author zouyan(305463219@qq.com)
     */
    public static function createQrcode(Request $request, Controller $controller, $id, $block = 'default', $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;

        // 生成二维码
        $filePathArr = QRCode::getCodeUnlimited($company_id, 1, $block, static::$table_name, $id . '.png', $id, []);
        $publicPath = $filePathArr['publicPath'] ?? '';// public目录(系统的) 绝对路径 /srv/www/work/work.0101jz.com/public
        $savePath = $filePathArr['savePath'] ?? '';// 文件目录 '/resource/company/1/images/2019/10/04/'
        $saveName = $filePathArr['saveName'] ?? '';// 文件名  20191003121326d710d554edce12a1.png
        $files_names = $filePathArr['files_names'] ?? '';//  文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
        $full_names = $filePathArr['full_names'] ?? '';// 服务器中的全路径（目录+文件名）  站点public目录 + 文件目录+文件名 '/data/public/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $company_id,
            'id' => $id,// 记录id
             'files_names' => $files_names,// 文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $statusCountList = static::exeDBBusinessMethodCT($request, $controller, '', 'createQrCode', $apiParams, $company_id, $notLog);
        return url($files_names);
    }

}