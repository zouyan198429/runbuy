<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPINoticeBusiness;
use App\Services\Request\CommonRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoticeController extends BaseController
{
    // ajax获得列表数据
    public function ajax_alist(Request $request){
        // $this->InitParams($request);
        // $resultDatas = [];
        // return ajaxDataArr(1, $resultDatas, '');
        return  CTAPINoticeBusiness::getList($request, $this, 2 + 4, []);
    }

    // ajax获得详情数据
    public function ajax_info(Request $request,$id = 0){
        // $this->InitParams($request);
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');

        $info = CTAPINoticeBusiness::getInfoData($request, $this, $id, []);// , ['city']
        return ajaxDataArr(1, $info, '');
    }
    // ajax获得详情数据--根据城市id
    public function ajax_infoByCityId(Request $request,$city_id = 0){
        // $this->InitParams($request);
        if(!is_numeric($city_id) || $city_id <=0) return ajaxDataArr(0, null, '参数[city_id]有误！');

        $queryParams = [
            'where' => [
                ['city_site_id', '=', $city_id],
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
             'orderBy' => ['id'=>'desc'],
        ];
        $info = CTAPINoticeBusiness::getInfoByQuery($request, $this, '', $this->company_id, $queryParams);
        // if(empty($info) ) return ajaxDataArr(0, null, '系统还没有设置此内容！');

//        $city_name = $info['city_history']['city_name'] ?? '';
//        if(empty($city_name)) $city_name = $info['city']['city_name'] ?? '';
//        $info['city_name'] = $city_name;
//        if(isset($info['city_history'])) unset($info['city_history']);
//        if(isset($info['city'])) unset($info['city']);
        return ajaxDataArr(1, $info, '');
    }
}
