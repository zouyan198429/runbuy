<?php

namespace App\Services\Request;

use App\Services\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

/**
 * 通用工具服务类--HTTP请求数据库操作
 */
class CommonRequest
{
    //写一些通用方法
    /*
    public static function test(){
        echo 'aaa';die;
    }
    */

    /**
     * 获得所有的请求的数据  -- 可以筛选指定/排除的下标，且下标可改名
     *
     * @param Request $request 请求信息
     * @param int $requestType 获得的数据类型
     *          1、all()
     *          2、input() 获取所有 HTTP 请求参数值, 从 query + request属性对象中获取参数值
     *          3、query() 获取 GET 请求查询字符串参数值, 从 query 属性对象中获取参数值
     *          4、post() 获取 POST 请求参数值, 从 request 属性对象中获取参数值
     * @param boolean $needNotIn  keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *    先加入包含的，再排除去掉-加入些,排除些，修改一些下标名，并排除一些字段是很有用-- 两个下标都有值；
     * @param array $includeUboundArr 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     * @param array $exceptUboundArr 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     * @return  array 需要的请求数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getParamsByUbound(Request $request, $requestType = 1, $needNotIn = false, $includeUboundArr = [], $exceptUboundArr = []){
        $params = static::getParams($request, $requestType);

        if(empty($includeUboundArr) && empty($exceptUboundArr)) return $params;

        return Tool::formatArrUbound($params, $needNotIn, $includeUboundArr, $exceptUboundArr);
    }

    /**
     * 获得所有的请求的数据
     *
     * @param Request $request 请求信息
     * @param int $requestType 获得的数据类型
     *          1、all()
     *          2、input() 获取所有 HTTP 请求参数值, 从 query + request属性对象中获取参数值
     *          3、query() 获取 GET 请求查询字符串参数值, 从 query 属性对象中获取参数值
     *          4、post() 获取 POST 请求参数值, 从 request 属性对象中获取参数值
     *          5、only  返回所有你想要获取的参数键值对，不过，如果你想要获取的参数不存在，则对应参数会被过滤掉。
     *          6、except  除了在数组中的
     * @param array $dataUbound 一维数组 $requestType 为 5、6时使用  ['username', 'password']
     * @return  array 需要的请求数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getParams(Request $request, $requestType = 1, $dataUbound = []){
        // $method = $request->method();
        $params = [];
        switch ($requestType) {
            case 1:
                $params = $request->all();
                break;
            case 2://  获取所有 HTTP 请求参数值, 从 query + request属性对象中获取参数值
                $params = $request->input();
                break;
            case 3:// 获取 GET 请求查询字符串参数值, 从 query 属性对象中获取参数值
                $params = $request->query();
                break;
            case 4:// 获取 POST 请求参数值, 从 request 属性对象中获取参数值
                $params = $request->post();
                break;
            case 5:// 返回所有你想要获取的参数键值对，不过，如果你想要获取的参数不存在，则对应参数会被过滤掉。
                $params = $request->only($dataUbound);
                break;
            case 6:// 除了在数组中的
                $params = $request->except($dataUbound);
                break;
            default:
                break;
        }
        return $params;
    }


    // 先从get获取，没有再从post获取
    public static function get(Request $request, $key)
    {
        $value  = $request->get($key) ?: $request->post($key);
        // $value = StringHelper::deepFilterDatas($value, ['trim', 'strip_tags']);
        if(is_null($value)){ $value = '';}
        return $value;
    }

    public static function getInt(Request $request, $key)
    {
        return (int) self::get($request, $key);
    }

    public static function getInts(Request $request, $key)
    {
        $value = self::get($request,$key);

        return is_array($value) ? array_filter(array_map('intval', $value)) : intval($value);
    }

    public static function getBool(Request $request, $key)
    {
        return (bool) self::get($request, $key);
    }

    public static function getPosts(Request $request)
    {
        $params = $request->post() ?? [];

        // 兼容 RAW-JSON
//        if (Yii::$app->request->headers->get('content-type') == 'application/json') {
//            $params = array_merge(
//                $params,
//                json_decode(Yii::$app->request->getRawBody(), true) ?? []
//            );
//        }

        // $params = StringHelper::deepFilterDatas($params, ['trim', 'strip_tags']);

        return $params;
    }


//    public static function outputJson(Request $request, $resp)
//    {
//        header('Content-type: text/json');
//        header('Content-type: application/json; charset=UTF-8');

//        header('Access-Control-Allow-Origin: *');
//        header('Access-Control-Allow-Credentials: true');
//        header('Access-Control-Max-Age: 864000');
//
//        // 允许所有自定义请求头
//        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
//            header('Access-Control-Allow-Headers: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
//        }
//        return response()->json($resp);
    // 创建一个 JSONP 响应
//        return response()
//            ->json($resp)
//            ->withCallback($request->input('callback'));

//    }


    // 获得翻页的三个关键参数
    public static function getPageParams(Request $request){

        // 当前页page,如果不正确默认第一页
        $page = self::getInt($request, 'page');
        if ( (! is_numeric($page)) || $page<=0 ){ $page = 1; }

        // 每页显示的数量,取值1 -- 100 条之间,默认15条
        $pagesize = self::getInt($request, 'pagesize');
        //if ( (! is_numeric($pagesize)) || $pagesize <= 0 || $pagesize > 100 ){ $pagesize = 15; }
        if ( (! is_numeric($pagesize)) || $pagesize <= 0 || $pagesize > 10000 ){ $pagesize = 15; }

        // 总记录数,优化方案：传0传重新获取总数，如果传了，则不会再获取，而是用传的，减软数据库压力
        $total = self::getInt($request, 'total');
        if ( (! is_numeric($total)) || $total<0 ){ $total = 0; }
        return [
            'page' => $page,
            'pagesize' => $pagesize,
            'total' => $total,
        ];
    }


    /**
     * 查询参数处理
     *
     * @return  array :错误信息 array:查询参数及查询关系参数
     * @author zouyan(305463219@qq.com)
     */
    public static function getQueryRelations(Request $request){
        // 查询条件参数
        $queryParams = self::get($request, 'queryParams');
        if(empty($queryParams)) $queryParams = [];
        // json 转成数组
        jsonStrToArr($queryParams , 1, '参数[queryParams]格式有误!');

        // 查询关系参数
        $relations = self::get($request, 'relations');
        if(empty($relations)) $relations = [];
        // json 转成数组
        jsonStrToArr($relations , 1, '参数[relations]格式有误!');

        /*
        $queryParams = [
            'where' => [
                ['id', '>', '0'],
                //  ['phonto_name', 'like', '%知的标题1%']
            ],
            'orderBy' => ['id'=>'desc','company_id'=>'asc'],
            // 'limit' => $pagesize,
            // 'take' => $pagesize,
            // 'offset' => $offset,
            // 'skip' => $offset,
        ];
        $relations = ['siteResources','CompanyInfo.proUnits.proRecords','CompanyInfo.companyType'];
        */
        return [
            'queryParams' => $queryParams,
            'relations' => $relations,
        ];
    }

    /**
     * api请求数据验签
     *
     * @param Request $request 请求信息
     * @param array $params 需要验签的数据 ,为空，则重新获取
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $appsecret 密匙
     * @return boolean 结果 是否合法请求 true:合法请求;  sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function apiJudgeSign(Request $request, $params = [], $errDo = 1, $appsecret = ''){

        if(empty($params)) $params = CommonRequest::getParamsByUbound($request, 2, false, [], []);

        /**
         *
         *  服务端接到这个请求：
         *  1 先验证sign签名是否合理，证明请求参数没有被中途篡改
         *  2 再验证timestamp是否过期，证明请求是在最近60s被发出的
         *  3 最后验证nonce是否已经有了，证明这个请求不是60s内的重放请求
         *
         */
        $otherParams = [
             'paramsurlsafe' => false, // boolean 加密前的字符-是否进行urldecode转换 true:转换;false:不转换[默认] -
             'urlsafe' => false,// boolean  加密后的字符-是否进行url传输转换 true:转换;false:不转换[默认] -
         ];
        // $appsecret = '';
        $timestamp = $params['timestamp'] ?? 0;
        if(!is_numeric($timestamp) || $timestamp <= 0) {
            $errMsg = '请求参数不完整!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
            // return false;
        }
        $nonceStr = $params['noncestr'] ?? 0;
        if( (!is_string($nonceStr) && !is_numeric($nonceStr)) || strlen($nonceStr) <=0) {
            $errMsg = '请求参数不完整!!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
            // return false;
        }
        if( HttpRequest::verifySign($otherParams, $params, 'sign', $appsecret, 5, 2, FALSE)
         && HttpRequest::isValidTimestamp($timestamp,  1 * 60)
             && HttpRequest::isValidNonce($nonceStr, 1 * 60)
        ) return true;// 成功

        $errMsg = '请求不合法!!';
        if($errDo == 1) throws($errMsg);
        return $errMsg;
       //  return false;// 失败
    }
}
