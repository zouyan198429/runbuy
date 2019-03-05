<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPIShopTypeBusiness;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopTypeController extends BaseController
{

    // ajax获得列表数据
    public function ajax_alist(Request $request){
        // $this->InitParams($request);
        // $resultDatas = [];
        // return ajaxDataArr(1, $resultDatas, '');
        $result = CTAPIShopTypeBusiness::getList($request, $this, 2 + 4, [], ['siteResources']); //

        $data_list = $result['result']['data_list'] ?? [];

        foreach($data_list as $k => $v){
            $data_list[$k]['resource_url'] = $v['resource_list'][0]['resource_url'] ?? '';
            // if(isset($v['resource_list']))  unset($data_list[$k]['resource_list']);
        }
        $result['result']['data_list'] = $data_list;
        return $result;
    }

    // ajax获得详情数据
    public function ajax_info(Request $request,$id = 0){
        // $this->InitParams($request);
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');

        $info = CTAPIShopTypeBusiness::getInfoData($request, $this, $id, [], [ 'siteResources']);// , ['city']
        $info['resource_url'] = $info['resource_list'][0]['resource_url'] ?? '';
        // if(isset($info['resource_list']))  unset($info['resource_list']);
        return ajaxDataArr(1, $info, '');
    }
}
