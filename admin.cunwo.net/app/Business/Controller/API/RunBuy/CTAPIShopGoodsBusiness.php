<?php
// 店铺商品
namespace App\Business\Controller\API\RunBuy;

use App\Business\API\RunBuy\ShopGoodsHistoryAPIBusiness;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIShopGoodsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\ShopGoodsAPI';

    // 热销1非热销2热销
    public static $isHotArr = [
        '1' => '非热销',
        '2' => '热销',
    ];

    // 是否上架1上架2下架
    public static $isSaleArr = [
        '1' => '上架',
        '2' => '下架',
    ];

    public static $orderProp = [
        ['key' => 'prop_id', 'sort' => 'asc', 'type' => 'numeric'],
        ['key' => 'prop_val_id', 'sort' => 'asc', 'type' => 'numeric'],
    ];

    public static $pricePropVals = [
        ['key' => 'sort_num', 'sort' => 'desc', 'type' => 'numeric'],
        ['key' => 'id', 'sort' => 'asc', 'type' => 'numeric'],
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

            $city_site_id = CommonRequest::getInt($request, 'city_site_id');
            if($city_site_id > 0 )  array_push($queryParams['where'], ['city_site_id', '=', $city_site_id]);

            $city_partner_id = CommonRequest::getInt($request, 'city_partner_id');
            if($city_partner_id > 0 )  array_push($queryParams['where'], ['city_partner_id', '=', $city_partner_id]);

            $seller_id = CommonRequest::getInt($request, 'seller_id');
            if($seller_id > 0 )  array_push($queryParams['where'], ['seller_id', '=', $seller_id]);

            $shop_id = CommonRequest::getInt($request, 'shop_id');
            if($shop_id > 0 )  array_push($queryParams['where'], ['shop_id', '=', $shop_id]);

            $type_id = CommonRequest::getInt($request, 'type_id');
            if($type_id > 0 )  array_push($queryParams['where'], ['type_id', '=', $type_id]);

            $is_hot = CommonRequest::getInt($request, 'is_hot');
            if($is_hot > 0 )  array_push($queryParams['where'], ['is_hot', '=', $is_hot]);

            $is_sale = CommonRequest::getInt($request, 'is_sale');
            if($is_sale > 0 )  array_push($queryParams['where'], ['is_sale', '=', $is_sale]);


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
        foreach($data_list as $k => $v){
            // 城市分站名称
            $data_list[$k]['site_name'] = $v['city']['city_name'] ?? '';
            // $data_list[$k]['site_id'] = $v['city']['id'] ?? 0;
            if(isset($data_list[$k]['city'])) unset($data_list[$k]['city']);
            // 城市城市合伙人
            $data_list[$k]['partner_name'] = $v['city_partner']['partner_name'] ?? '';
            // $data_list[$k]['partner_id'] = $v['city_partner']['id'] ?? 0;
            if(isset($data_list[$k]['city_partner'])) unset($data_list[$k]['city_partner']);
            // 商家
            $data_list[$k]['seller_name'] = $v['seller']['seller_name'] ?? '';
            // $data_list[$k]['seller_id'] = $v['seller']['id'] ?? 0;
            if(isset($data_list[$k]['seller'])) unset($data_list[$k]['seller']);
            // 铺店
            $data_list[$k]['shop_name'] = $v['shop']['shop_name'] ?? '';
            // $data_list[$k]['shop_id'] = $v['shop']['id'] ?? 0;
            if(isset($data_list[$k]['shop'])) unset($data_list[$k]['shop']);
            // 分类
            $data_list[$k]['type_name'] = $v['type']['type_name'] ?? '';
            // $data_list[$k]['type_id'] = $v['type']['id'] ?? 0;
            if(isset($data_list[$k]['type'])) unset($data_list[$k]['type']);

            // 资源url
            $resource_list = [];
            if(isset($v['site_resources'])){
                Tool::resourceUrl($v, 2);
                $resource_list = Tool::formatResource($v['site_resources'], 2);
                unset($data_list[$k]['site_resources']);
            }
            $data_list[$k]['resource_list'] = $resource_list;
            // 价格处理
            $priceList = [];
            $price_type = $v['price_type'] ?? 1;// 是否有价格属性1没有2有
            if($price_type == 1){
                array_push($priceList, ['price_id' => 0, 'prop_id' => 0, 'prop_name' => '', 'prop_val_id' => 0, 'price_name' => '', 'price_val' => $v['price'] ?? 0]);
            }
            // 价格属性
            $price_props = $v['price_props'] ?? [];
            if($price_type == 2) {
                // 排序
                if(!empty($price_props)) $price_props = Tool::php_multisort($price_props, self::$pricePropVals);
                foreach ($price_props as $t_v) {
                    $prop_name = '';
                    if(isset($t_v['prop_name']['main_name'])){// 含历史--用这个
                        $prop_name = $t_v['prop_name']['main_name'] ?? '';
                    }elseif(isset($t_v['prop']['name']['main_name'])){// 实时与属性表同步
                         $prop_name = $t_v['prop']['name']['main_name'];
                     }
                     $price_pv_name = "";
                    if(isset($t_v['prop_val_name']['main_name'])){// 历史--用这个
                        $price_pv_name = $t_v['prop_val_name']['main_name'] ?? '';
                    }elseif(isset($t_v['prop_val']['name']['main_name'])){// 实时与属性值表同步
                        $price_pv_name = $t_v['prop_val']['name']['main_name'];
                    }

                    array_push($priceList, [
                        'price_id' => $t_v['id']
                        , 'prop_id' => $t_v['prop_id']
                        , 'prop_name' => $prop_name
                        , 'prop_val_id' => $t_v['prop_val_id']
                        , 'price_name' => $price_pv_name
                        , 'price_val' => $t_v['price'] ?? 0
                    ]);
                }
                 if(isset($v['price_props'])) unset($data_list[$k]['price_props']);
            }
            $data_list[$k]['price_list'] = $priceList;
        }
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
        if(isset($saveData['goods_name']) && empty($saveData['goods_name'])  ){
            throws('商品名称不能为空！');
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
//        $isModify = false;
//        if($id > 0){
//            $isModify = true;
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
//               // 'company_id' => $company_id,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//            // 加入操作人员信息
//            static::addOprate($request, $controller, $saveData);
//        }
//        // 新加或修改
//        $result =  static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);
//        if($isModify){
//            // 判断版本号是否要+1
//            $historySearch = [
//                //  'company_id' => $company_id,
//                'goods_id' => $id,
//            ];
//            static::compareHistoryOrUpdateVersion($request, $controller, '' , $id, ShopGoodsHistoryAPIBusiness::$model_name
//                , 'shop_goods_history', $historySearch, ['goods_id'], 1, $company_id);
//        }
//        return $result;
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
//                'id','company_id','type_name','sort_num'
//            ],
            'orderBy' => ['sort_num'=>'desc', 'id'=>'asc'],
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
            'orderBy' => ['sort_num'=>'desc', 'id'=>'desc'],
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
                'id','city_name','sort_num'
                //,'operate_staff_id','operate_staff_history_id'
            ],
            'orderBy' => ['sort_num'=>'desc','id'=>'asc'],
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
     * 获得商品属性数据--根据商品id
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 商品属性数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getPropByGoodId(Request $request, Controller $controller, $notLog = 0){
        $good_id = CommonRequest::getInt($request, 'good_id');
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        // 调用获得商品及属性数据--根据商品id接口
        $apiParams = [
            'company_id' => $company_id,
            'good_id' => $good_id,
            'operate_staff_id' => $user_id,
        ];
        $info = static::exeDBBusinessMethodCT($request, $controller, '',  'getPropIdsByKey', $apiParams, $company_id, $notLog);
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $info, $judgeData );
        return $info['propList'] ?? [];
    }
    /**
     * 格式化商品属性
     *
     * @param array $props 商品表关系获得的商品属性
     * @param array $formatCartPropArr 已有的商品属性 ['属性id' => ['属性值id',...]]
     * @param int $name_type 名称类型 1 实时[商品或购物车]--不用这个了 2 历史[订单][商品或购物车]
     *
    , 'goods.props.propName', 'goods.props.propValName'// 属性名--订单后
    , 'goods.props.prop.name', 'goods.props.propVal.name'// 属性名--订单前
    , 'goodsPrice**.propName', 'goodsPrice**.propValName'// 价格属性名--订单后
    , 'goodsPrice**.prop.name', 'goodsPrice**.propVal.name'// 价格属性名--订单前
     * @return  array 商品属性数据
     *
        [
        {
            "prop_table_id": 1,
            "sort_num": 5,
            "prop_id": 3,
            "prop_name": "糖量",
            "is_multi": 0,
            "is_must": 0,
            "prop_vals": [
                {
                    "prop_val_id": 9,
                    "prop_val_name": "多糖"
                },
                {
                    "prop_val_id": 10,
                    "prop_val_name": "中糖"
                },
                {
                    "prop_val_id": 12,
                    "prop_val_name": "无糖"
                }
            ]
        },
        ]
     * @author zouyan(305463219@qq.com)
     */
    public static function formatProps($props, $formatCartPropArr = [], $name_type = 1){
        // 排序
        $propDistance = [
            ['key' => 'sort_num', 'sort' => 'desc', 'type' => 'numeric'],
            ['key' => 'id', 'sort' => 'desc', 'type' => 'numeric'],
        ];
        $props = Tool::php_multisort($props, $propDistance);
        $props = array_values($props);
        $format_props = [];
        foreach($props as $p_k => $p_v){
            $table_id = $p_v['id'];
            $is_multi = $p_v['is_multi'];
            $is_must = $p_v['is_must'];
            $sort_num = $p_v['sort_num'];

            $prop_id = $p_v['prop_id'];
            if($name_type == 1){// 实时
                $prop_name = $p_v['prop']['name']['main_name'] ?? '';
            }else{// 历史
                $prop_name = $p_v['prop_name']['main_name'] ?? '';
            }
            $prop_val_id = $p_v['prop_val_id'];
            if($name_type == 1) {// 实时
                $prop_val_name = $p_v['prop_val']['name']['main_name'] ?? '';
            }else{
                $prop_val_name = $p_v['prop_val_name']['main_name'] ?? '';
            }
            if(!isset($format_props[$prop_id])) $format_props[$prop_id] = [
                'prop_id' => $prop_id,
                'prop_name' => $prop_name,
                'is_multi' => $is_multi,
                'is_must' => $is_must,
            ];
            $checked = 0;
            $seledPvArr = $formatCartPropArr[$prop_id] ?? [];
            if(in_array($prop_val_id, $seledPvArr)) $checked = 1;
            $format_props[$prop_id]['prop_vals'][] = [
                'prop_table_id' => $table_id,
                'sort_num' => $sort_num,
                'prop_val_id' => $prop_val_id,
                'prop_val_name' => $prop_val_name,
                'checked' => $checked,// 是否选中 0 未选中 1选中
            ];
        }
        return array_values($format_props);
    }
}