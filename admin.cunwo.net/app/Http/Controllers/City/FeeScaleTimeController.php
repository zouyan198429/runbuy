<?php

namespace App\Http\Controllers\City;

use App\Business\Controller\API\RunBuy\CTAPICityBusiness;
use App\Business\Controller\API\RunBuy\CTAPIFeeScaleTimeBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class FeeScaleTimeController extends WorksController
{
    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $reDataArr['city_site_id'] =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        return view('city.feeScaleTime.index', $reDataArr);
    }

    /**
     * 同事选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function select(Request $request)
//    {
//        $this->InitParams($request);
//        $reDataArr = $this->reDataArr;
//        $reDataArr['province_kv'] = CTAPIFeeScaleTimeBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPIFeeScaleTimeBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        $reDataArr['city_site_id'] =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
//        return view('city.feeScaleTime.select', $reDataArr);
//    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add(Request $request,$id = 0)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $city_site_id =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');

        // return ajaxDataArr(0, null, '参数有误！');
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
            'now_city_state' => 0,
            'city_site_id' => $city_site_id,
        ];
        $operate = "添加";

        if($city_site_id > 0 ){
            $cityInfo = CTAPICityBusiness::getInfoHistoryId($request, $this, $city_site_id, []);
        }
        $info['price_distance_default'] = $cityInfo['price_distance_default'] ?? '';// 费用距离(默认)2公里
        $info['price_distance_every'] = $cityInfo['price_distance_every'] ?? '';// 费用距离每加1公里(总路程超2公里后+*元/公里)
        $info['price_shop_default'] = $cityInfo['price_shop_default'] ?? '';// 费用店铺(默认)1家
        $info['price_shop_every'] = $cityInfo['price_shop_every'] ?? '';// 费用距离每加1店铺(每多一店铺+*元)
        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPIFeeScaleTimeBusiness::getInfoData($request, $this, $id, [], ['city']);
        }else{
            $info['city_name'] = $cityInfo['city_name'] ?? '';
            $info['city_site_id_history'] = $cityInfo['history_id'] ?? 0;
            $info['now_city_state'] = $cityInfo['now_state'] ?? 0;
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        return view('city.feeScaleTime.add', $reDataArr);
    }

    /**
     * 添加--按城市批量
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function addBath(Request $request,$city_site_id = 0)// ,$id = 0
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $city_site_id =  $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        if(empty($city_site_id) || !is_numeric($city_site_id)) return ajaxDataArr(0, null, '参数有误！');

        $info = [
            // 'id'=>$id,
            //   'department_id' => 0,
            'now_city_state' => 0,
            'city_site_id' => $city_site_id,
        ];
        $operate = "添加";

        //if($city_site_id > 0 ){
            $cityInfo = CTAPICityBusiness::getInfoHistoryId($request, $this, $city_site_id, []);
       // }
        if(empty($cityInfo)) return ajaxDataArr(0, null, '城市记录不存在！');

        $info['city_name'] = $cityInfo['city_name'] ?? '';
        $info['city_site_id_history'] = $cityInfo['history_id'] ?? 0;
        $info['now_city_state'] = $cityInfo['now_state'] ?? 0;

        $info['price_distance_default'] = $cityInfo['price_distance_default'] ?? '';// 费用距离(默认)2公里
        $info['price_distance_every'] = $cityInfo['price_distance_every'] ?? '';// 费用距离每加1公里(总路程超2公里后+*元/公里)
        if(isset($info['price_distance_every'])) $info['price_distance_every_format'] = Tool::formatMoney($info['price_distance_every'], 2, '');

        $info['price_shop_default'] = $cityInfo['price_shop_default'] ?? '';// 费用店铺(默认)1家
        $info['price_shop_every'] = $cityInfo['price_shop_every'] ?? '';// 费用距离每加1店铺(每多一店铺+*元)
        if(isset($info['price_shop_every'])) $info['price_shop_every_format'] = Tool::formatMoney($info['price_shop_every'], 2, '');
        //if ($id > 0) { // 获得详情数据
            $operate = "修改";
            // $info = CTAPIFeeScaleTimeBusiness::getInfoData($request, $this, $id, [], ['city']);
            // 获得当前城市的配置记录

       // }else{
        //}

        $extParams = [
            'useQueryParams' => false,
            'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
                'where' => [
                    ['city_site_id', $city_site_id],
                ]
            ]
        ];
        $rsData = CTAPIFeeScaleTimeBusiness::getList($request, $this, 1, [], [], $extParams);
        $timeCityList = $rsData['result']['data_list'] ?? [];
        if( !empty($timeCityList) ){
            Tool::formatTwoArrKeys($timeCityList, Tool::arrEqualKeyVal(['id', 'time_num', 'begin_time', 'end_time', 'init_price']), false);
            $timeCityList = Tool::arrUnderReset($timeCityList, 'time_num', 1);

            foreach($timeCityList as $k => $v){
                if(isset($v['init_price'])) $timeCityList[$k]['init_price_format'] = Tool::formatMoney($v['init_price'], 2, '');
            }
        }else{
            $info['price_distance_every_format'] = '';
            $info['price_shop_every_format'] = '';
        }

        if(empty($timeCityList)) $timeCityList = CTAPIFeeScaleTimeBusiness::$timeCityListDefault;
        if(empty($timeCityList))  return ajaxDataArr(0, null, '城市时间记录有误！');

        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['timeList'] = $timeCityList;
        $reDataArr['operate'] = $operate;
        return view('city.feeScaleTime.addBath', $reDataArr);
    }

    /**
     * 详情
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request, $id = 0)
    {
        $this->InitParams($request);

        $reDataArr = $this->reDataArr;

        // 详情信息
        $infoDatas = [
            'id'=>$id,
        ];

        if ($id > 0) { // 获得详情数据
            $infoDatas =CTAPIFeeScaleTimeBusiness::getInfoData($request, $this, $id, [], ['oprateStaffHistory']);
            // 修改点击点
            $id = $infoDatas['id'] ??  0;
//            $volume = $infoDatas['volume'] ??  0;
//            $saveData = [
//                'volume' => $volume + 1,
//            ];
//            CTAPIFeeScaleTimeBusiness::replaceById($request, $this, $saveData, $id, false);
//            $infoDatas['volume'] = $volume + 1;
        }
        // $reDataArr = array_merge($reDataArr, $infoDatas);
        $reDataArr['info'] = $infoDatas;

        // 上一条
        $preList = CTAPIFeeScaleTimeBusiness::getNearList($request, $this, $id, 1, 1, 0, [], '');
        $reDataArr['preList'] = $preList;
        // 下一条
        $nextList = CTAPIFeeScaleTimeBusiness::getNearList($request, $this, $id, 2, 1, 0, [], '');
        $reDataArr['nextList'] = $nextList;
        return view('manage.feeScaleTime.info', $reDataArr);
    }

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $city_site_id = $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        // $city_site_id_history = CommonRequest::getInt($request, 'city_site_id_history');
//        $title = CommonRequest::get($request, 'title');
//        $resource = CommonRequest::get($request, 'resource');
//        $content = CommonRequest::get($request, 'content');
//        $content = stripslashes($content);
        $sort_num = CommonRequest::getInt($request, 'sort_num');

        $saveData = [
            'city_site_id' => $city_site_id,
//            'title' => $title,
//            'resource' => $resource,
//            'content' => $content,
            'sort_num' => $sort_num,
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPIFeeScaleTimeBusiness::replaceById($request, $this, $saveData, $id, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax保存数据--按城市批量
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_bath(Request $request)
    {
        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $city_site_id = $this->city_site_id;// CommonRequest::getInt($request, 'city_site_id');
        // $city_site_id_history = CommonRequest::getInt($request, 'city_site_id_history');
//        $title = CommonRequest::get($request, 'title');
//        $resource = CommonRequest::get($request, 'resource');
//        $content = CommonRequest::get($request, 'content');
//        $content = stripslashes($content);
//        $sort_num = CommonRequest::getInt($request, 'sort_num');
        $price_distance_every = CommonRequest::get($request, 'price_distance_every');
        $price_shop_every = CommonRequest::get($request, 'price_shop_every');

        // 时间段价格
        $time_ids = CommonRequest::get($request, 'time_ids');// 时间段价格id
        if(is_string($time_ids) || !is_array($time_ids)) $time_ids = explode(',', $time_ids);

        $time_nums = CommonRequest::get($request, 'time_nums');// 时间编号
        if(is_string($time_nums) || !is_array($time_nums)) $time_nums = explode(',', $time_nums);

        $init_prices = CommonRequest::get($request, 'init_prices');// 时间段价格
        if(is_string($init_prices) || !is_array($init_prices)) $init_prices = explode(',', $init_prices);

        $timeCityList = CTAPIFeeScaleTimeBusiness::$timeCityListDefault;

        $timeList = [];
        $pCount = count($time_ids);
        foreach ($time_ids as $k => $tId){
            $tem_time_num = $time_nums[$k];
            $tem_arr = $timeCityList[$tem_time_num] ?? [];
            if( empty($tem_arr) || !is_array($tem_arr) )  return ajaxDataArr(0, null, '城市时间不存在！');
            $temTime = [
                'id' => $tId,
                'city_site_id' => $city_site_id,
                'time_num' => $tem_time_num,
                'begin_time' => $tem_arr['begin_time'] ?? '',
                'end_time' => $tem_arr['end_time'] ?? '',
                'init_price' => $init_prices[$k],
                'sort_num' => $pCount--,
            ];
            array_push($timeList, $temTime);
        }


        $saveData = [
            'city_site_id' => $city_site_id,
            'price_distance_every' => $price_distance_every,
            'price_shop_every' => $price_shop_every,

            'time_list' => $timeList,// 时间段价格
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
//        $resultDatas = CTAPIFeeScaleTimeBusiness::replaceById($request, $this, $saveData, $id, true);
        $resultDatas = CTAPIFeeScaleTimeBusiness::saveTimesByCityId($request, $this, $saveData, $city_site_id, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request){
        $this->InitParams($request);
        return  CTAPIFeeScaleTimeBusiness::getList($request, $this, 2 + 4, [], [ 'city', 'oprateStaffHistory']);
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_ids(Request $request){
//        $this->InitParams($request);
//        $result = CTAPIFeeScaleTimeBusiness::getList($request, $this, 1 + 0);
//        $data_list = $result['result']['data_list'] ?? [];
//        $ids = implode(',', array_column($data_list, 'id'));
//        return ajaxDataArr(1, $ids, '');
//    }


    /**
     * 导出
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPIFeeScaleTimeBusiness::getList($request, $this, 1 + 0);
//    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPIFeeScaleTimeBusiness::importTemplate($request, $this);
//    }


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
        return CTAPIFeeScaleTimeBusiness::delAjax($request, $this);
    }

    /**
     * ajax根据部门id,小组id获得所属部门小组下的员工数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_child(Request $request){
//        $this->InitParams($request);
//        $parent_id = CommonRequest::getInt($request, 'parent_id');
//        // 获得一级城市信息一维数组[$k=>$v]
//        $childKV = CTAPIFeeScaleTimeBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIFeeScaleTimeBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIFeeScaleTimeBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import(Request $request)
//    {
//        $this->InitParams($request);
//        // 上传并保存文件
//        $result = Resource::fileSingleUpload($request, $this, 1);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//        $resultDatas = CTAPIFeeScaleTimeBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
