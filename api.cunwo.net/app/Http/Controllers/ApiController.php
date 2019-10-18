<?php

namespace App\Http\Controllers;


use App\Services\DB\CommonDB;
use App\Services\Tool;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /*
     *  Dingo
     * 响应生成器提供了一个流畅的接口去方便的建立一个更定制化的响应。响应的生成器通常是与 transformer 相结合。
     * 要利用响应生成器，你的控制器需要使用 Dingo\Api\Routing\Helpers trait。为了在你的控制器里保持引入和使用这个 trait，
     * 你可以创建一个基础控制器，然后你的所有的 API 控制器都继承它。
     *
     */
    use Helpers;

    protected $company_id = null;
    protected $pro_unit_id = null;
    protected $cache_sel = 1 + 2;//是否强制不缓存 1:缓存读,读到则直接返回;2缓存数据

//    public function init()
//    {
//        parent::init();
//
//        // CORS 预检请求
//        if (Yii::$app->getRequest()->getIsOptions()) {
//            return okJson('Preflight');
//        }
//    }

    /**
     * 主参数判断
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function InitParams(Request $request)
    {
        return true;
    }

    /**
     * 获得缓存数据
     *
     * @param string $pre 键前缀 __FUNCTION__
     * @param string $cacheKey 键
     * @param array $paramKeyValArr 会作为键的关键参数值数组 --一维数组
     * @param int 选填 $operate 操作 1 转为json 2 序列化 ;
     * @param keyPush 键加入无素 1 $pre 键前缀 2 当前控制器方法名;
     * @return mixed  ; false失败
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheData($pre, &$cacheKey, $paramKeyValArr, $operate = 1, $keyPush = 0){
         return Tool::getCacheData($pre, $cacheKey, $paramKeyValArr, $operate, $keyPush);
    }

    /**
     * 保存redis值-json/序列化保存
     * @param string 必填 $pre 前缀
     * @param string $key 键 null 自动生成
     * @param string 选填 $cacheData 需要保存的值，如果是对象或数组，则序列化
     * @param int 选填 $expire 有效期 秒 <=0 长期有效
     * @param int 选填 $operate 操作 1 转为json 2 序列化
     * @return $key
     * @author zouyan(305463219@qq.com)
     */
    public function setCacheData($pre, $cacheKey, $cacheData, $expire = 60, $operate =1){
        // 缓存数据
        return Tool::cacheData($pre, $cacheKey, $cacheData, $expire, $operate); // 1分钟
    }
}
