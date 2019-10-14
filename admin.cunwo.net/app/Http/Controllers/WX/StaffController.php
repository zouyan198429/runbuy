<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPICommonAddrBusiness;
use App\Business\Controller\API\RunBuy\CTAPIStaffBusiness;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffController extends BaseController
{


    // ajax获得列表数据
//    public function ajax_alist(Request $request){
//        // $redisKey =  CommonRequest::get($request, 'redisKey'); // 登陆redis 键;
//        $this->InitParams($request);
//        $user_id = $this->user_id;
//        // $resultDatas = [];
//        // return ajaxDataArr(1, $resultDatas, '');
//        // 必须要有 ower_type    ower_id
//        CTAPIStaffBusiness::mergeRequest($request, $this, [
//            'ower_type' => 64,// 拥有者类型1平台2城市分站4城市代理8商家16店铺32快跑人员64用户
//            'ower_id' => $user_id,
//        ]);
//        return  CTAPIStaffBusiness::getList($request, $this, 2 + 4, []); //
//    }

    // ajax获得详情数据
    public function ajax_info(Request $request){
        $this->InitParams($request);
        $id = $this->user_id;
        $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], ['cityinfo', 'face', 'back'], []);// , ['city']


        $city_id = 0;
        $city_name = '';
        if(isset($info['cityinfo']) && !empty($info['cityinfo'])){
            $city_id = $info['cityinfo']['id'] ?? 0;
            $city_name = $info['cityinfo']['city_name'] ?? 0;;
        }
        // 身份证正反面
        $resource_list = [];

        if(isset($info['face']) && !empty($info['face'])){
            $resource_list[] = Tool::formatResource($info['face'], 1);
        }

        if(isset($info['back']) && !empty($info['back'])){
            $resource_list[] = Tool::formatResource($info['back'], 1);
        }

        $result = [
            'id' => $info['id'] ?? 0,
            'admin_type' => $info['admin_type'] ?? 0,
            'city_site_id' => $info['city_site_id'] ?? 0,
            'city_id' => $city_id,
            'city_name' => $city_name,
            'city_partner_id' => $info['city_partner_id'] ?? 0,
            'issuper' => $info['issuper'] ?? 0,
            'open_status' => $info['open_status'] ?? 0,
            'open_fail_reason' => $info['open_fail_reason'] ?? '',
            'account_status' => $info['account_status'] ?? 0,
            'frozen_fail_reason' => $info['frozen_fail_reason'] ?? '',
            'on_line' => $info['on_line'] ?? 0,
            'real_name' => $info['real_name'] ?? '',
            'on_time' => $info['on_time'] ?? '',
            'sex' => $info['sex'] ?? 0,
            'tel' => $info['tel'] ?? '',
            'mobile' => $info['mobile'] ?? '',
            'nickname' => $info['nickname'] ?? '',
            'gender' => $info['gender'] ?? '',
            'province' => $info['province'] ?? '',
            'city' => $info['city'] ?? '',
            'country' => $info['country'] ?? '',
            'avatar_url' => $info['avatar_url'] ?? '',
            'longitude' => $info['longitude'] ?? '',
            'latitude' => $info['latitude'] ?? '',
            'created_at' => $info['created_at'] ?? '',
            'admin_type_text' => $info['admin_type_text'] ?? '',
            'issuper_text' => $info['issuper_text'] ?? '',
            'sex_text' => $info['sex_text'] ?? '',
            'account_status_text' => $info['account_status_text'] ?? '',
            'open_status_text' => $info['open_status_text'] ?? '',
            'on_line_text' => $info['on_line_text'] ?? '',
            'resource_list' => $resource_list ?? [],
        ];

        return ajaxDataArr(1, $result, '');
    }

    /**
     * ajax保存数据-操作类型  operate_type 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
     *   reason 原因
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_operate(Request $request)
    {
        $this->InitParams($request);
        $id = $this->user_id;
        CTAPIStaffBusiness::mergeRequest($request, $this, [
            'id' => $id,// 订单类型1普通订单/父订单4子订单
        ]);
        $resultDatas = CTAPIStaffBusiness::staffOperateById($request, $this, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax保存数据-修改信息，提交审核
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)    {
        $this->InitParams($request);
        $user_id = $this->user_id;

        $id = $user_id;// CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        $real_name = CommonRequest::get($request, 'real_name');
        // $sex = CommonRequest::getInt($request, 'sex');
        $mobile = CommonRequest::get($request, 'mobile');
//        $addr_name = CommonRequest::get($request, 'addr_name');
//        $addr = CommonRequest::get($request, 'addr');
//        $is_default = CommonRequest::getInt($request, 'is_default');
//        $latitude = CommonRequest::get($request, 'latitude');
//        $longitude = CommonRequest::get($request, 'longitude');

        $saveData = [
//            'ower_type' => 64,
            'city_site_id' => $city_site_id,
            'real_name' => $real_name,
            'open_status' => 1,// 审核状态1待审核2审核通过3审核未通过--32快跑人员用
            // 'open_fail_reason' => '',
//            'sex' => $sex,
            'mobile' => $mobile,
//            'addr_name' => $addr_name,
//            'addr' => $addr,
//            'is_default' => $is_default,
//            'latitude' => $latitude,
//            'longitude' => $longitude,
        ];
        $saveData['operate_type'] = 1;

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

}
