<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPIShopBusiness;
use App\Services\Map\Map;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopController extends BaseController
{
    // ajax获得列表数据
    /*
     *  可以传入的参数
        城市  city_site_id
        店铺分类 shop_type_id
        标签 label_id
        关键字 keyword

        // 排序 orderType
        1综合排序[默认]
        2 销量排序 月销量 -最近30天  mon_sales_volume
        4 最近的店铺
        排序[综合排序用] sort_num
     */
    public function ajax_alist(Request $request){
        // $this->InitParams($request);
        // $resultDatas = [];
        // return ajaxDataArr(1, $resultDatas, '');

        $latitude =  CommonRequest::get($request, 'latitude'); // '34.32932';
        $longitude = CommonRequest::get($request, 'longitude'); // '108.70929';//

        $city_site_id = CommonRequest::getInt($request, 'city_site_id');
        // 查询
           // 关键字
        $keyWord = CommonRequest::get($request, 'keyword');
        // 关键字不为空，则查询包含此关键字的商品
        $shop_ids = [];// 店铺id数组 ---一维
        if(!empty($keyWord)){
            $shop_ids = CTAPIShopBusiness::getShopIdsByKeyWord($request, $this, $city_site_id, $keyWord, '1', '1', '1,2', 0);
        }
        // 排序 orderType 1综合排序[默认] 2 销量排序 4 最近
        $orderType = CommonRequest::getInt($request, 'orderType');
        if($orderType == 4 && (empty($latitude) || empty($longitude)) ) $orderType = 2;
        if(empty($orderType) || (!in_array($orderType, [1,2,4]))) $orderType = 1;
        $defaultRelations = ['province', 'city', 'area', 'shopCity', 'shopCityPartner', 'shopSeller', 'shopType', 'labels', 'siteResources'];
        $oprateBit = 2 + 4;
        $relations = $defaultRelations;
        $extParams = [
            'useQueryParams' => true,// '是否用来拼接查询条件，true:用[默认];false：不用',
            'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
                //'where' => '如果有值，则替换where',
                //'select' => '如果有值，则替换select',
                //'orderBy' => '如果有值，则替换orderBy',
                //'whereIn' => '如果有值，则替换whereIn',
                //'whereNotIn' => '如果有值，则替换whereNotIn',
                //'whereBetween' => '如果有值，则替换whereBetween',
                //'whereNotBetween' => '如果有值，则替换whereNotBetween',
            ],
        ];
        switch($orderType){
            case 4:// 最近的店铺 ，
                //方法: 优化：对排好序的所有数据进行缓存，时间暂定 2 分钟
                //   1获得所有数据[不要记录关系]，只查id和位置相关、要参加排序的字段:
                //   2对获得的数据进行排序。--缓存
                //   3获取要获取页的数据，并只取 id。
                //   4根据id去获取需要的数据,包括 记录关系 --可缓存
                //   5返回数据
                $oprateBit = 1;
                $relations = [];
                $extParams['sqlParams']['select'] = ['id','longitude','latitude', 'status_business', 'mon_sales_volume', 'sort_num'];
                $extParams['sqlParams']['orderBy'] = [];
                break;
            case 2:// 销量排序 月销量 -最近30天
                $extParams['sqlParams']['orderBy'] = ['status_business'=>'asc', 'mon_sales_volume'=>'desc', 'sort_num'=>'desc', 'id'=>'desc'];
                break;
            case 1://1综合排序[默认]
            default:
                $extParams['sqlParams']['orderBy'] = ['status_business'=>'asc', 'sort_num' => 'desc', 'mon_sales_volume'=>'desc', 'id'=>'desc'];
                break;
        }

        //  显示到定位点的距离
        CTAPIShopBusiness::mergeRequest($request, $this, [
            'status' => 1,// 状态0待审核1审核通过2审核未通过4冻结(禁用)
            'status_business' => 1 + 2 ,// 经营状态  1营业中 2 歇业中 4 停业[店铺人工操作 8  关业[店铺平台操作]
            'field' => '',// 'shop_name',// 关键词查询时的字段
            'keyword' => '',
            'ids' => implode(',', $shop_ids),
        ]);

        $result = CTAPIShopBusiness::getList($request, $this, $oprateBit, [], $relations, $extParams);

        $data_list = $result['result']['data_list'] ?? [];
        if(!empty($data_list)) Map::resolveDistance($data_list, $latitude, $longitude, 'distance', 400, '', 'latitude', 'longitude', '');
        if($orderType == 4 && !empty($data_list)){// 最近
            //   2对获得的数据进行排序。--缓存
            $orderDistance = [
                ['key' => 'status_business', 'sort' => 'asc', 'type' => 'numeric'],
                ['key' => 'distance', 'sort' => 'asc', 'type' => 'numeric'],
                ['key' => 'mon_sales_volume', 'sort' => 'desc', 'type' => 'numeric'],
                ['key' => 'sort_num', 'sort' => 'desc', 'type' => 'numeric'],
                ['key' => 'id', 'sort' => 'desc', 'type' => 'numeric'],
            ];
            $data_list = Tool::php_multisort($data_list, $orderDistance);
            $data_list = array_values($data_list);
            // 获得翻页的三个关键参数
            /*
            翻页的三个关键参数
            [
                'page' => $page,// 当前页,如果不正确默认第一页
                'pagesize' => $pagesize,// 每页显示数量,取值1 -- 100 条之间,默认15条
                'total' => $total,// 总记录数,优化方案：传0传重新获取总数，如果传了，则不会再获取，而是用传的，减软数据库压力;=-5:只统计条件记录数量，不返回数据
            ]
             */
            $pageParams = CommonRequest::getPageParams($request);
            list($page, $pagesize, $total) = array_values($pageParams);
            $has_page = true;
            $total = count($data_list);
            //   3获取要获取页的数据，并只取 id。
            $firstUbound = ($page - 1) * $pagesize;
            $temDataList = [];
            for($k = $firstUbound; $k < $pagesize;$k++){
                if(!isset($data_list[$k])){
                    $has_page = false;
                    break;
                }
                array_push($temDataList, $data_list[$k]);
            }
            //if(!empty($temDataList)){
                $idsArr = array_column($temDataList,'id');
                $ids = implode(',', $idsArr);
                //   4根据id去获取需要的数据,包括 记录关系 --可缓存
                CTAPIShopBusiness::mergeRequest($request, $this, [
                    'ids' => $ids,
                ]);

                if(isset($extParams['sqlParams']['select'])) unset($extParams['sqlParams']['select']);

                $temResult = CTAPIShopBusiness::getList($request, $this, 1, [], $defaultRelations, $extParams);
                $temDataList = $temResult['result']['data_list'] ?? [];
                if(!empty($temDataList)) Map::resolveDistance($temDataList, $latitude, $longitude, 'distance', 400, '', 'latitude', 'longitude', '');
                $temDataList = Tool::php_multisort($temDataList, $orderDistance);
                $temDataList = array_values($temDataList);
            //}

            $data_list = $temDataList;

            $totalPage = ceil($total/$pagesize);
            $result['result']['has_page'] = $has_page;
            $result['result']['total'] = $total;
            $result['result']['page'] = $page;
            $result['result']['pagesize'] = $pagesize;
            $result['result']['totalPage'] = $totalPage;
            $result['result']['pageInfo'] = '';// showPage($totalPage,$page,$total,12,1);
        }

        foreach($data_list as $k => $v){
            $data_list[$k]['resource_url'] = $v['resource_list'][0]['resource_url'] ?? '';
            // if(isset($v['resource_list']))  unset($data_list[$k]['resource_list']);
        }
        $data_list = array_values($data_list);

        $data_list = Tool::formatTwoArrKeys($data_list, Tool::arrEqualKeyVal(['id', 'resource_url', 'shop_name', 'type_name'
            , 'mon_sales_volume', 'per_price'
            , 'area_name', 'addr', 'distanceStr', 'status_business_text', 'status_business', 'labelNnames'
            , 'labelIdKV'
//            , 'aaaa', 'aaaa', 'aaaa', 'aaaa', 'aaaa'
//            , 'aaaa', 'aaaa', 'aaaa', 'aaaa', 'aaaa', 'aaaa'
                ]), false);
        $result['result']['data_list'] = $data_list;

        return $result;
    }

    // ajax获得详情数据
    public function ajax_info(Request $request,$id = 0){
        // $this->InitParams($request);
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');

        $latitude =  CommonRequest::get($request, 'latitude'); // '34.32932';
        $longitude = CommonRequest::get($request, 'longitude'); // '108.70929';//

        $info = CTAPIShopBusiness::getInfoData($request, $this, $id, [], ['shopSeller', 'labels', 'siteResources', 'shopType']);// , ['city']
        $info['resource_url'] = $info['resource_list'][0]['resource_url'] ?? '';
        // if(isset($info['resource_list']))  unset($info['resource_list']);

        if(!empty($info)) Map::resolveDistanceSingle($info, $latitude, $longitude, 'distance', 400, '', 'latitude', 'longitude', '');
        return ajaxDataArr(1, $info, '');
    }
}
