<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPIStaffBusiness;
use App\Services\MiniProgram\MiniProgram;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class MiniProgramController extends BaseController
{

    public function aaaa(Request $request){
        return 'dddddd';
    }
    public function test(Request $request){
        // echo($expiry_time);
        //  $this->source = 2;
        // throws('aaaa');

        $appid = 'wx4f4bc4dec97d474b';
        $sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';

        $encryptedData="CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==";

        $iv = 'r7BXXKkLb8qrSNn05n0qiA==';
        $reslut = MiniProgram::decryptData($appid, $sessionKey, $encryptedData, $iv) ;
        pr($reslut);
    }

    /**
     * 登陆-- 小程序登陆专用--用户小程序
     *
     * @param Request $request
     * @return mixed redis key
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login(Request $request)
    {
        return $this->login($request, 64);

    }

    /**
     * 登陆-- 小程序登陆专用--快跑人员小程序
     *
     * @param Request $request
     * @return mixed redis key
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login_run(Request $request)
    {
        return $this->login($request, 32);

    }

    /**
     * 登陆-- 方法
     *
     * @param Request $request
     * @param int $admin_type 用户类型 32快跑人员64用户
     * @return mixed redis key
     * @author zouyan(305463219@qq.com)
     */

    public function login(Request $request, $admin_type = 64){
        $preKey = CommonRequest::get($request, 'preKey');// 0 小程序 1后台
        if(!is_numeric($preKey)){
            $preKey = 0;
        }

        $code = CommonRequest::get($request, 'code');
        $iv = CommonRequest::get($request, 'iv');
        $encryptedData = CommonRequest::get($request, 'encryptedData');
        if(isAjax()){
            Log::info('微信日志-是ajax请求:',['']);
        }else{
            Log::info('微信日志-不是ajax请求:',['']);
        }
        $block = "default";
        if($admin_type == 32) $block = "run";
        $res = MiniProgram::login($code, $encryptedData, $iv, $block, 2);
        // 保存到数据库
        $wx_unionid = $res['unionId'] ?? '';
        $mini_openid = $res['openId'] ?? '';
        if(!isset($res['openId']) || empty($mini_openid)) throws('登录失败:openId获取失败!');
        // 获得用户id
        $id = 0;
        $saveData = [
            'admin_type' => $admin_type,// 64,
            'wx_unionid' => $wx_unionid,
            'mini_openid' => $mini_openid,
            // 'mp_openid' => $res['aaaa'] ?? '',
            'mini_session_key' => $res['session_key'] ?? '',
            'nickname' => $res['nickName'] ?? '',
            'gender' => $res['gender'] ?? 0,
            'sex' => $res['gender'] ?? 0,
            'province' => $res['province'] ?? '',
            'city' => $res['city'] ?? '',
            'country' => $res['country'] ?? '',
            'avatar_url' => $res['avatarUrl'] ?? '',
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, false, 1);

        // 缓存数据
        $redisKey = $resultDatas['redisKey'] ?? '';
        $userInfo = $resultDatas['result'] ?? [];
        if(empty($userInfo) && !is_array($userInfo)) return ajaxDataArr(0, null, '登录失败！');;

        // 保存session
        // 存储数据到session...
        if (!session_id()) session_start(); // 初始化session
        // $_SESSION['userInfo'] = $userInfo; //保存某个session信息
        $redisKey = $this->setUserInfo($userInfo, $preKey);
        $userInfo['redisKey'] = $redisKey;
        Log::info('微信日志-登陆成功:',[$userInfo]);
        return ajaxDataArr(1, $redisKey, '');
    }
}
