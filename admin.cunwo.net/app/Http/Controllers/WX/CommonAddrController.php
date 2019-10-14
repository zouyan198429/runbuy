<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPICommonAddrBusiness;
use App\Services\Request\CommonRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommonAddrController extends BaseController
{


    // ajax获得列表数据
    public function ajax_alist(Request $request){
        // $redisKey =  CommonRequest::get($request, 'redisKey'); // 登陆redis 键;
        $this->InitParams($request);
        $user_id = $this->user_id;
        // $resultDatas = [];
        // return ajaxDataArr(1, $resultDatas, '');
        // 必须要有 ower_type    ower_id
        CTAPICommonAddrBusiness::mergeRequest($request, $this, [
            'ower_type' => 64,// 拥有者类型1平台2城市分站4城市代理8商家16店铺32快跑人员64用户
            'ower_id' => $user_id,
        ]);
        return  CTAPICommonAddrBusiness::getList($request, $this, 2 + 4, []); //
    }

    // ajax获得详情数据
    public function ajax_info(Request $request,$id = 0){
        $this->InitParams($request);
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');

        $info = CTAPICommonAddrBusiness::getInfoData($request, $this, $id, [], '', []);// , ['city']
        return ajaxDataArr(1, $info, '');
    }

    // ajax获得详情数据--默认地址或最新的第一条[没有设置默认]
    public function ajax_firstInfo(Request $request){
         $this->InitParams($request);
        $user_id = $this->user_id;

        $queryParams = [
            'where' => [
                ['ower_type', '=', 64],// 拥有者类型1平台2城市分站4城市代理8商家16店铺32快跑人员64用户
                ['ower_id', '=', $user_id],
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
            'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
        ];
        $info = CTAPICommonAddrBusiness::getInfoByQuery($request, $this, '', $this->company_id, $queryParams);
        // if(empty($info) ) return ajaxDataArr(0, null, '系统还没有设置此内容！');

//        $city_name = $info['city_history']['city_name'] ?? '';
//        if(empty($city_name)) $city_name = $info['city']['city_name'] ?? '';
//        $info['city_name'] = $city_name;
//        if(isset($info['city_history'])) unset($info['city_history']);
//        if(isset($info['city'])) unset($info['city']);
        return ajaxDataArr(1, $info, '');
    }
    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)    {
        $this->InitParams($request);
        $user_id = $this->user_id;

        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        // $ower_id = CommonRequest::getInt($request, 'ower_id');
        $real_name = CommonRequest::get($request, 'real_name');
        $sex = CommonRequest::getInt($request, 'sex');
        $mobile = CommonRequest::get($request, 'mobile');
        $addr_name = CommonRequest::get($request, 'addr_name');
        $addr = CommonRequest::get($request, 'addr');
        if($addr_name == $addr){
            $addr_name = "";
        }
        $is_default = CommonRequest::getInt($request, 'is_default');
        $latitude = CommonRequest::get($request, 'latitude');
        $longitude = CommonRequest::get($request, 'longitude');

        $saveData = [
            'ower_type' => 64,
            'ower_id' => $user_id,
            'real_name' => $real_name,
            'sex' => $sex,
            'mobile' => $mobile,
            'addr_name' => $addr_name,
            'addr' => $addr,
            'is_default' => $is_default,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPICommonAddrBusiness::replaceById($request, $this, $saveData, $id, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_del(Request $request)
    {
        $this->InitParams($request);
        return CTAPICommonAddrBusiness::delAjax($request, $this);
    }
}
