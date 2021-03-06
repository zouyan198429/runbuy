<?php
// 钱包操作记录
namespace App\Business\Controller\API\RunBuy;

use App\Business\API\RunBuy\WalletRecordAPIBusiness;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\pay\weixin\easyWechatPay;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Log;

class CTAPIWalletRecordBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\WalletRecordAPI';
    public static $table_name = 'wallet_record';// 表名称

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

            $operate_type = CommonRequest::get($request, 'operate_type');
            if (!empty($operate_type)) {
                if (strpos($operate_type, ',') === false) { // 单条
                    array_push($queryParams['where'], ['operate_type', $operate_type]);
                } else {
                    $queryParams['whereIn']['operate_type'] = explode(',', $operate_type);
                }
            }

            $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');
            if($pay_config_id > 0 )  array_push($queryParams['where'], ['pay_config_id', '=', $pay_config_id]);

            $pay_type = CommonRequest::get($request, 'pay_type');
            if (!empty($pay_type)) {
                if (strpos($pay_type, ',') === false) { // 单条
                    array_push($queryParams['where'], ['pay_type', $pay_type]);
                } else {
                    $queryParams['whereIn']['pay_type'] = explode(',', $pay_type);
                }
            }

            $pay_order_no = CommonRequest::get($request, 'pay_order_no');
            if(!empty($pay_order_no))  array_push($queryParams['where'], ['pay_order_no', '=', $pay_order_no]);

            $my_order_no_old = CommonRequest::get($request, 'my_order_no_old');
            if(!empty($my_order_no_old))  array_push($queryParams['where'], ['my_order_no_old', '=', $my_order_no_old]);

            $my_order_no = CommonRequest::get($request, 'my_order_no');
            if(!empty($my_order_no))  array_push($queryParams['where'], ['my_order_no', '=', $my_order_no]);

            $third_order_no_old = CommonRequest::get($request, 'third_order_no_old');
            if(!empty($third_order_no_old))  array_push($queryParams['where'], ['third_order_no_old', '=', $third_order_no_old]);

            $third_order_no = CommonRequest::get($request, 'third_order_no');
            if(!empty($third_order_no))  array_push($queryParams['where'], ['third_order_no', '=', $third_order_no]);

            $status = CommonRequest::get($request, 'status');
            if (!empty($status)) {
                if (strpos($status, ',') === false) { // 单条
                    array_push($queryParams['where'], ['status', $status]);
                } else {
                    $queryParams['whereIn']['status'] = explode(',', $status);
                }
            }

            $operate_staff_id = CommonRequest::getInt($request, 'operate_staff_id');
            if($operate_staff_id > 0 )  array_push($queryParams['where'], ['operate_staff_id', '=', $operate_staff_id]);


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
//        foreach($data_list as $k => $v){
//            // 所属人员
//            $data_list[$k]['nickname'] = $v['staff']['nickname'] ?? '';
//            $data_list[$k]['staff_id'] = $v['staff']['id'] ?? 0;
//            if(isset($data_list[$k]['staff'])) unset($data_list[$k]['staff']);
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
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
        $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
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
//        $company_id = $controller->company_id;
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
//                //  'company_id' => $company_id,
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
     * 支付订单跑腿费或追加订单跑腿费
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $params 参数
        $params = [
            'pay_type' => 1, // 支付类型 1 订单支付跑腿费 2 订单追加跑腿费
            'amount' => 10,// 追加跑腿费 单位元
        ];
     * @param string $order_no 订单号
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array
        $returnArr = [
            'body' => '支付说明',
            'total_fee' => '支付金额，单位元',
            'out_trade_no' => '支付单号-我方',
        ];
     * @author zouyan(305463219@qq.com)
     */
    public static function payOrder(Request $request, Controller $controller, $params, $order_no, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }

        // 调用新加或修改接口
        $apiParams = [
            'params' => $params,
            'company_id' => $company_id,
            'order_no' => $order_no,
            'operate_staff_id' => $user_id,
        ];
        $result = static::exeDBBusinessMethodCT($request, $controller, '', 'payOrder', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 支付回调--微信
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $message 参数
     * @param array $queryMessage
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  string 正常返回 已经处理好，不用再通知了 or  throws string :错误信息，还要再通知我
     * @author zouyan(305463219@qq.com)
     */
    public static function payWXNotify(Request $request, Controller $controller, $message = [], $queryMessage = [], $notLog = 0)
    {
        $company_id = 0;// $controller->company_id;
        // $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }

        // 调用新加或修改接口
        $apiParams = [
            'message' => $message,
            'queryMessage' => $queryMessage,
            // 'company_id' => $company_id,
        ];
        $result = static::exeDBBusinessMethodCT($request, $controller, '', 'payWXNotify', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 申请退款--微信
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $params 参数
        $params = [
            [
                'order_no' => '', // 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
                'my_order_no' => '',//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
                'refund_amount' => 0,// 需要退款的金额--0为全退---单位元
                'refund_reason' => '',// 退款的原因--:为空，则后台自己组织内容
            ]
        ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array
        $returnArr = [
            [
                'pay_order_no' => '',// 我方的付款单号
                'refund_order_no' => '',// 我方生成的退款单号
                'pay_amount' => 0,// 我方付款的总金额[当前付款单]--单位元
                'refund_amount' => 0,// 需要退款的金额---单位元
                'config'=> [// 其它退款参数
                    'refund_desc' => '',// 退款的原因
                ]
            ],
        ];
     * @author zouyan(305463219@qq.com)
     */
    public static function refundApplyWX(Request $request, Controller $controller, $params,  $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
        $result = WalletRecordAPIBusiness::refundApplyWX($company_id, $user_id, $params,  $notLog);
        // 调用新加或修改接口
//        $apiParams = [
//            'params' => $params,
//            'company_id' => $company_id,
//            'operate_staff_id' => $user_id,
//        ];
//        $result = static::exeDBBusinessMethodCT($request, $controller, '', 'refundApplyWX', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 申请退款--微信 --成功/失败手动修改数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $out_refund_no 我方退款单号
     * @param string $refund_status 业务结果  SUCCESS/FAIL SUCCESS/FAIL  SUCCESS退款申请接收成功，结果通过退款查询接口查询  FAIL 提交业务失败
     * @param string $return_msg 失败原因
     * @author zouyan(305463219@qq.com)
     */
    public static function refundApplyWXFail(Request $request, Controller $controller, $out_refund_no, $refund_status, $return_msg, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }

        $result = WalletRecordAPIBusiness::refundApplyWXFail($company_id, $user_id, $out_refund_no, $refund_status, $return_msg, $notLog);
        // 调用新加或修改接口
//        $apiParams = [
//            'out_refund_no' => $out_refund_no,
//            'refund_status' => $refund_status,
//            'return_msg' => $return_msg,
//            'company_id' => $company_id,
//            'operate_staff_id' => $user_id,
//        ];
//        $result = static::exeDBBusinessMethodCT($request, $controller, '', 'refundApplyWXFail', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 支付退款回调--微信
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $reqInfo
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  正常返回字符 已经处理好，不用再通知了 or throws 异常 string :错误信息，还要再通知我
     * @author zouyan(305463219@qq.com)
     */
    public static function refundWXNotify(Request $request, Controller $controller, $reqInfo = [], $notLog = 0)
    {
        $company_id = 0;// $controller->company_id;
        // $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }

        // 调用新加或修改接口
        $apiParams = [
            'reqInfo' => $reqInfo,
            // 'company_id' => $company_id,
        ];
        $result = static::exeDBBusinessMethodCT($request, $controller, '', 'refundWXNotify', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 根据订单号取消订单--
     *
     * @param Request $request 请求信息  order_no 订单号 、 my_order_no 付款 我方单号 二选一;  amount 需要退款的金额--0为全退---单位元;  refund_reason  退款的原因--:为空，则后台自己组织内容
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function cancelOrder(Request $request, Controller $controller, $notLog = 0){

        $company_id = $controller->company_id;
        $user_id = $controller->user_id;

        // $pay_type = CommonRequest::getInt($request, 'pay_type');// 支付类型 1 订单支付跑腿费 2 订单追加跑腿费
        // if(!in_array($pay_type, [1,2])) throws('支付类型有误!');

        $order_no = CommonRequest::get($request, 'order_no');// 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
        $my_order_no = CommonRequest::get($request, 'my_order_no');//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空


        if(empty($order_no) && empty($my_order_no)) throws('订单号或付款单号不能为空!');

        // 如果是订单，则判断订单状态
        if(!empty($order_no)){
            // $company_id = $controller->company_id;

            $queryParams = [
                'where' => [
                    ['order_type', '=', 1],
                    // ['staff_id', '=', $user_id],
                    ['order_no', '=', $order_no],
                    // ['id', '&' , '16=16'],
                    // ['company_id', $company_id],
                    // ['admin_type',self::$admin_type],
                ],
                // 'whereIn' => [
                //   'id' => $subjectHistoryIds,
                //],
            'select' => ['id', 'status'],
                // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
            ];
            $orderInfo = CTAPIOrdersDoingBusiness::getInfoByQuery($request, $controller, '', $company_id, $queryParams);
            if(empty($orderInfo)) throws('订单记录不存在!');
            if($orderInfo['status'] != 2) throws('订单记录非待接单状态，不可取消!');
        }

        $refund_reason = CommonRequest::get($request, 'refund_reason');// 退款的原因--:为空，则后台自己组织内容

        $amount = CommonRequest::get($request, 'amount');// 需要退款的金额--0为全退---单位元

        if(!is_numeric($amount)) $amount = 0;
        if(!is_numeric($amount) && $amount < 0) throws('费用不能小于0!');

        $params = [
            [
                'order_no' => $order_no, // 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
                'my_order_no' => $my_order_no,//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
                'refund_amount' => $amount,// 需要退款的金额--0为全退---单位元
                'refund_reason' => $refund_reason,// 退款的原因--:为空，则后台自己组织内容
            ]
        ];

        $out_refund_nos = WalletRecordAPIBusiness::orderCancel($company_id, $user_id, $params, $notLog);

        return ajaxDataArr(1, $out_refund_nos, '');
    }

    /**
     * 根据订单号取消订单--
     *
     * @param Request $request 请求信息  order_no 订单号 、 my_order_no 付款 我方单号 二选一;  amount 需要退款的金额--0为全退---单位元;  refund_reason  退款的原因--:为空，则后台自己组织内容
     * @param Controller $controller 控制对象
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
//    public static function cancelOrder_back(Request $request, Controller $controller){
//
//        // $pay_type = CommonRequest::getInt($request, 'pay_type');// 支付类型 1 订单支付跑腿费 2 订单追加跑腿费
//        // if(!in_array($pay_type, [1,2])) throws('支付类型有误!');
//
//        $order_no = CommonRequest::get($request, 'order_no');// 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
//        $my_order_no = CommonRequest::get($request, 'my_order_no');//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
//
//
//        if(empty($order_no) && empty($my_order_no)) throws('订单号或付款单号不能为空!');
//
//        // 如果是订单，则判断订单状态
//        if(!empty($order_no)){
//            $company_id = $controller->company_id;
//
//            $queryParams = [
//                'where' => [
//                    ['order_type', '=', 1],
//                    // ['staff_id', '=', $user_id],
//                    ['order_no', '=', $order_no],
//                    // ['id', '&' , '16=16'],
//                    // ['company_id', $company_id],
//                    // ['admin_type',self::$admin_type],
//                ],
//                // 'whereIn' => [
//                //   'id' => $subjectHistoryIds,
//                //],
//                'select' => ['id', 'status'],
//                // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
//            ];
//            $orderInfo = CTAPIOrdersDoingBusiness::getInfoByQuery($request, $controller, '', $company_id, $queryParams);
//            if(empty($orderInfo)) throws('订单记录不存在!');
//            if($orderInfo['status'] != 2) throws('订单记录非待接单状态，不可取消!');
//        }
//
//        $refund_reason = CommonRequest::get($request, 'refund_reason');// 退款的原因--:为空，则后台自己组织内容
//
//        $amount = CommonRequest::get($request, 'amount');// 需要退款的金额--0为全退---单位元
//
//        if(!is_numeric($amount)) $amount = 0;
//        if(!is_numeric($amount) && $amount < 0) throws('费用不能小于0!');
//
//        $params = [
//            [
//                'order_no' => $order_no, // 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
//                'my_order_no' => $my_order_no,//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
//                'refund_amount' => $amount,// 需要退款的金额--0为全退---单位元
//                'refund_reason' => $refund_reason,// 退款的原因--:为空，则后台自己组织内容
//            ]
//        ];
//
//        Log::info('微信支付日志 退款申请-->' . __FUNCTION__,$params);
//        $out_refund_nos = [];
//        try{
//
//            $returnArr = CTAPIWalletRecordBusiness::refundApplyWX($request, $controller, $params);
//
//            Log::info('微信支付日志 退款申请返回参数-->' . __FUNCTION__,$returnArr);
//            $config = [
//                'refund_desc' => '',// $refund_desc,//'测试退款',// 退款原因 若商户传入，会在下发给用户的退款消息中体现退款原因  ；注意：若订单退款金额≤1元，且属于部分退款，则不会在退款消息中体现退款原因
//                'notify_url' => config('public.wxNotifyURL') . 'api/pay/refundNotify' ,// 退款结果通知的回调地址
//            ];
//            Log::info('微信支付日志 退款申请参数config-->' . __FUNCTION__,$config);
//            // 根据商户订单号退款
//            $app = app('wechat.payment');
//            foreach($returnArr as $v){
//                $out_refund_no = $v['refund_order_no'];//  我方生成的退款单号
//                $pay_order_no = $v['pay_order_no'];//我方的付款单号
//
//                $out_trade_no =  $pay_order_no;//我方的付款单号
//                $refundNumber = $out_refund_no;//我方生成的退款单号
//                $totalFee = floor($v['pay_amount'] * 100);//我方付款的总金额[当前付款单]--单位元
//                $refundFee = floor($v['refund_amount'] * 100);// 需要退款的金额---单位元
//                $refund_desc = $v['config']['refund_desc'];// 其它退款参数  退款的原因
//                $config['refund_desc'] = $refund_desc ;
//                $result = easyWechatPay::refundByOutTradeNumber($app, $out_trade_no, $refundNumber, $totalFee, $refundFee, $config);
//                // $result['result_code'] = 'FAIL';
//                Log::info('微信支付日志 退款申请返回结果-->' . __FUNCTION__,[$result]);
//                // 业务结果  SUCCESS/FAIL SUCCESS/FAIL  SUCCESS退款申请接收成功，结果通过退款查询接口查询  FAIL 提交业务失败
//                $result_code = $result['result_code'];
//                if($result_code != 'SUCCESS'){// FAIL 提交业务失败,回退
//                    $return_msg = $result['return_msg'] ?? '';// 失败原因
//                    $err_code = $result['err_code'] ?? '';// 错误代码
//                    $err_code_des = $result['err_code_des'] ?? '';// 错误代码描述
//                    $errMsg = '错误代码[' . $err_code . '];错误代码描述[' . $err_code_des . ']';
//                    $resultFail = CTAPIWalletRecordBusiness::refundApplyWXFail($request, $controller, $out_refund_no, $result_code, $errMsg);
//                    Log::info('微信支付日志 退款申请业务失败回退$resultFail-->' . __FUNCTION__,[$resultFail]);
//                    throws('退款申请失败:' . $errMsg);
//                }else{// 成功，查询是否成功
//                    // 重试 3次 6秒
////                    $queryNum = 0;
////                    while(true)   #循环获取锁
////                    {
////                        $queryNum++;
////                        $delay = mt_rand(2 * 1000 * 1000, 3 * 1000 * 1000);
////                        usleep($delay);//usleep($delay * 1000);
//
////                        $resultQuery = easyWechatPay::queryByOutRefundNumber($app, $out_refund_no);
////                        Log::info('微信支付日志 退款结果查询情况$resultQuery-->' . __FUNCTION__,[$resultQuery]);
////                        // 如果成功，则修改退款单为成功
////                        $quest_result_code = $resultQuery['result_code'] ?? '';
////                        $quest_refund_status = $resultQuery['refund_status_0'] ?? '';
////                        Log::info('微信支付日志 退款结果查询情况 $quest_result_code-->' . __FUNCTION__,[$quest_result_code]);
////                        Log::info('微信支付日志 退款结果查询情况 $quest_refund_status-->' . __FUNCTION__,[$quest_refund_status]);
////                        if($quest_result_code == 'SUCCESS' && $quest_refund_status == 'SUCCESS' ) {
////                            $quest_return_msg = $resultQuery['return_msg'] ?? '';// 失败原因
////                            $resultSuccess = CTAPIWalletRecordBusiness::refundApplyWXFail($request, $controller, $out_refund_no, $quest_refund_status, $quest_return_msg);
////                            Log::info('微信支付日志 退款申请业务成功自动更新记录-->' . __FUNCTION__,[$resultSuccess]);
////                        }
////                        if($quest_refund_status == 'SUCCESS' || $queryNum >= 3) break;
////                    }
//
//                }
//                array_push($out_refund_nos, $out_refund_no);
//                // 根据微信订单号退款
//                // $result = easyWechatPay::refundByTransactionId($app, $transactionId, $refundNumber, $totalFee, $refundFee, $config);
//            }
//        } catch ( \Exception $e) {
//            throws('失败；信息[' . $e->getMessage() . ']');
//        }
//        return ajaxDataArr(1, $out_refund_nos, '');
//    }
}