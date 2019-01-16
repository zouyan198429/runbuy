<?php

namespace App\Http\Controllers\Admin;

use App\Business\Controller\API\RunBuy\CTAPICityBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopBusiness;
use App\Business\Controller\API\RunBuy\CTAPIShopGoodsBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class ShopGoodsController extends WorksController
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
        // 热销
        $reDataArr['isHot'] =  CTAPIShopGoodsBusiness::$isHotArr;
        $reDataArr['defaultIsHot'] = -1;// 默认状态
        // 是否上架
        $reDataArr['isSale'] =  CTAPIShopGoodsBusiness::$isSaleArr;
        $reDataArr['defaultIsSale'] = -1;// 默认状态

        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  CommonRequest::getInt($request, 'seller_id');
        $reDataArr['shop_id'] =  CommonRequest::getInt($request, 'shop_id');
        return view('admin.shopGoods.index', $reDataArr);
    }

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
            'price_type' => 1,
          //   'department_id' => 0,
            'now_shop_state' => 0,
            'shop_id' => $shop_id,
            'resource_list' => [],
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPIShopGoodsBusiness::getInfoData($request, $this, $id, [], ['shop', 'siteResources']);
            $intro = $info['intro'] ?? '';
            $info['intro'] = replace_enter_char($intro,2);
        }else{
            if($shop_id > 0 ){
                $partnerInfo = CTAPIShopBusiness::getInfoHistoryId($request, $this, $shop_id, []);
                $info['shop_name'] = $partnerInfo['shop_name'] ?? '';
                $info['shop_id_history'] = $partnerInfo['history_id'] ?? 0;
                $info['now_shop_state'] = $partnerInfo['now_state'] ?? 0;
            }
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        // 热销
        $reDataArr['isHot'] =  CTAPIShopGoodsBusiness::$isHotArr;
        $reDataArr['defaultIsHot'] = $info['is_hot'] ?? 1;// 默认状态
        // 是否上架
        $reDataArr['isSale'] =  CTAPIShopGoodsBusiness::$isSaleArr;
        $reDataArr['defaultIsSale'] = $info['is_sale'] ?? 1;// 默认状态
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        return view('admin.shopGoods.add', $reDataArr);
    }


    /**
     * 选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function select(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        // 热销
        $reDataArr['isHot'] =  CTAPIShopGoodsBusiness::$isHotArr;
        $reDataArr['defaultIsHot'] = -1;// 默认状态
        // 是否上架
        $reDataArr['isSale'] =  CTAPIShopGoodsBusiness::$isSaleArr;
        $reDataArr['defaultIsSale'] = -1;// 默认状态

        $reDataArr['city_site_id'] =  CommonRequest::getInt($request, 'city_site_id');
        $reDataArr['city_partner_id'] =  CommonRequest::getInt($request, 'city_partner_id');
        $reDataArr['seller_id'] =  CommonRequest::getInt($request, 'seller_id');
        $reDataArr['shop_id'] =  CommonRequest::getInt($request, 'shop_id');
        return view('admin.shopGoods.select', $reDataArr);
    }

    /**
     * 选中/更新
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_selected(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        $info = CTAPIShopGoodsBusiness::getInfoHistoryId($request, $this, $id, []);
        return ajaxDataArr(1, $info, '');

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
        $type_id = CommonRequest::getInt($request, 'type_id');
        $goods_name = CommonRequest::get($request, 'goods_name');
        $sort_num = CommonRequest::getInt($request, 'sort_num');
        $is_hot = CommonRequest::getInt($request, 'is_hot');
        $is_sale = CommonRequest::getInt($request, 'is_sale');
        $price = CommonRequest::get($request, 'price');
        $intro = CommonRequest::get($request, 'intro');
        $intro =  replace_enter_char($intro,1);
        $price_type = CommonRequest::getInt($request, 'price_type');
        if($price_type == 2) $price = 0;

        // 图片资源
        $resource_id = CommonRequest::get($request, 'resource_id');
        if(is_string($resource_id) || is_numeric($resource_id)){
            $resource_id = explode(',' ,$resource_id);
        }

        $resource_ids = implode(',', $resource_id);
        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

        // 属性
        $prop_ids = CommonRequest::get($request, 'prop_ids');// 属性id
        if(is_string($prop_ids) || !is_array($prop_ids)) $prop_ids = explode(',', $prop_ids);

        $prop_id_historys = CommonRequest::get($request, 'prop_id_historys');// 属性历史id
        if(is_string($prop_id_historys) || !is_array($prop_id_historys)) $prop_id_historys = explode(',', $prop_id_historys);

        $is_prices = CommonRequest::get($request, 'is_prices');// 是否价格属性 0否/1是
        if(is_string($is_prices) || !is_array($is_prices)) $is_prices = explode(',', $is_prices);

        $is_multis = CommonRequest::get($request, 'is_multis');// 是否多选属性 0否/1是
        if(is_string($is_multis) || !is_array($is_multis)) $is_multis = explode(',', $is_multis);

        $is_musts = CommonRequest::get($request, 'is_musts');// 是否必填属性 0否/1是
        if(is_string($is_musts) || !is_array($is_musts)) $is_musts = explode(',', $is_musts);

        $pv_ids = CommonRequest::get($request, 'pv_ids');// 属性值id串数组 ['1,2','3,4'] 多个用,号分隔
        if(is_string($pv_ids) || !is_array($pv_ids)) $pv_ids = explode(',', $pv_ids);


        // 属性价格
        $price_pv_id = CommonRequest::get($request, 'price_pv_id');// 属性值id数组
        if(is_string($price_pv_id) || !is_array($price_pv_id)) $price_pv_id = explode(',', $price_pv_id);

        $price_pv_val = CommonRequest::get($request, 'price_pv_val');// 属性值价格数组
        if(is_string($price_pv_val) || !is_array($price_pv_val)) $price_pv_val = explode(',', $price_pv_val);

        $pvList = [];// 属性价格数组 ['属性值id' => '属性值价格',....]
        foreach ($price_pv_id as $k => $pvId){
            $pvList[$pvId] = $price_pv_val[$k];
        }

        $propList = [];
        // $propPriceList = [];
        // $pCount = count($prop_ids);
        foreach ($prop_ids as $k => $pId){
            $temProp = [
                'is_prices' => $is_prices[$k],
                'is_multi' => $is_multis[$k],
                'is_must' => $is_musts[$k],
                'prop_id' => $pId,
                // 'sort_num' => $pCount--,

                'pv_ids' => $pv_ids[$k],
            ];
            array_push($propList, $temProp);
            // $isPrices = $is_prices[$k] ?? 0;

        }



        $saveData = [
            'seller_id' => $seller_id,
            'shop_id' => $shop_id,
            'type_id' => $type_id,
            'is_hot' => $is_hot,
            'is_sale' => $is_sale,
            'goods_name' => $goods_name,
            'sort_num' => $sort_num,
            'price' => $price,
            'price_type' => $price_type,
            'intro' => $intro,
            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            'resourceIds' => $resource_id,// 此下标为图片资源关系

            'prop_list' => $propList,// 属性
            'pv_prices' => $pvList,// 属性价格数组 ['属性值id' => '属性值价格',....]
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $resultDatas = CTAPIShopGoodsBusiness::replaceById($request, $this, $saveData, $id, true);
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
        return  CTAPIShopGoodsBusiness::getList($request, $this, 2 + 4, [], ['city', 'cityPartner', 'seller'
            , 'shop', 'type', 'siteResources', 'priceProps.propVal.name']);
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
//        $result = CTAPIShopGoodsBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIShopGoodsBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIShopGoodsBusiness::importTemplate($request, $this);
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
        return CTAPIShopGoodsBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIShopGoodsBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIShopGoodsBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIShopGoodsBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIShopGoodsBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * ajax获得属性数据-根据商品id
     *
     * @param Request $request
     * @return mixed
     * @author liuxin
     */
    public function ajax_get_prop(Request $request){
        $this->InitParams($request);
        $prop_list = CTAPIShopGoodsBusiness::getPropByGoodId($request, $this);
        return ajaxDataArr(1, ['data_list' => $prop_list], '');
    }

}
