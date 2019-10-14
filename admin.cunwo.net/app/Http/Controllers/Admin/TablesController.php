<?php

namespace App\Http\Controllers\Admin;

use App\Business\Controller\API\RunBuy\CTAPINumPrefixBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopBusiness;
use App\Business\Controller\API\RunBuy\CTAPITablePersonBusiness;
use App\Business\Controller\API\RunBuy\CTAPITablesBusiness;
use App\Http\Controllers\WorksController;
use App\Services\File\DownFile;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class TablesController extends WorksController
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
        // 是否开启
        $reDataArr['isOpen'] =  CTAPITablesBusiness::$isOpenArr;
        $reDataArr['defaultIsOpen'] = -1;// 列表页默认状态

        // 状态
        $reDataArr['status'] =  CTAPITablesBusiness::$statusArr;
        $reDataArr['defaultStatus'] = 2;// 列表页默认状态
        $reDataArr['countStatus'] = [1,2,4];// 列表页需要统计的状态数组
        $reDataArr['countPlayStatus'] = '2';// '2,4';// 需要播放提示声音的状态，多个逗号,分隔

        // 桌位人数分类
//        $reDataArr['table_person_kv'] = CTAPITablePersonBusiness::getListKV($request, $this);
//        $reDataArr['defaultTablePersonId'] = -1;// 默认

        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  CommonRequest::getInt($request, 'seller_id');
        $reDataArr['shop_id'] =  CommonRequest::getInt($request, 'shop_id');
        return view('admin.tables.index', $reDataArr);
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
//        $reDataArr['province_kv'] = CTAPITablesBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPITablesBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        return view('admin.tables.select', $reDataArr);
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
        $shop_id =  CommonRequest::getInt($request, 'shop_id');
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
            'now_shop_state' => 0,
            'shop_id' => $shop_id,
            'resource_list' => [],
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPITablesBusiness::getInfoData($request, $this, $id, [], ['shop', 'siteResources'], []);
        }else{
            if($shop_id > 0 ){
                $partnerInfo = CTAPIShopBusiness::getInfoHistoryId($request, $this, $shop_id, []);
                $info['shop_name'] = $partnerInfo['shop_name'] ?? '';
                $info['shop_id_history'] = $partnerInfo['history_id'] ?? 0;
                $info['now_shop_state'] = $partnerInfo['now_state'] ?? 0;
            }
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        // 是否开启
        $reDataArr['isOpen'] =  CTAPITablesBusiness::$isOpenArr;
        $reDataArr['defaultIsOpen'] = $info['is_open'] ?? -1;// 列表页默认状态

        // 状态
        $reDataArr['status'] =  CTAPITablesBusiness::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 列表页默认状态

        // 桌位人数分类
//        $reDataArr['table_person_kv'] = CTAPITablePersonBusiness::getListKV($request, $this);
//        $reDataArr['defaultTablePersonId'] = $info['table_person_id'] ?? -1;// 默认
        return view('admin.tables.add', $reDataArr);
    }


    /**
     * 打印二维码--不需要登录就能访问
     *
     * @param Request $request
     * @param string $ids 多个用逗号分隔
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function print(Request $request,$ids = 0)
    {
        // $this->InitParams($request);
        // $this->source = 2;
        $reDataArr = $this->reDataArr;
        $relations = '';//  CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'relationsArr');
        $extParams = [
            'useQueryParams' => true,// '是否用来拼接查询条件，true:用[默认];false：不用'
            'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
                'select' => [
                    'id','table_name','has_qrcode','qrcode_url'
                 ],
            ],
//            'formatDataUbound' => CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'formatDataUbound'),
        ];
        //  显示到定位点的距离
        CTAPITablesBusiness::mergeRequest($request, $this, [
            'has_qrcode' => 2,// 是否已生成二维码1未生成2已生成
            'ids' => $ids,
        ]);
        $result = CTAPITablesBusiness::getList($request, $this, 2 , [], $relations, $extParams, 1);
        $data_list = $result['result']['data_list'] ?? [];

        $reDataArr['webName'] = config('public.webName');// 系统名称
        $reDataArr['orderList'] = $data_list;//订单列表
        return view('admin.tables.print_rqcode', $reDataArr);
    }

    /**
     * 下载二维码
     *
     * @param Request $request
     * @param int $id id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function down(Request $request,$id = 0)
    {
         $this->InitParams($request);
        // $this->source = 2;
        $reDataArr = $this->reDataArr;
        $relations = '';//  CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'relationsArr');

        $info = CTAPITablesBusiness::getInfoData($request, $this, $id, ['id','table_name','has_qrcode','qrcode_url'], $relations, []);

        $has_qrcode = $info['has_qrcode'] ?? 1;
        $qrcode_url = $info['qrcode_url'] ?? '';//  http://runbuy.admin.cunwo.net/resource/company/1/images/qrcode/tables/1.png
        $qrcode_url_old = $info['qrcode_url_old'] ?? '';// /resource/company/1/images/qrcode/tables/1.png
        if($has_qrcode != 2 ) die('记录不存在或未生成二维码');
        // 下载二维码文件
        $publicPath = Tool::getPath('public');
        $res = DownFile::downFilePath(2, $publicPath . $qrcode_url_old);
        if(is_string($res)) echo $res;
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
        $seller_id = CommonRequest::getInt($request, 'seller_id');
        $shop_id = CommonRequest::getInt($request, 'shop_id');
        $table_name = CommonRequest::get($request, 'table_name');
        $sort_num = CommonRequest::getInt($request, 'sort_num');
        $is_open = CommonRequest::getInt($request, 'is_open');
        $table_person_id = CommonRequest::getInt($request, 'table_person_id');

        // 图片资源
        $resource_id = CommonRequest::get($request, 'resource_id');
        if(is_string($resource_id) || is_numeric($resource_id)){
            if(strlen(trim($resource_id)) > 0){
                $resource_id = explode(',' ,$resource_id);
            }
        }
        if(!is_array($resource_id)) $resource_id = [];

        $resource_ids = implode(',', $resource_id);
        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

        $saveData = [
            'seller_id' => $seller_id,
            'shop_id' => $shop_id,
            'table_name' => $table_name,
            'is_open' => $is_open,
            'table_person_id' => $table_person_id,
            'sort_num' => $sort_num,
            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            'resourceIds' => $resource_id,// 此下标为图片资源关系
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPITablesBusiness::replaceById($request, $this, $saveData, $id, true);
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
        // $relations = ['city', 'cityPartner', 'seller', 'shop' , 'tablePerson'];
        $relations = CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'relationsArr');
        $extParams = [
            'formatDataUbound' => CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'formatDataUbound'),
        ];
        return  CTAPITablesBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
    }

    /**
     * ajax获得统计数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_getCountByStatus(Request $request)
    {
        $this->InitParams($request);
        $user_id = $this->user_id;
        $status = '1,2,4';// 订单状态,多个用逗号分隔, 可为空：所有的
        $otherWhere = [
            ['is_open', '=', 2]
//            ['order_type', '=', 1]// // 订单类型1普通订单/父订单4子订单
            // ,['staff_id', '=', $user_id]
        ];//  其它条件[['company_id', '=', $company_id],...]

//        $staff_id = CommonRequest::getInt($request, 'staff_id');
//        if($staff_id > 0 )  array_push($otherWhere, ['staff_id', '=', $staff_id]);

//        $send_staff_id = CommonRequest::getInt($request, 'send_staff_id');
//        if($send_staff_id > 0 )  array_push($otherWhere, ['send_staff_id', '=', $send_staff_id]);

        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        if($city_site_id > 0 )  array_push($otherWhere, ['city_site_id', '=', $city_site_id]);

        $city_partner_id = CommonRequest::getInt($request, 'city_partner_id');
        if($city_partner_id > 0 )  array_push($otherWhere, ['city_partner_id', '=', $city_partner_id]);

        $seller_id = CommonRequest::getInt($request, 'seller_id');
        if($seller_id > 0 )  array_push($otherWhere, ['seller_id', '=', $seller_id]);

        $shop_id = CommonRequest::getInt($request, 'shop_id');
        if($shop_id > 0 )  array_push($otherWhere, ['shop_id', '=', $shop_id]);

        $statusCountList = CTAPITablesBusiness::getStatusCount($request, $this, $status, $otherWhere, 1);
        return ajaxDataArr(1, $statusCountList, '');
    }


    /**
     * ajax获得统计数据 状态统计
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_status_count(Request $request)
    {
        $this->InitParams($request);
        $user_id = $this->user_id;
        $status = '1,2,4';// CommonRequest::get($request, 'status');// 订单状态,多个用逗号分隔, 可为空：所有的
        $otherWhere = [
            ['is_open', '=', 2]
//            ['order_type', '=', 1]// // 订单类型1普通订单/父订单4子订单
            // ,['staff_id', '=', $user_id]
        ];//  其它条件[['company_id', '=', $company_id],...]

//        $staff_id = CommonRequest::getInt($request, 'staff_id');
//        if($staff_id > 0 )  array_push($otherWhere, ['staff_id', '=', $staff_id]);

//        $send_staff_id = CommonRequest::getInt($request, 'send_staff_id');
//        if($send_staff_id > 0 )  array_push($otherWhere, ['send_staff_id', '=', $send_staff_id]);

        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        if($city_site_id > 0 )  array_push($otherWhere, ['city_site_id', '=', $city_site_id]);

        $city_partner_id = CommonRequest::getInt($request, 'city_partner_id');
        if($city_partner_id > 0 )  array_push($otherWhere, ['city_partner_id', '=', $city_partner_id]);

        $seller_id = CommonRequest::getInt($request, 'seller_id');
        if($seller_id > 0 )  array_push($otherWhere, ['seller_id', '=', $seller_id]);

        $shop_id = CommonRequest::getInt($request, 'shop_id');
        if($shop_id > 0 )  array_push($otherWhere, ['shop_id', '=', $shop_id]);

        $statusCountList = CTAPITablesBusiness::getStatusCount($request, $this, $status, $otherWhere, 1);
        return ajaxDataArr(1, $statusCountList, '');
    }

    /**
     * ajax生成二维码
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_create_qrcode(Request $request){
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        if(!is_numeric($id) || $id <= 0) ajaxDataArr(0, null, '请求参数有误！');

        $urlInfo = CTAPITablesBusiness::createQrcode($request, $this, $id, 'sweep_order', 1);
        return ajaxDataArr(1, $urlInfo, '');
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
//        $result = CTAPITablesBusiness::getList($request, $this, 1 + 0);
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
//        CTAPITablesBusiness::getList($request, $this, 1 + 0);
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
//        CTAPITablesBusiness::importTemplate($request, $this);
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
        return CTAPITablesBusiness::delAjax($request, $this);
    }

    /**
     * ajax获得商品分类信息;根据店铺id，获得店铺分类信息数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_get_kv(Request $request){
        $this->InitParams($request);
        // 获得一级城市信息一维数组[$k=>$v]
        $listKV = CTAPITablesBusiness::getListKV($request, $this);

        return  ajaxDataArr(1, $listKV, '');
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
//        $childKV = CTAPITablesBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPITablesBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPITablesBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPITablesBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
