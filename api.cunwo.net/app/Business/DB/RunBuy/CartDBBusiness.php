<?php
// 购物车
namespace App\Business\DB\RunBuy;


use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class CartDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Cart';
    public static $table_name = 'cart';// 表名称

    /**
     * 如果id<=0 ；则必须有 参数 staff_id  city_site_id  goods_id  prop_price_id
     * id > 0; 则必须有 参数 id staff_id  city_site_id  goods_id  prop_price_id
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组  必要参数 ower_type , ower_id
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['staff_id']) && empty($saveData['staff_id'])  ){
            throws('用户id不能为空！');
        }

        if(isset($saveData['city_site_id']) && empty($saveData['city_site_id'])  ){
            throws('城市id不能为空！');
        }

//        if(isset($saveData['shop_id']) && empty($saveData['shop_id'])  ){
//            throws('店铺id不能为空！');
//        }

        if(isset($saveData['goods_id']) && empty($saveData['goods_id'])  ){
            throws('商品id不能为空！');
        }

        if(isset($saveData['prop_price_id']) && !is_numeric($saveData['prop_price_id'])  ){
            throws('价格属性id不能为空！');
        }

        if(isset($saveData['amount']) && !is_numeric($saveData['amount']) ){
            throws('商品数量不能为空！');
        }

        DB::beginTransaction();
        $amount = $saveData['amount'];
        if(!is_numeric($amount)) $amount = 0;

        try {
            $queryParams = [
                'where' => [
                    ['staff_id', $saveData['staff_id']],
                    ['city_site_id', $saveData['city_site_id']],
                    ['goods_id', $saveData['goods_id']],
                    ['prop_price_id', $saveData['prop_price_id'] ],
                ],
//            'select' => [
//                'id','title','sort_num','volume'
//                ,'operate_staff_id','operate_staff_id_history'
//                ,'created_at' ,'updated_at'
//            ],
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            if($id <= 0){
                $queryParams['select'] = ['id'];
                // 查询记录
                $info = static::getInfoByQuery(1, $queryParams, []);
                $id = $info['id'] ?? 0;
            }
            if($amount <= 0){// 删除记录
                if($id > 0){
//                    if(isset($queryParams['select'])) unset($queryParams['select']);
//                    static::del($queryParams);
                    // $id = 0;
                    // 删除属性表记录
                    $propQueryParams = [
                        'where' => [
                            ['cart_id', $id],
                        ],
                     ];
                    CartGoodsPropsDBBusiness::del($propQueryParams);
                    // 删除主表记录
                    static::deleteByIds($id);
                }
            }else{
                $isModify = false;
                $operate_staff_id_history = 0;
                if($id > 0){
                    $isModify = true;
                    // 判断权限
                    //            $judgeData = [
                    //                'company_id' => $company_id,
                    //            ];
                    //            $relations = '';
                    //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                    if($modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history);

                }else {// 新加;要加入的特别字段
                    // 根据商品id,获得相关信息
                    $goods_id = $saveData['goods_id'];
                    $shopInfo = ShopGoodsDBBusiness::getInfo($goods_id, ['city_site_id', 'city_partner_id', 'seller_id', 'shop_id', 'price_type', 'price']);
                    if(empty($shopInfo)) throws('商品信息不存在');
                    $saveData = array_merge($saveData,[
                        'city_site_id' => $shopInfo['city_site_id']
                        , 'city_partner_id' => $shopInfo['city_partner_id']
                        , 'seller_id' => $shopInfo['seller_id']
                        , 'shop_id' => $shopInfo['shop_id']
                    ]);
                    if($shopInfo['price_type'] == 1){
                        $saveData['prop_price_id'] = 0;
                        $saveData['price'] = $shopInfo['price'];
                    }else{
                        $prop_price_id = $saveData['prop_price_id'];
                        $goodPriceInfo = ShopGoodsPricesDBBusiness::getInfo($prop_price_id, ['price']);
                        if(empty($goodPriceInfo)) throws('商品价格属性不存在');
                        $saveData['price'] = $goodPriceInfo['price'];
                    }
                    //            $addNewData = [
                    //                'company_id' => $company_id,
                    //            ];
                    //            $saveData = array_merge($saveData, $addNewData);
                    // 加入操作人员信息
                    static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history);
                }

                // 新加或修改
                if($id <= 0){// 新加
                    $resultDatas = static::create($saveData);
                    $id = $resultDatas['id'] ?? 0;
                }else{// 修改
                    $modelObj = null;
                    $saveBoolen = static::saveById($saveData, $id,$modelObj);
                    // $resultDatas = static::getInfo($id);
                    // 修改数据，是否当前版本号 + 1
                    // static::compareHistory($id, 1);
                }
                if($isModify){
                    // static::compareHistory($id, 1);
                }
            }

        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
             throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

    /**
     * 如果shop_id <=0 ；则为清空购物车，必须有 参数 staff_id  city_site_id
     * 如果shop_id > 0; 则为清空指定的店铺，必须有 参数 id staff_id  city_site_id
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组  必要参数 ower_type , ower_id
     * @param int  $company_id 企业id
     * @param int $shop_id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function delByShopId($saveData, $company_id, &$shop_id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['staff_id']) && empty($saveData['staff_id'])  ){
            throws('用户id不能为空！');
        }

        if(isset($saveData['city_site_id']) && empty($saveData['city_site_id'])  ){
            throws('城市id不能为空！');
        }

        DB::beginTransaction();

        try {
            $queryParams = [
                'where' => [
                    ['staff_id', $saveData['staff_id']],
                    ['city_site_id', $saveData['city_site_id']],
                ],
//            'select' => [
//                'id','title','sort_num','volume'
//                ,'operate_staff_id','operate_staff_id_history'
//                ,'created_at' ,'updated_at'
//            ],
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            $queryParams['select'] = ['id'];
            if($shop_id > 0 )  array_push($queryParams['where'], ['shop_id', '=', $shop_id]);
            $cartList = static::getAllList($queryParams, '')->toArray();
            if( !empty($cartList) ){
                $cartIdsArr = array_column($cartList,'id');

                // 删除属性表记录
                $propQueryParams = [
                    'whereIn' => [
                        'cart_id' => $cartIdsArr,
                    ]
                ];
                CartGoodsPropsDBBusiness::del($propQueryParams);
                // 删除主表记录
                static::deleteByIds(implode(',',$cartIdsArr));
            }
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
             throws($e->getMessage());
        }
        DB::commit();
        return $shop_id;
    }

    /**
     * 如果 good_prop_table_id 0 ；则为清空当前属性及值，必须有 参数 staff_id  city_site_id cart_id
     * 如果 good_prop_table_id 多个逗号分隔; 则为设置为指定的属性值，必须有 参数 id staff_id  city_site_id  cart_id
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组  必要参数 ower_type , ower_id
     * @param int  $company_id 企业id
     * @param int $prop_id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function saveProps($saveData, $company_id, &$prop_id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['staff_id']) && empty($saveData['staff_id'])  ){
            throws('用户id不能为空！');
        }

        if(isset($saveData['city_site_id']) && empty($saveData['city_site_id'])  ){
            throws('城市id不能为空！');
        }

        if (isset($saveData['cart_id']) && empty($saveData['cart_id'])) {
            throws('购物车编号不能为空！');
        }

        $good_prop_table_id = $saveData['good_prop_table_id'] ?? 0;


        DB::beginTransaction();

        try {
            if($good_prop_table_id == 0 || $good_prop_table_id == '0'){// 清除所有属性下的，属性值
                $delParams = [
                    'where' => [
                        ['cart_id', $saveData['cart_id']],
                        ['prop_id', $prop_id],
                    ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
                CartGoodsPropsDBBusiness::del($delParams);
            }else{
                // 获得购物车记录
                $cartInfo = static::getInfo($saveData['cart_id'], ['goods_id']);
                if(empty($cartInfo)) throws('购物车中商品记录不存在');
                $goods_id = $cartInfo['goods_id'];

                // 获得所有的商品属性值表-- 前端给过来的
                $queryParams = [
                    'where' => [
                        ['goods_id', $goods_id],
                        ['prop_id', $prop_id],
                    ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
                if (strpos($good_prop_table_id, ',') === false) { // 单条
                    array_push($queryParams['where'], ['id', $good_prop_table_id]);
                } else {
                    $queryParams['whereIn']['id'] = explode(',', $good_prop_table_id);
                }
                $goodPropsList = ShopGoodsPropsDBBusiness::getAllList($queryParams, '')->toArray();

                // 获得当前已有的属性值
                $queryParams = [
                    'where' => [
                        ['cart_id', $saveData['cart_id']],
                        ['prop_id', $prop_id],
                    ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                ];
//                if (strpos($good_prop_table_id, ',') === false) { // 单条
//                    array_push($queryParams['where'], ['goods_props_id', $good_prop_table_id]);
//                } else {
//                    $queryParams['whereIn']['goods_props_id'] = explode(',', $good_prop_table_id);
//                }
                $cartPropsList = CartGoodsPropsDBBusiness::getAllList($queryParams, '')->toArray();
                $hasCartGoodPropTableIds = array_column($cartPropsList,'goods_props_id');

                $addCartProps = [];// 要新加的商品属性
                $needOldGoodPropTableIds = [];// 表中已有的商品属性
                // 遍历商品属性
                $operate_staff_id_history = 0;
                foreach($goodPropsList as $k => $v){
                    $tem_goods_props_id = $v['id'];
                    $temSaveData = [
                        'cart_id' => $saveData['cart_id'],
                        'goods_props_id' => $tem_goods_props_id,
                        'prop_id' => $v['prop_id'],
                        'prop_val_id' => $v['prop_val_id'],
                        'prop_names_id' => $v['prop_names_id'],
                        'prop_val_names_id' => $v['prop_val_names_id'],
                        'operate_staff_id' => $operate_staff_id,
                        // 'operate_staff_id_history' => $v['aaa'],
                    ];
                    if(in_array($tem_goods_props_id, $hasCartGoodPropTableIds)){ // 已经存在
                        // 更新
                        $saveQueryParams = [
                            'where' => [
                                ['cart_id', $saveData['cart_id']],
                                ['prop_id', $prop_id],
                                ['goods_props_id', $tem_goods_props_id],
                            ],
//                            'select' => [
//                                'id','title','sort_num','volume'
//                                ,'operate_staff_id','operate_staff_id_history'
//                                ,'created_at' ,'updated_at'
//                            ],
                            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                        ];
                        CartGoodsPropsDBBusiness::save($temSaveData, $saveQueryParams);
                        array_push($needOldGoodPropTableIds, $tem_goods_props_id);
                        continue;
                    }
                    static::addOprate($temSaveData, $operate_staff_id,$operate_staff_id_history);
                    array_push($addCartProps, $temSaveData);
                }
                if(!empty($addCartProps))  CartGoodsPropsDBBusiness::addBath($addCartProps);// 批量添加
                // 删除多余记录
                $delCartGoodPropTableIDS = array_diff($hasCartGoodPropTableIds, $needOldGoodPropTableIds);
                if(!empty($delCartGoodPropTableIDS)){
                    $delParams = [
                        'where' => [
                            ['cart_id', $saveData['cart_id']],
                            ['prop_id', $prop_id],
                        ],
//                    'select' => [
//                        'id','title','sort_num','volume'
//                        ,'operate_staff_id','operate_staff_id_history'
//                        ,'created_at' ,'updated_at'
//                    ],
                        //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                    ];
                    $delParams['whereIn']['goods_props_id'] = $delCartGoodPropTableIDS;
                    CartGoodsPropsDBBusiness::del($delParams);
                }

            }
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
             throws($e->getMessage());
        }
        DB::commit();
        return $prop_id;
    }


    /**
     * 根据购物车生成订单,返回生成的订单号
     *
     * @param array $saveData 要保存或修改的数组  必要参数 ower_type , ower_id
     * @param int  $company_id 企业id
     * @param string $cartIds 购物车id,多个用逗号分隔
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function createOrder($saveData, $company_id, $cartIds, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['staff_id']) && empty($saveData['staff_id'])  ){
            throws('用户id不能为空！');
        }
        $staff_id = $saveData['staff_id'] ?? 0;
        if(!is_numeric($staff_id) || $staff_id <= 0) throws('用户id不能为空！');


        if(isset($saveData['city_site_id']) && empty($saveData['city_site_id'])  ){
            throws('城市id不能为空！');
        }
        $city_site_id = $saveData['city_site_id'] ?? 0;
        if(!is_numeric($city_site_id) || $city_site_id <= 0) throws('城市id不能为空！');

        $tableware = $saveData['tableware'] ?? 0;
        if(!is_numeric($tableware) || $tableware < 0) throws('需要的餐具数不能为空！');

        $second_num = $saveData['second_num'] ?? 0;
        if(!is_numeric($second_num) || $second_num <= 0) throws('送货速度不能为空！');

        $total_run_price = $saveData['total_run_price'] ?? 0;
        if(!is_numeric($total_run_price) || $total_run_price <= 0) throws('总跑腿费不能为空！');

        $addr_id = $saveData['addr_id'] ?? 0;
        if(!is_numeric($addr_id) || $addr_id <= 0) throws('收货地址信息不能为空！');


        $remarks = $saveData['remarks'] ?? '';// 买家备注

        if(empty($cartIds)) throws('购物车id不能为空！');

        // $cart_ids = explode(',', $cartIds);
        // 判断收货人地址
        $addrInfo = CommonAddrDBBusiness::getInfo($addr_id, ['id']);
        if(empty($addrInfo)) throws('收货地址信息记录不存在');

        // 查询购物车数据
        $queryParams = [
            'where' => [
                 ['staff_id', '=' , $staff_id],
                ['city_site_id', '=' , $city_site_id],
                // ['main_name', 'like', '' . $main_name . '%'],
                // ['id', '&' , '16=16'],
            ],
            //'select' => [
            //   'id', 'city_site_id', 'city_partner_id', 'seller_id', 'names_id', 'sort_num'
            //],
//             'orderBy' => [ 'id'=>'desc'],// 'sort_num'=>'desc',
        ];
        if (strpos($cartIds, ',') === false) { // 单条
            array_push($queryParams['where'], ['id', $cartIds]);
        } else {
            $queryParams['whereIn']['id'] = explode(',', $cartIds);
        }

        $cartList = static::getList($queryParams, [
            'props.prop.name'// 购物车属性的属性
            ,'props.propVal.name'// 购物车属性的属性值
            ,'goodsPrice.prop.name'// 购物车价格属性
            ,'goodsPrice.propVal.name'// 购物车价格属性

            ,'shopSeller'// 商家
            ,'shop'// 店铺
            ,'goods.siteResources'// 商品

            ,'goods.priceProps.prop.name'// 商品价格属性
            ,'goods.priceProps.propVal.name'// 商品价格属性值
            ,'goods.props.prop.name'// 商品属性
            ,'goods.props.propVal.name'// 商品属性值
        ])->toArray();

//         pr($cartList);
        $formatCartList = [];
        // 判断商家是否存在，是否通过审核
        // 判断店铺是否存在，是否通过审核，经营状态
        // 判断商品是否存在，是否上架
        // 判断价格属性是否存存，价格属性值是否存在
        // 判断属性是否存存，属性值是否存在
        $cartList = array_values(array_reverse($cartList));// 反转数组
        foreach($cartList as $k => $cartInfo){
            $cart_seller_id = $cartInfo['seller_id'];// 商家ID
            $cart_shop_id = $cartInfo['shop_id'];// 店铺ID
            $cart_goods_id = $cartInfo['goods_id'];// 商品ID
            $cart_prop_price_id = $cartInfo['prop_price_id'];// 商品价格ID
            $cart_amount = $cartInfo['amount'];// 数量
            $cartProps = $cartInfo['props'] ?? [];// 购物车属性及值
            if(isset($cartInfo['props'])) unset($cartInfo['props']);
            $cartPriceProps = $cartInfo['goods_price'] ?? [];//购物车价格属性, 可能为空
            if(isset($cartInfo['goods_price'])) unset($cartInfo['goods_price']);

            $cartShopSeller = $cartInfo['shop_seller'] ?? [];// 判断商家是否存在，是否通过审核
            if(isset($cartInfo['shop_seller'])) unset($cartInfo['shop_seller']);
            $cartShop = $cartInfo['shop'] ?? [];// 判断店铺是否存在，是否通过审核，经营状态
            if(isset($cartInfo['shop'])) unset($cartInfo['shop']);

            // 店铺不存在
            if(empty($cartShop)) throws('店铺ID[' . $cart_shop_id . ']不存在，请移除店铺的产品，再提交。');
            // 店铺未通过审核
            if($cartShop['status'] != 1) throws('店铺[' . $cartShop['shop_name'] . ']未通过审核，请移除店铺的产品，再提交。');
            // 店铺经营状态  1营业中 2 歇业中 4 停业[店铺人工操作 8  关业[店铺平台操作]
            if($cartShop['status_business'] != 1) throws('店铺[' . $cartShop['shop_name'] . ']当前时间非营业中，请移除店铺的产品，再提交。');

            // 商家不存在
            if(empty($cartShopSeller)) throws('店铺[' . $cartShop['shop_name'] . '所属商家ID[' . $cart_seller_id . ']不存在，请移除店铺的产品，再提交。');
            // 商家未通过审核
            if($cartShopSeller['status'] != 1) throws('店铺[' . $cartShop['shop_name'] . '所属商家ID[' . $cart_seller_id . ']未通过审核，请移除店铺的产品，再提交。');

            $cartGoods = $cartInfo['goods'] ?? [];// 商品  否存在，是否上架
            if(isset($cartInfo['goods'])) unset($cartInfo['goods']);

            // 商品不存在
            if(empty($cartGoods)) throws('店铺[' . $cartShop['shop_name'] . '商品ID[' . $cart_goods_id . ']不存在，请移除店铺的产品，再提交。');
            // 商品已下架
            if($cartGoods['is_sale'] != 1) throws('店铺[' . $cartShop['shop_name'] . '的商品[' . $cartGoods['goods_name'] . ']未上架，请移除店铺的产品，再提交。');

            // 商品图片
            $site_resources = $cartGoods['site_resources'] ?? [];
            $tem_resource_id = 0;
            if(!empty($site_resources)){
                $tem_resource_id = $site_resources[0]['id'];
            }
            $cartInfo['resource_id'] = $tem_resource_id;

            $shopGoodName = '店铺[' . $cartShop['shop_name'] . '商品[' . $cartGoods['goods_name'] . ']';
            $cartGoodsPriceProps = $cartGoods['price_props'] ?? [];// 商品价格属性
            $formatCartPriceProps = [];// 购物车商品价格属性格式化 [属性id=>[...,'pv_ids'=> ['prop_id' => [],......]]]
            foreach($cartGoodsPriceProps as $price_k => $price_v){
                $temPriceProp = $price_v['prop'] ?? [];
                $temPricePropVal = $price_v['prop_val'] ?? [];
                // 如果价格属性或价格属性值不存在，则删除当前价格属性---价格属性已经去掉了
                if(empty($temPriceProp) || empty($temPricePropVal)) unset($cartGoodsPriceProps[$price_k]);
                $cartGoodsPriceProps[$price_k]['prop_names_id'] = $temPriceProp['names_id'];
                $cartGoodsPriceProps[$price_k]['prop_name'] = $temPriceProp['name']['main_name'];
                $cartGoodsPriceProps[$price_k]['prop_val_names_id'] = $temPricePropVal['names_id'];
                $cartGoodsPriceProps[$price_k]['prop_val_name'] = $temPricePropVal['name']['main_name'];
                if(!isset($formatCartPriceProps[$price_v['prop_id']]))  $formatCartPriceProps[$price_v['prop_id']] = [
                    // 'id' => $price_v['id'],
                    'city_site_id' => $price_v['city_site_id'],
                    'city_partner_id' => $price_v['city_partner_id'],
                    'seller_id' => $price_v['seller_id'],
                    'shop_id' => $price_v['shop_id'],
                    'goods_id' => $price_v['goods_id'],
                    'prop_id' => $price_v['prop_id'],
                    'prop_name' => $temPriceProp['name']['main_name'],
                    'prop_names_id' => $temPriceProp['names_id'],// $prop_v['prop_names_id'],
                ];
                $formatCartPriceProps[$price_v['prop_id']]['pv_ids'][$price_v['prop_val_id']] = [
                    'prop_val_table_id' => $price_v['id'],
                    'prop_val_id' => $price_v['prop_val_id'],
                    'prop_val_names_id' => $temPricePropVal['names_id'],// $prop_v['prop_val_names_id'],
                    'prop_val_name' => $temPricePropVal['name']['main_name'],
                    'version_num' => $price_v['version_num'],
                    'sort_num' => $price_v['sort_num'],
                ];
                if(isset($cartGoodsPriceProps[$price_k]['prop'])) unset($cartGoodsPriceProps[$price_k]['prop']);
                if(isset($cartGoodsPriceProps[$price_k]['prop_val'])) unset($cartGoodsPriceProps[$price_k]['prop_val']);
            }

            // pr($cartGoodsProps);

            $cartGoodsProps = $cartGoods['props'] ?? [];// 商品属性
            $formatCartProps = [];// 购物车商品属性格式化 [属性]
            foreach($cartGoodsProps as $prop_k => $prop_v){
                $temProp = $prop_v['prop'] ?? [];
                $temPropVal = $prop_v['prop_val'] ?? [];
                // 如果属性或属性值不存在，则删除当前属性---属性已经去掉了
                if(empty($temProp) || empty($temPropVal) ) unset($cartGoodsProps[$prop_k]);
                $cartGoodsProps[$prop_k]['prop_names_id'] = $temProp['names_id'];
                $cartGoodsProps[$prop_k]['prop_name'] = $temProp['name']['main_name'];
                $cartGoodsProps[$prop_k]['prop_val_names_id'] = $temPropVal['names_id'];
                $cartGoodsProps[$prop_k]['prop_val_name'] = $temPropVal['name']['main_name'];

                if(!isset($formatCartProps[$prop_v['prop_id']]))  $formatCartProps[$prop_v['prop_id']] = [
                    'city_site_id' => $prop_v['city_site_id'],
                    'city_partner_id' => $prop_v['city_partner_id'],
                    'seller_id' => $prop_v['seller_id'],
                    'shop_id' => $prop_v['shop_id'],
                    'goods_id' => $prop_v['goods_id'],
                    'is_multi' => $prop_v['is_multi'],
                    'is_must' => $prop_v['is_must'],
                    'prop_id' => $prop_v['prop_id'],
                    'prop_names_id' => $temProp['names_id'],// $prop_v['prop_names_id'],
                    'prop_name' => $temProp['name']['main_name'],
                ];
                $formatCartProps[$prop_v['prop_id']]['pv_ids'][$prop_v['prop_val_id']] = [
                    'prop_val_table_id' => $prop_v['id'],
                    'prop_val_id' => $prop_v['prop_val_id'],
                    'prop_val_names_id' => $temPropVal['names_id'],// $prop_v['prop_val_names_id'],
                    'prop_val_name' => $temPropVal['name']['main_name'],
                    'version_num' => $prop_v['version_num'],
                    'sort_num' => $prop_v['sort_num'],
                ];
                if(isset($cartGoodsProps[$prop_k]['prop'])) unset($cartGoodsProps[$prop_k]['prop']);
                if(isset($cartGoodsProps[$prop_k]['prop_val'])) unset($cartGoodsProps[$prop_k]['prop_val']);

            }

             // pr($formatCartProps);

            // 整理购物车属性
            $formatGProps = [];
            foreach($cartProps as $cart_p_k => $cart_p_v){
                $tem_cart_prop = $cart_p_v['prop']  ?? [];
                $tem_cart_prop_val = $cart_p_v['prop_val']  ?? [];
                if(empty($tem_cart_prop) || empty($tem_cart_prop_val)) unset($cartProps[$cart_p_k]);
                $cartProps[$cart_p_k]['prop_names_id'] = $tem_cart_prop['names_id'];
                $cartProps[$cart_p_k]['prop_name'] = $tem_cart_prop['name']['main_name'];
                $cartProps[$cart_p_k]['prop_val_names_id'] = $tem_cart_prop_val['names_id'];
                $cartProps[$cart_p_k]['prop_val_name'] = $tem_cart_prop_val['name']['main_name'];

                if(!isset($formatGProps[$cart_p_v['prop_id']]))  $formatGProps[$cart_p_v['prop_id']] = [
                    'prop_id' => $cart_p_v['prop_id'],
                    'cart_id' => $cart_p_v['cart_id'],
                    'prop_names_id' => $tem_cart_prop['names_id'],
                    'prop_name' => $tem_cart_prop['name']['main_name'],
                ];
                $formatGProps[$cart_p_v['prop_id']]['pv_ids'][$cart_p_v['prop_val_id']] = [
                    'cart_prop_table_id' => $cart_p_v['id'],
                    'goods_props_id' => $cart_p_v['goods_props_id'],
                    'prop_val_id' => $cart_p_v['prop_val_id'],
                    'prop_val_names_id' => $tem_cart_prop_val['names_id'],
                    'prop_val_name' => $tem_cart_prop_val['name']['main_name'],
                ];
                if(isset($cartProps[$cart_p_k]['prop'])) unset($cartProps[$cart_p_k]['prop']);
                if(isset($cartProps[$cart_p_k]['prop_val'])) unset($cartProps[$cart_p_k]['prop_val']);
            }

            $cartInfo['props'] = $cartProps;
            // pr($formatGProps);

            // 购物车价格属性
            if(!empty($cartPriceProps)){
                $t_price_prop = $cartPriceProps['prop'] ?? [];
                $t_price_prop_val = $cartPriceProps['prop_val'] ?? [];
                // 如果价格属性或价格属性值不存在，则删除当前价格属性---价格属性已经去掉了
                if(empty($t_price_prop) || empty($t_price_prop_val)) $cartPriceProps = [];
                $cartPriceProps['prop_names_id'] = $t_price_prop['names_id'];
                $cartPriceProps['prop_name'] = $t_price_prop['name']['main_name'];
                $cartPriceProps['prop_val_names_id'] = $t_price_prop_val['names_id'];
                $cartPriceProps['prop_val_name'] = $t_price_prop_val['name']['main_name'];
                if(isset($cartPriceProps['prop'])) unset($cartPriceProps['prop']);
                if(isset($cartPriceProps['prop_val'])) unset($cartPriceProps['prop_val']);

            }
            // $cartInfo['goods_price'] = $cartPriceProps;

            // 判断价格属性
            if($cart_prop_price_id > 0){// 价格属性
                if(empty($cartPriceProps)) throws($shopGoodName . '价格属性表ID[' . $cart_prop_price_id . ']不存在。');

                $temGoodsPriceTBIds = array_column($cartGoodsPriceProps, 'id');
                $tem_prop_val_name = $cartPriceProps['prop_val_name'] ?? '';
                if(!in_array($cart_prop_price_id, $temGoodsPriceTBIds)) throws($shopGoodName . '价格属性[' . $tem_prop_val_name . ']属性或属性值不存在。');
                $price = $cartPriceProps['price'];
            }else{
                $price = $cartGoods['price'];
            }
            $cartInfo['price'] = $price;

            // 遍历商品属性---判断属性
            foreach($formatCartProps as $gp_k => $gp_v){
                $is_must = $gp_v['is_must'] ?? 0  ;// 是否必选[下单时]0不是1是
                if($is_must != 1) continue;
                $gp_prop_id = $gp_v['prop_id'] ?? 0;// 属性id
                $gp_prop_name = $gp_v['prop_name'] ?? 0;// 属性名称
                $gp_prop_val_idarr = $gp_v['pv_ids'] ?? [];// 属性值id数组
                if(!isset($formatGProps[$gp_prop_id])) throws('请选择' . $shopGoodName . '属性[' . $gp_prop_name . ']！');
                // 遍历属性值
                foreach($formatGProps[$gp_prop_id]['pv_ids'] as $t_vk => $t_vv){
                     if(!isset($gp_prop_val_idarr[$t_vv['prop_val_id']]))  throws('' . $shopGoodName . '属性[' . $gp_prop_name . '属性值[' . $t_vv['prop_val_name'] . ']不存在！');
                }
            }
            if(!isset($formatCartList[$cart_shop_id])) $formatCartList[$cart_shop_id] = [
                'city_site_id' => $cartShop['city_site_id'] ,// 城市分站id
                'city_partner_id' => $cartShop['city_partner_id'],// 城市合伙人id
                'seller_id' => $cartShop['seller_id'],// 商家ID
                'shop_id' => $cartShop['id'],// 店铺ID
                'province_id' => $cartShop['province_id'],// 省id
                'city_id' => $cartShop['city_id'],// 市id
                'area_id' => $cartShop['area_id'],// 区id
            ];
            $formatCartList[$cart_shop_id]['cart_list'][] = $cartInfo;

        }
        $formatCartList = array_values($formatCartList);
        // pr($formatCartList);
        $shopCount = count($formatCartList);
        if($shopCount <= 0) throws('没有可生成订单的商品');

        DB::beginTransaction();
        try {
            // 获得操作人历史
            $operate_staff_id_history = 0;
            $operateData = [
                'operate_staff_id' => $operate_staff_id,
            ];
            static::addOprate($operateData, $operate_staff_id,$operate_staff_id_history);
            // 用户历史
            $staff_id_history = 0;
            $staffData = [
                'operate_staff_id' => $staff_id,
            ];
            static::addOprate($staffData, $staff_id,$staff_id_history);

            $cacheData = [];
            $parent_order_no = '';// 父订单号
            $expiry_time =  Carbon::now()->addMinute($second_num);

            $orderBase = $formatCartList[0] ?? [];

            $city_site_id = $orderBase['city_site_id'] ?? 0;// 城市分站id
            if(isset($cacheData['city_site_id'][$city_site_id]) && $cacheData['city_site_id'][$city_site_id] > 0){
                $city_site_id_history = $cacheData['city_site_id'][$city_site_id];
            }else{
                $city_site_id_history = ($city_site_id > 0) ? CityDBBusiness::getIdHistory($city_site_id) : 0;// 城市分站历史id
                $cacheData['city_site_id'][$city_site_id] = $city_site_id_history;
            }

            $city_partner_id = $orderBase['city_partner_id'] ?? 0;// 城市合伙人id
            if(isset($cacheData['city_partner_id'][$city_partner_id]) && $cacheData['city_partner_id'][$city_partner_id] > 0){
                $city_partner_id_history = $cacheData['city_partner_id'][$city_partner_id];
            }else{
                $city_partner_id_history = ($city_partner_id > 0) ? CityPartnerDBBusiness::getIdHistory($city_partner_id) : 0;// 城市合伙人历史id
                $cacheData['city_partner_id'][$city_partner_id] = $city_partner_id_history;
            }

            $seller_id = $orderBase['seller_id'] ?? 0;// 商家ID
            if(isset($cacheData['seller_id'][$seller_id]) && $cacheData['seller_id'][$seller_id] > 0){
                $seller_id_history = $cacheData['seller_id'][$seller_id];
            }else{
                $seller_id_history = ($seller_id > 0) ? SellerDBBusiness::getIdHistory($seller_id) : 0;// 商家历史ID
                $cacheData['seller_id'][$seller_id] = $seller_id_history;
            }

            $shop_id = $orderBase['shop_id'] ?? 0;// 店铺ID
            if(isset($cacheData['shop_id'][$shop_id]) && $cacheData['shop_id'][$shop_id] > 0){
                $shop_id_history = $cacheData['shop_id'][$shop_id];
            }else{
                $shop_id_history = ($shop_id > 0) ? ShopDBBusiness::getIdHistory($shop_id) : 0;// 店铺历史ID
                $cacheData['shop_id'][$shop_id] = $shop_id_history;
            }


            $province_id = $orderBase['province_id'] ?? 0;// 省id
            if(isset($cacheData['province_id'][$province_id]) && $cacheData['province_id'][$province_id] > 0){
                $province_id_history = $cacheData['province_id'][$province_id];
            }else{
                $province_id_history = ($province_id > 0) ? CityDBBusiness::getIdHistory($province_id) : 0; // 省历史id
                $cacheData['province_id'][$province_id] = $province_id_history;
            }


            $city_id = $orderBase['city_id'] ?? 0;// 市id
            if(isset($cacheData['city_id'][$city_id]) && $cacheData['city_id'][$city_id] > 0){
                $city_id_history = $cacheData['city_id'][$city_id];
            }else{
                $city_id_history = ($city_id > 0) ?  CityDBBusiness::getIdHistory($city_id) : 0;// 市历史id
                $cacheData['city_id'][$city_id] = $city_id_history;
            }

            $area_id = $orderBase['area_id'] ?? 0;// 区id
            if(isset($cacheData['area_id'][$area_id]) && $cacheData['area_id'][$area_id] > 0){
                $area_id_history = $cacheData['area_id'][$area_id];
            }else{
                $area_id_history = ($area_id > 0) ?   CityDBBusiness::getIdHistory($area_id) : 0;// 区历史id
                $cacheData['area_id'][$area_id] = $area_id_history;
            }

            if(isset($cacheData['addr_id'][$addr_id]) && $cacheData['addr_id'][$addr_id] > 0){
                $addr_id_history = $cacheData['addr_id'][$addr_id];
            }else{
                $addr_id_history = ($addr_id > 0) ? CommonAddrDBBusiness::getIdHistory($addr_id) : 0;// 收货地址历史ID
                $cacheData['addr_id'][$addr_id] = $addr_id_history;
            }

            $all_total_amount = 0;
            $all_total_price = 0;
            $parentOrderLogs = [];
            if($shopCount > 1){// 有多家店铺
                // 生成订单号
                // 重新发起一笔支付要使用原订单号，避免重复支付；已支付过或已调用关单、撤销（请见后文的API列表）的订单号不能重新发起支付。--支付未成功的订单号，可以重新发起支付
                $parent_order_no = static::createSn($company_id , $operate_staff_id, 1);
                $orders = [
                    'staff_id' => $staff_id,// 用户id
                    'staff_id_history' => $staff_id_history,// 用户历史id
                    // 'send_staff_id' => 'aaaa',// 派送用户id
                    // 'send_staff_id_history' => 'aaaa',// 派送用户历史id
                    'city_site_id' => $city_site_id,// 城市分站id
                    'city_site_id_history' => $city_site_id_history,// 城市分站历史id
                    'city_partner_id' => $city_partner_id,// 城市合伙人id
                    'city_partner_id_history' => $city_partner_id_history,// 城市合伙人历史id
                    'seller_id' => 0,//$seller_id,// 商家ID
                    'seller_id_history' => 0,//$seller_id_history,// 商家历史ID
                    'shop_id' => 0,//$shop_id,// 店铺ID
                    'shop_id_history' => 0,//$shop_id_history,// 店铺历史ID
                    'order_type' => 1,// 订单类型1普通订单/父订单4子订单
                    'has_son_order' => ($shopCount <= 1) ? 0 : 1,// 是否有子订单0无1有
                    'is_order' => 1,// 是否订单1非订单[父订单--无商品]2订单[有商品]
                    'order_no' => $parent_order_no,// 订单号
                    'parent_order_no' => '',// 父订单号
                    'province_id' => $province_id,// 省id
                    'province_id_history' => $province_id_history,// 省历史id
                    'city_id' => $city_id,// 市id
                    'city_id_history' => $city_id_history,// 市历史id
                    'area_id' => $area_id,// 区id
                    'area_id_history' => $area_id_history,// 区历史id
                    'tableware' => $tableware,// 需要餐具数
                    'remarks' => $remarks,// 买家备注
                    'speed_id' => 0,// 送货速度id
                    'speed_name' => '',// 送货速度名称
                    'second_num' => $second_num,// 时间分钟数
                    'send_end_time' => $expiry_time,// 派送到期时间
                    'order_time' => date("Y-m-d H:i:s",time()),// 下单时间
                    //  'pay_time' => 'aaaa',// 付款时间
                    //  'receipt_time' => 'aaaa',// 接单时间
                    //  'cancel_time' => 'aaaa',// 作废时间
                    //  'finish_time' => 'aaaa',// 送到完成时间
                    //  'total_amount' => 'aaaa',// 商品数量
                    // 'total_price' => 'aaaa',// 商品总价
                    'status' => 1,// 状态1待支付2等待接单4取货或配送中8订单完成16作废
                    'total_run_price' => $total_run_price,// 总跑腿费
                    'pay_run_amount' => $total_run_price,// 支付跑腿费[总共支付的]
                    // 'pay_type' => 'aaaa',// 支付方式1余额支付2在线支付
                    'pay_run_price' => 0,// 是否支付跑腿费0未支付1已支付
                    // 'pay_order_no' => 'aaaa',// 支付订单号
                    // 'has_refund' => 'aaaa',// 是否退费0未退费1已退费
                    // 'refund_price' => 'aaaa',// 退费
                    // 'refund_time' => 'aaaa',// 退费时间
                    // 'refund_pay_order_no' => 'aaaa',// 退费支付订单号
                    'addr_id' => $addr_id,// 收货地址ID
                    'addr_id_history' => $addr_id_history,// 收货地址历史ID
                    'operate_staff_id' => $operate_staff_id,// 操作员工id
                    'operate_staff_id_history' => $operate_staff_id_history,// 操作员工历史id
                ];
                $parentOrderObj = OrdersDBBusiness::create($orders);// 父订单
                $parentOrderDoingObj = OrdersDoingDBBusiness::create($orders);// 父订单
                array_push($parentOrderLogs, '创建父订单[' . $parent_order_no . ']');
            }
            // $maxOrderDoingId = 0;// 最大的订单id
            foreach($formatCartList as $vShop){

                $city_site_id = $vShop['city_site_id'] ?? 0;// 城市分站id
                if(isset($cacheData['city_site_id'][$city_site_id]) && $cacheData['city_site_id'][$city_site_id] > 0){
                    $city_site_id_history = $cacheData['city_site_id'][$city_site_id];
                }else{
                    $city_site_id_history = ($city_site_id > 0) ?  CityDBBusiness::getIdHistory($city_site_id) : 0;// 城市分站历史id
                    $cacheData['city_site_id'][$city_site_id] = $city_site_id_history;
                }

                $city_partner_id = $vShop['city_partner_id'] ?? 0;// 城市合伙人id
                if(isset($cacheData['city_partner_id'][$city_partner_id]) && $cacheData['city_partner_id'][$city_partner_id] > 0){
                    $city_partner_id_history = $cacheData['city_partner_id'][$city_partner_id];
                }else{
                    $city_partner_id_history = ($city_partner_id > 0) ? CityPartnerDBBusiness::getIdHistory($city_partner_id) : 0;// 城市合伙人历史id
                    $cacheData['city_partner_id'][$city_partner_id] = $city_partner_id_history;
                }

                $seller_id = $vShop['seller_id'] ?? 0;// 商家ID
                if(isset($cacheData['seller_id'][$seller_id]) && $cacheData['seller_id'][$seller_id] > 0){
                    $seller_id_history = $cacheData['seller_id'][$seller_id];
                }else{
                    $seller_id_history = ($seller_id > 0) ? SellerDBBusiness::getIdHistory($seller_id) : 0;// 商家历史ID
                    $cacheData['seller_id'][$seller_id] = $seller_id_history;
                }

                $shop_id = $vShop['shop_id'] ?? 0;// 店铺ID
                if(isset($cacheData['shop_id'][$shop_id]) && $cacheData['shop_id'][$shop_id] > 0){
                    $shop_id_history = $cacheData['shop_id'][$shop_id];
                }else{
                    $shop_id_history = ($shop_id > 0) ? ShopDBBusiness::getIdHistory($shop_id) : 0;// 店铺历史ID
                    $cacheData['shop_id'][$shop_id] = $shop_id_history;
                }


                $province_id = $vShop['province_id'] ?? 0;// 省id
                if(isset($cacheData['province_id'][$province_id]) && $cacheData['province_id'][$province_id] > 0){
                    $province_id_history = $cacheData['province_id'][$province_id];
                }else{
                    $province_id_history = ($province_id > 0) ?  CityDBBusiness::getIdHistory($province_id) : 0; // 省历史id
                    $cacheData['province_id'][$province_id] = $province_id_history;
                }


                $city_id = $vShop['city_id'] ?? 0;// 市id
                if(isset($cacheData['city_id'][$city_id]) && $cacheData['city_id'][$city_id] > 0){
                    $city_id_history = $cacheData['city_id'][$city_id];
                }else{
                    $city_id_history = ($city_id > 0) ?  CityDBBusiness::getIdHistory($city_id) : 0;// 市历史id
                    $cacheData['city_id'][$city_id] = $city_id_history;
                }

                $area_id = $vShop['area_id'] ?? 0;// 区id
                if(isset($cacheData['area_id'][$area_id]) && $cacheData['area_id'][$area_id] > 0){
                    $area_id_history = $cacheData['area_id'][$area_id];
                }else{
                    $area_id_history = ($area_id > 0) ?   CityDBBusiness::getIdHistory($area_id) : 0 ;// 区历史id
                    $cacheData['area_id'][$area_id] = $area_id_history;
                }

                if(isset($cacheData['addr_id'][$addr_id]) && $cacheData['addr_id'][$addr_id] > 0){
                    $addr_id_history = $cacheData['addr_id'][$addr_id];
                }else{
                    $addr_id_history = ($addr_id > 0) ?  CommonAddrDBBusiness::getIdHistory($addr_id) : 0;// 收货地址历史ID
                    $cacheData['addr_id'][$addr_id] = $addr_id_history;
                }

                // 生成订单号
                // 重新发起一笔支付要使用原订单号，避免重复支付；已支付过或已调用关单、撤销（请见后文的API列表）的订单号不能重新发起支付。--支付未成功的订单号，可以重新发起支付
                $orderNum = static::createSn($company_id , $operate_staff_id, 1);
                $orders = [
                    'staff_id' => $staff_id,// 用户id
                    'staff_id_history' => $staff_id_history,// 用户历史id
                    // 'send_staff_id' => 'aaaa',// 派送用户id
                    // 'send_staff_id_history' => 'aaaa',// 派送用户历史id
                    'city_site_id' => $city_site_id,// 城市分站id
                    'city_site_id_history' => $city_site_id_history,// 城市分站历史id
                    'city_partner_id' => $city_partner_id,// 城市合伙人id
                    'city_partner_id_history' => $city_partner_id_history,// 城市合伙人历史id
                    'seller_id' => $seller_id,// 商家ID
                    'seller_id_history' => $seller_id_history,// 商家历史ID
                    'shop_id' => $shop_id,// 店铺ID
                    'shop_id_history' => $shop_id_history,// 店铺历史ID
                    'order_type' => ($shopCount <= 1) ? 1 : 4,// 订单类型1普通订单/父订单4子订单
                    'has_son_order' => 0,// 是否有子订单0无1有
                    'is_order' => 2,// 是否订单1非订单[父订单--无商品]2订单[有商品]
                    'order_no' => $orderNum,// 订单号
                    'parent_order_no' => $parent_order_no,// 父订单号
                    'province_id' => $province_id,// 省id
                    'province_id_history' => $province_id_history,// 省历史id
                    'city_id' => $city_id,// 市id
                    'city_id_history' => $city_id_history,// 市历史id
                    'area_id' => $area_id,// 区id
                    'area_id_history' => $area_id_history,// 区历史id
                    'tableware' => $tableware,// 需要餐具数
                    'remarks' => $remarks,// 买家备注
                    'speed_id' => 0,// 送货速度id
                    'speed_name' => '',// 送货速度名称
                    'second_num' => $second_num,// 时间分钟数
                    'send_end_time' => $expiry_time,// 派送到期时间
                    'order_time' => date("Y-m-d H:i:s",time()),// 下单时间
                    //  'pay_time' => 'aaaa',// 付款时间
                    //  'receipt_time' => 'aaaa',// 接单时间
                    //  'cancel_time' => 'aaaa',// 作废时间
                    //  'finish_time' => 'aaaa',// 送到完成时间
                    //  'total_amount' => 'aaaa',// 商品数量
                    // 'total_price' => 'aaaa',// 商品总价
                    'status' => 1,// 状态1待支付2等待接单4取货或配送中8订单完成16作废
                    'total_run_price' => $total_run_price,// 总跑腿费
                    'pay_run_amount' => $total_run_price,// 支付跑腿费[总共支付的]
                    // 'pay_type' => 'aaaa',// 支付方式1余额支付2在线支付
                    'pay_run_price' => 0,// 是否支付跑腿费0未支付1已支付
                    // 'pay_order_no' => 'aaaa',// 支付订单号
                    // 'has_refund' => 'aaaa',// 是否退费0未退费1已退费
                    // 'refund_price' => 'aaaa',// 退费
                    // 'refund_time' => 'aaaa',// 退费时间
                    // 'refund_pay_order_no' => 'aaaa',// 退费支付订单号
                    'addr_id' => $addr_id,// 收货地址ID
                    'addr_id_history' => $addr_id_history,// 收货地址历史ID
                    'operate_staff_id' => $operate_staff_id,// 操作员工id
                    'operate_staff_id_history' => $operate_staff_id_history,// 操作员工历史id
                ];
                // 创建订单
                $orderObj = OrdersDBBusiness::create($orders);// 订单
                $orderDoingObj = OrdersDoingDBBusiness::create($orders);// 订单
                // $maxOrderDoingId = $orderDoingObj->id;// 最大的订单id

                $orderLogs = [];
                if($shopCount > 1) {// 有多家店铺
                    array_push($parentOrderLogs, '创建子订单[' . $orderNum . '');
                    array_push($orderLogs, '创建子订单[' . $orderNum . '');
                }else{
                    array_push($orderLogs, '创建订单[' . $orderNum . '');
                }
                OrdersRecordDBBusiness::saveOrderLog($orderObj , $operate_staff_id , $operate_staff_id_history, implode(",", $orderLogs));
                OrdersRecordDoingDBBusiness::saveOrderLog($orderDoingObj , $operate_staff_id , $operate_staff_id_history, implode(",", $orderLogs));

                $order_total_amount = 0;
                $order_total_price = 0;
                // 保存商品
                $cart_list = $vShop['cart_list'] ?? [];

                $cart_list = array_values(array_reverse($cart_list));// 反转数组
                foreach($cart_list as $goodInfo){
                    $goods_id = $goodInfo['goods_id'] ;
                    $cart_id = $goodInfo['id'] ?? 0;
                    if(isset($cacheData['goods_id'][$goods_id]) && $cacheData['goods_id'][$goods_id] > 0){
                        $goods_id_history = $cacheData['goods_id'][$goods_id];
                    }else{
                        $goods_id_history = ($goods_id > 0) ? ShopGoodsDBBusiness::getIdHistory($goods_id) : 0;
                        $cacheData['goods_id'][$goods_id] = $goods_id_history;
                    }

                    $prop_price_id = $goodInfo['prop_price_id'] ;
                    if(isset($cacheData['prop_price_id'][$prop_price_id]) && $cacheData['prop_price_id'][$prop_price_id] > 0){
                        $prop_price_id_history = $cacheData['prop_price_id'][$prop_price_id];
                    }else{
                        $prop_price_id_history = ($prop_price_id > 0) ? ShopGoodsPricesDBBusiness::getIdHistory($prop_price_id) : 0;
                        $cacheData['prop_price_id'][$prop_price_id] = $prop_price_id_history;
                    }

                    $resource_id = $goodInfo['resource_id'] ;
                    if(isset($cacheData['resource_id'][$resource_id]) && $cacheData['resource_id'][$resource_id] > 0){
                        $resource_id_history = $cacheData['resource_id'][$resource_id];
                    }else{
                        $resource_id_history = ($resource_id > 0) ? ResourceDBBusiness::getIdHistory($resource_id) : 0;
                        $cacheData['resource_id'][$resource_id] = $resource_id_history;
                    }


                    $price = $goodInfo['price'] ;
                    $amount = $goodInfo['amount'] ;
                    $totalPrice = $price * $amount;

                    $order_total_amount += $amount;
                    $order_total_price += $totalPrice;
                    $ordersGoods = [
                        'order_no' => $orderNum,// 订单号
                        'staff_id' => $staff_id,// 用户id
                        'staff_id_history' => $staff_id_history,// 用户历史id
                        'city_site_id' => $city_site_id,// 城市分站id
                        'city_site_id_history' => $city_site_id_history,// 城市分站历史id
                        'city_partner_id' => $city_partner_id,// 城市合伙人id
                        'city_partner_id_history' => $city_partner_id_history,// 城市合伙人历史id
                        'seller_id' => $seller_id,// 商家ID
                        'seller_id_history' => $seller_id_history,// 商家历史ID
                        'shop_id' => $shop_id,// 店铺ID
                        'shop_id_history' => $shop_id_history,// 店铺历史ID
                        'goods_id' => $goods_id,// 商品ID
                        'goods_id_history' => $goods_id_history,// 商品历史ID
                        'prop_price_id' => $prop_price_id,// 商品价格ID
                        'prop_price_id_history' => $prop_price_id_history,// 商品价格历史ID
                       //  'spec_id' => $goodInfo['bbb'] ?? '',// 规格ID
                        'price' => $price,// 价格(单价)
                        'amount' => $amount,// 数量
                        'total_price' => $totalPrice,// 总价(商品)
                        'resource_id_history' => $resource_id_history,// 资源历史id
                        'cart_id' => $cart_id,// 购物车id[显示作用]
                        'operate_staff_id' => $operate_staff_id,// 操作员工id
                        'operate_staff_id_history' => $operate_staff_id_history,// 操作员工历史id
                    ];
                    // 保存商品
                    $orderGoodsObj = OrdersGoodsDBBusiness::create($ordersGoods);// 订单商品
                    $orderGoodsDoingObj = OrdersGoodsDoingDBBusiness::create($ordersGoods);// 订单商品
                    // 处理商品属性
                    $goodsProps = $goodInfo['props'] ?? [];
                    if(empty($goodsProps)) continue;
                    foreach($goodsProps as $tGoodsProp){
                        $goods_props_id = $tGoodsProp['goods_props_id'];
                        if(isset($cacheData['goods_props_id'][$goods_props_id]) && $cacheData['goods_props_id'][$goods_props_id] > 0){
                            $goods_props_id_history = $cacheData['goods_props_id'][$goods_props_id];
                        }else{
                            $goods_props_id_history = ($goods_props_id > 0) ? ShopGoodsPropsDBBusiness::getIdHistory($goods_props_id) : 0;
                            $cacheData['goods_props_id'][$goods_props_id] = $goods_props_id_history;
                        }

                        $orderGoodsProps = [
                            // 'orders_goods_id' => 'aaaa',// 订单商品表ID
                            'goods_props_id' => $goods_props_id,// 商品属性ID
                            'goods_props_id_history' => $goods_props_id_history,// 商品属性历史ID
                            'prop_id' => $tGoodsProp['prop_id'],// 属性ID
                            'prop_val_id' => $tGoodsProp['prop_val_id'],// 属性值ID
                            'prop_names_id' => $tGoodsProp['prop_names_id'],// 属性名称词ID
                            'prop_val_names_id' => $tGoodsProp['prop_val_names_id'],// 属性值名称词ID
                            'operate_staff_id' => $operate_staff_id,// 操作员工id
                            'operate_staff_id_history' => $operate_staff_id_history,// 操作员工历史id
                        ];
                        // 保存商品属性
                        $orderGoodsProps['orders_goods_id'] = $orderGoodsObj->id;
                        $orderGoodsPropsObj = OrderGoodsPropsDBBusiness::create($orderGoodsProps);// 订单商品属性

                        $orderGoodsProps['orders_goods_id'] = $orderGoodsDoingObj->id;
                        $orderGoodsPropsDoingObj = OrderGoodsPropsDoingDBBusiness::create($orderGoodsProps);// 订单商品属性
                    }

                }
                // 修改订单总数量及总价格
                $orderObj->total_amount = $order_total_amount;
                $orderDoingObj->total_amount = $order_total_amount;

                $orderObj->total_price = $order_total_price;
                $orderDoingObj->total_price = $order_total_price;

                $orderObj->save();
                $orderDoingObj->save();

                if($shopCount > 1) {// 有多家店铺

                    $all_total_amount += $order_total_amount;
                    $all_total_price += $order_total_price;
                }



            }
            if($shopCount > 1) {// 有多家店铺

                $parentOrderObj->total_amount = $all_total_amount;
                $parentOrderDoingObj->total_amount = $all_total_amount;

                $parentOrderObj->total_price = $all_total_price;
                $parentOrderDoingObj->total_price = $all_total_price;

                $parentOrderObj->save();
                $parentOrderDoingObj->save();
                OrdersRecordDBBusiness::saveOrderLog($parentOrderObj , $operate_staff_id , $operate_staff_id_history, implode(",", $parentOrderLogs));
                OrdersRecordDoingDBBusiness::saveOrderLog($parentOrderDoingObj , $operate_staff_id , $operate_staff_id_history, implode(",", $parentOrderLogs));
                $orderNum = $parent_order_no;

            }

            // 删除购物车

            // 删除属性表记录
            $propQueryParams = [
                'whereIn' => [
                    'cart_id' => explode(',', $cartIds),
                ]
            ];
            CartGoodsPropsDBBusiness::del($propQueryParams);
            // 删除主表记录
            static::deleteByIds($cartIds);
            // 缓存最大订订单id
            // Tool::setRedis('order:', 'maxOrderDoingId', $maxOrderDoingId, 0 , 3);

        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return $orderNum;
    }
}
