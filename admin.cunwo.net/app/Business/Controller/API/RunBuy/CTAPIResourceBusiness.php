<?php
// 资源
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Log;

class CTAPIResourceBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\ResourceAPI';

    // 大后台 admin/年/月/日/文件
    // 企业 company/[生产单元/]年/月/日/文件
    protected static $source_path = '/resource/company/';
    // 1:图片;2:excel
    public static $resource_type = [
        '1' => [
            'name' => '图片文件',
            'ext' => ['jpg','jpeg','gif','png','bmp','ico'],// 扩展名
            'dir' => 'images',// 文件夹名称
            'maxSize' => 1,// 文件最大值  单位 M
            'other' => [],// 其它各自类型需要判断的指标
        ],
        '2' => [
            'name' => 'excel文件',
            'ext' => ['xlsx', 'xls'],// 扩展名
            'dir' => 'excel',// 文件夹名称
            'maxSize' => 10,// 文件最大值 单位 M
            'other' => [],// 其它各自类型需要判断的指标
        ]
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
            'orderBy' => ['id'=>'desc'],// 'sort_num'=>'desc',
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
            if(isset($sqlParams[$tKey]) && !empty($sqlParams[$tKey]))  $queryParams[$tKey] = $sqlParams[$tKey];
        }

        if($useSearchParams) {
            // $params = self::formatListParams($request, $controller, $queryParams);
            $ower_type = CommonRequest::getInt($request, 'ower_type');
            if($ower_type > 0 )  array_push($queryParams['where'], ['ower_type', '=', $ower_type]);

            $ower_id = CommonRequest::getInt($request, 'ower_id');
            if($ower_id > 0 )  array_push($queryParams['where'], ['ower_id', '=', $ower_id]);

            $type_self_id = CommonRequest::getInt($request, 'type_self_id');
            if($type_self_id > 0 )  array_push($queryParams['where'], ['type_self_id', '=', $type_self_id]);

            $type_self_id_history = CommonRequest::getInt($request, 'type_self_id_history');
            if($type_self_id_history > 0 )  array_push($queryParams['where'], ['type_self_id_history', '=', $type_self_id_history]);

            $resource_type = CommonRequest::getInt($request, 'resource_type');
            if($resource_type > 0 )  array_push($queryParams['where'], ['resource_type', '=', $resource_type]);

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
//        $data_list = $result['data_list'] ?? [];
//        foreach($data_list as $k => $v){
//
//        }
//        $result['data_list'] = $data_list;
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
         // $controller->company_id = 1;
         // $controller->user_id = 1;
         // $controller->operate_staff_id = 1;

        $company_id = $controller->company_id;

        $id = CommonRequest::getInt($request, 'id');
        Tool::judgeInitParams('id', $id);

        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);

        $resultDatas = $modelObj::ResourceDelById($id, $company_id, $notLog);
        return ajaxDataArr(1, $resultDatas, '');
        // $id = CommonRequest::getInt($request, 'id');
        // return static::delAjaxBase($request, $controller, '', $notLog);

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
//        if(isset($saveData['shop_name']) && empty($saveData['shop_name'])  ){
//            throws('店铺名称不能为空！');
//        }

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
//              //  'company_id' => $company_id,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//            // 加入操作人员信息
//            static::addOprate($request, $controller, $saveData);
//        }
//        // 新加或修改
//        $result =  static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);
//
//        if($isModify){
//            // 判断版本号是否要+1
//            $historySearch = [
//                //  'company_id' => $company_id,
//                'shop_id' => $id,
//            ];
//            static::compareHistoryOrUpdateVersion($request, $controller, '' , $id, ShopHistoryAPIBusiness::$model_name
//                , 'shop_history', $historySearch, ['shop_id'], 1, $company_id);
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
     * 上传文件
     * post参数 photo 文件；name  文件名称;note 资源说明[可为空];;;;
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $resource_type 资源类型 1:图片;2:excel
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function uploadFile(Request $request, Controller $controller, $resource_type = 1)
    {
        // $controller->company_id = 1;
        // $controller->user_id = 1;
        // $controller->operate_staff_id = 1502;

        $company_id = $controller->company_id;

        ini_set('memory_limit','1024M');    // 临时设置最大内存占用为 3072M 3G
        ini_set("max_execution_time", "300");
        set_time_limit(300);   // 设置脚本最大执行时间 为0 永不过期

        // $pro_unit_id = Common::getInt($request, 'pro_unit_id');
        $name = CommonRequest::get($request, 'name'); // 文件名称
        $resource_note = CommonRequest::get($request, 'note'); // 资源说明
        // 日志
        $requestLog = [
            'files'       => $request->file(),
            'posts'  => $request->post(),
            'input'      => $request->input(),
        ];
        Log::info('上传文件日志',$requestLog);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photo = $request->file('photo');
            Log::info('上传文件日志-文件信息',[$photo]);
            // $extension = strtolower($photo->extension());// 扩展名  该扩展名可能会和客户端提供的扩展名不一致
            $extension = $photo->getClientOriginalExtension(); //上传文件的后缀.
            $hashname = $photo->hashName();
            //获取上传文件的大小
            $size = $photo->getSize();
            Log::info('上传文件日志-文件大小',[$size]);


            $temFile = [
                'extension' => $extension,
                'hashname' => $hashname,
                'name' => $name,
            ];
            Log::info('上传文件日志-文件信息',$temFile);

            // 生成保存路径
            $savPath = self::$source_path . $company_id . '/';

            $resourceTypeArr = self::$resource_type[$resource_type] ?? [];
            if(empty($resourceTypeArr)) throws('不明确的资源类型!');

            $typeName = $resourceTypeArr['name'] ?? '';// 类型名称
            $typeExt = $resourceTypeArr['ext'] ?? [];// 扩展名
            $typeDir = $resourceTypeArr['dir'] ?? '';// 文件夹名称
            $typeMaxSize = $resourceTypeArr['maxSize'] ?? '0.5';// 文件最大值 单位 M
            if(!is_numeric($typeMaxSize)) $typeMaxSize = 0.5;// 0.5M
            $typeOther = $resourceTypeArr['other'] ?? [];// 其它各自类型需要判断的指标

            if(!in_array($extension , $typeExt)) throws($typeName . '扩展名必须为[' . implode('、', $typeExt) . ']');

            //这里可根据配置文件的设置，做得更灵活一点
            if($size > $typeMaxSize * 1024 * 1024){
                throws('上传文件不能超过' . [$typeMaxSize . 'M']);
            }

            if($typeDir != '' ) $savPath .=   $typeDir . '/';// 类型文件夹

            //if(is_numeric($pro_unit_id)){
            //    $savPath .=   'pro' . $pro_unit_id . '/';
            //}

            $savPath .= date('Y/m/d/',time());

            $saveName = Tool::createUniqueNumber(30) .'.' . $extension;
            //$store_result = $photo->store('photo');
            try{
                $store_result = $photo->storeAs($savPath, $saveName);// 返回 "resource/company/1/pro0/2018/10/13//20181013182843dc1a9783e212840f.jpeg"
                // 保存资源
                $saveData = [
                    'resource_name' => $name,
                    'resource_type' => $resource_type,
                    'resource_note' => $resource_note,
                    'resource_url' => $savPath . $saveName,
                ];
                // $reslut = CommonBusiness::createApi(self::$model_name, $saveData, $company_id);
                $id = 0;
                $reslut = self::replaceById($request, $controller, $saveData, $id);
                // $id = $reslut['id'] ?? '';
                Log::info('上传文件日志-reslut',[$reslut]);
                if(empty($id)){
                    Log::info('上传文件日志-保存资源失败',[$id]);
                    throws('保存资源失败!');
                }
            } catch ( \Exception $e) {
                throws($e->getMessage());
            }
            return [
                'id' => $id,// 资源id
                'name' => $name,// 文件名
                'savPath' => $savPath,// 保存路径 /结束
                'saveName' => $saveName,// 保存文件名称
                'store_result' => $store_result,// storeAs
                // 'info' => $reslut,// 资源表记录 一维
            ];

        }else{
            throws('请选择要上传的文件！');
        }
    }

    /**
     * 上传文件 --plupload
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $resource_type 资源类型 1:图片;2:excel
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function filePlupload(Request $request, Controller $controller, $resource_type = 1)
    {
        try{
            $result = self::uploadFile($request, $controller, $resource_type);
            $sucArr = [
                'result' => 'ok',// 文件上传成功
                'id' => $result['id'] , // 文件在服务器上的唯一标识
                'url'=> url($result['savPath'] . $result['saveName']),//'http://example.com/file-10001.jpg',// 文件的下载地址
                'store_result' => $result['store_result'],
                'data_list' => [
                    [
                        'id' => $result['id'],
                        'resource_name' => $result['name'],
                        'resource_url' => url($result['savPath'] . $result['saveName']),
                        'created_at' =>  date('Y-m-d H:i:s',time()),
                    ]
                ],
            ];
            Log::info('上传文件日志-成功',$sucArr);
            return $sucArr;
        } catch ( \Exception $e) {
            $errArr = [
                'result' => 'failed',// 文件上传失败
                'message' => $e->getMessage(),//'文件内容包含违规内容',//用于在界面上提示用户的消息
            ];
            Log::info('上传文件日志-失败',$errArr);
            return $errArr;
        }
    }

    /**
     * 上传文件 --plupload
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $resource_type 资源类型 1:图片;2:excel
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function fileSingleUpload(Request $request, Controller $controller, $resource_type = 1)
    {
        try{
            $result = self::uploadFile($request, $controller, $resource_type);
            $sucArr = [
                'id' => $result['id'] , // 文件在服务器上的唯一标识
                'url'=> url($result['savPath'] . $result['saveName']),//'http://example.com/file-10001.jpg',// 文件的下载地址
                'filePath' => $result['savPath'] . $result['saveName'],
                'store_result' => $result['store_result'],
                'resource_name' => $result['name'],
                'created_at' =>  date('Y-m-d H:i:s',time()),
            ];
            Log::info('上传文件日志-成功',$sucArr);
            return ajaxDataArr(1, $sucArr, '');

        } catch ( \Exception $e) {
            Log::info('上传文件日志-失败',[$e->getMessage()]);
            return ajaxDataArr(0, null,$e->getMessage());
        }
    }

    /**
     * 获得列表数据--根据图片ids
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $company_id 企业id
     * @param string $ids  查询的id ,多个用逗号分隔,
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getResourceByIds(Request $request, Controller $controller, $company_id = 0, $ids = '', $notLog = 0){
        if(empty($ids)) return [];
        $queryParams = [
            'where' => [
                //    ['company_id', $company_id],
                // ['operate_staff_id', $user_id],
            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//                //,'operate_staff_id','operate_staff_history_id'
//                ,'created_at'
//            ],
//            'orderBy' => ['sort_num'=>'desc','id'=>'desc'],
            'orderBy' => ['id'=>'desc'],
        ];// 查询条件参数
        if(is_numeric($company_id) && $company_id > 0) array_push($queryParams['where'],['company_id', $company_id]);

        if (!empty($ids)) {
            if (strpos($ids, ',') === false) { // 单条
                array_push($queryParams['where'], ['id', $ids]);
            } else {
                $idArr = explode(',', $ids);
                $queryParams['whereIn']['id'] = Tool::arrClsEmpty($idArr);
            }
        }
        $result = self::getList($request, $controller, 1 + 0, $queryParams, [], ['useQueryParams' => false], $notLog);
        $data_list = $result['result']['data_list'] ?? [];
//        $reList = [];
//        foreach($data_list as $k => $v){
//            $temArr = [
//                'id' => $v['id'],
//                'resource_name' => $v['resource_name'],
//                'resource_url' => url($v['resource_url']),
//                'created_at' => $v['created_at'],
//            ];
//            array_push($reList, $temArr);
//        }
        $reList = Tool::formatResource($data_list, 2);
        return $reList;
    }

}