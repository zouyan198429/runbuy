<?php
// 钱包操作记录
namespace App\Business\DB\RunBuy;

use App\Services\Tool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class WalletRecordDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\WalletRecord';
    public static $table_name = 'wallet_record';// 表名称

    /**
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

//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
//
//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }

        DB::beginTransaction();
        try {
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
                static::compareHistory($id, 1);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

    /**
     * 支付订单跑腿费或追加订单跑腿费
     *
     * @param array $params 参数
        $params = [
            'pay_way' => 2,// 支付方式1余额支付2微信支付
            'pay_type' => 1, // 支付类型 1 订单支付跑腿费--[订单有关] 2 订单追加跑腿费--[订单有关]   3 其它支付--[订单无关]
            'order_type' => 10,// $orderType 订单类型编号 1 订单号 2 退款订单 3 支付跑腿费  4 追加跑腿费 5 冲值  6 提现 7 压金或保证金
            'operate_type' => 10,// 操作类型1充值2提现3交压金/保证金4订单付款5追加付款8退款16冻结32解冻
            'operate_text' => '',// 操作名称
            'amount' => 10,// 追加跑腿费 单位元 -- 2 订单追加跑腿费 用
        ];
     * @param int  $company_id 企业id
     * @param string $order_no 订单号
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array
        $returnArr = [
            'body' => '支付说明',
            'total_fee' => '支付金额，单位元',
            'out_trade_no' => '支付单号-我方',
        ];
     * @author zouyan(305463219@qq.com)
     */
    public static function payOrder($params, $company_id, $order_no, $operate_staff_id = 0){
        $returnArr = [
            'body' => '',// '支付说明',
            'total_fee' => 0,// '支付金额，单位元',
            'out_trade_no' => '',// '支付单号-我方',
        ];

        $pay_type = $params['pay_type'];// 支付类型 1 订单支付跑腿费--[订单有关] 2 订单追加跑腿费--[订单有关]   3 其它支付--[订单无关]
        if(!in_array($pay_type, [1,2,3])) throws('支付类型有误!');

        $pay_way = $params['pay_way'];
        $order_type = $params['order_type'];
        $operate_type = $params['operate_type'];
        $operate_text = $params['operate_text'];

        $orderInfo = [];
        if(in_array($pay_type, [1,2])){
            if( empty($order_no)) throws('订单号不能为空！');
            $queryParams = [
                'where' => [
                    ['order_type', 1],
                    // ['staff_id', $operate_staff_id],
                    ['order_no',$order_no],
                ],
                // 'select' => ['id', 'status', 'pay_run_price', 'has_refund', 'total_run_price' ]
            ];
            // 获得订单详情
            $orderInfo = OrdersDoingDBBusiness::getInfoByQuery(1, $queryParams, []);
            if(empty($orderInfo)) throws('订单信息不存在 !');
        }

        $lockObj = Tool::getLockRedisesLaravelObj();
        $lockState = $lockObj->lock('lock:' . Tool::getUniqueKey([Tool::getActionMethod(), __CLASS__, __FUNCTION__, $order_no]), 2000, 2000);//加锁
        if($lockState)
        {
            DB::beginTransaction();
            try {
                $operate_staff_id_history = 0;
                switch($pay_type){
                    case 1:// 1 订单支付跑腿费
                        $status = $orderInfo['status'];// 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
                        if($status != 1) throws('订单非待支付状态!');

                        $pay_run_price = $orderInfo['pay_run_price'];// 是否支付跑腿费0未支付1已支付
                        if($pay_run_price != 0) throws('订单非未支付!');

                        $has_refund = $orderInfo['has_refund'];// 是否退费0未退费1已退费2待退费
                        if($has_refund != 0) throws('订单非未退费!');

                        $total_run_price = $orderInfo['total_run_price'];

                        // 生成订单号
                        $body = config('public.webName') . '-' . $operate_text;// 跑腿订单[' . $order_no . ']服务费';
                        $returnArr['body'] = $body;
                        $returnArr['total_fee'] = $total_run_price;

                        // 查找未支付成功的订单号，重新下单
                        // 获得2待确认和8失败 的记录    状态1已关闭2待确认4成功8失败
                        $queryParams = [
                            'where' => [
                                ['operate_type', 4],
                                ['staff_id', $operate_staff_id],
                                ['pay_order_no',$order_no],
                            ],
                            'select' => ['id', 'amount', 'status', 'my_order_no' ]
                        ];
                        $wrList = static::getAllList($queryParams, [])->toArray();
                        $my_order_no = '';// 可以支付的单号
                        foreach($wrList as $k => $v){
                            $payStatus = $v['status'];
                            if($payStatus == 4) throws('订单已支付过，订单号[' . $v['my_order_no'] . ']');
                            if(in_array($payStatus, [2, 8])) {// 状态1已关闭2待确认4成功8失败
                                $my_order_no = $v['my_order_no'];
                                break;
                            }
                        }
                        if($my_order_no == ''){// 重新生成支付订单
                            $my_order_no = static::createSn($company_id , $operate_staff_id, 3);
                            $temData = [];
                            static::addOprate($temData, $operate_staff_id,$operate_staff_id_history);
                            // 生成支付记录
                            $saveData = [
                                'staff_id' => $operate_staff_id,// 用户id
                                'staff_id_history' => $operate_staff_id_history,// 用户历史id
                                'operate_type' => 4,// 操作类型1充值2提现3交压金/保证金4订单付款5追加付款8退款16冻结32解冻
                                // 'pay_config_id' => 0,// 支付配置ID--提现用
                                // 'pay_config_id_history' => 0,// 支付配置历史ID--提现用
                                'pay_type' => $pay_way,// 2,// 支付方式1余额支付2微信支付
                                'pay_order_no' => $order_no,// 支付订单号[有则填]-订单表的订单号
                                // 'my_order_no_old' => 'aaa',// 原我方单号--与第三方对接用--如退款时，需要原交易号
                                'my_order_no' => $my_order_no,// 我方单号--与第三方对接用
                                // 'third_order_no_old' => 'aaa',// 原第三方单号[有则填]--如退款时，需要原交易号
                                // 'third_order_no' => 'aaa',// 第三方单号[有则填]
                                'content' => $body,// 记录内容
                                'amount' => $total_run_price,// 金额-具体金额
                                'refund_amount' => 0,// 已退费[所有退费]
                                'final_amount' => $total_run_price,// 最终剩余金额[所有退费后]
                                 'amount_frozen' => 0,// 金额[冻结]-具体金额
                                // 'total_money' => 'aaa',// 总金额[操作后]
                                // 'frozen_money' => 'aaa',// 冻结金额[操作后]
                                'status' => 2,// 状态1已关闭2待确认4成功8失败
                                // 'sure_time' => 'aaa',// 确认时间
                                'operate_staff_id' => $operate_staff_id,// 操作员工id
                                'operate_staff_id_history' => $operate_staff_id_history,// 操作员工历史id
                            ];
                            $wr_id = 0;
                            static::replaceById($saveData, $company_id, $wr_id, $operate_staff_id, 0);
                        }
                        $returnArr['out_trade_no'] = $my_order_no;

                        OrdersRecordDBBusiness::saveOrderLog($orderInfo , $operate_staff_id , $operate_staff_id_history, '订单微信下单付款，付款单号:' . $my_order_no);
                        OrdersRecordDoingDBBusiness::saveOrderLog($orderInfo , $operate_staff_id , $operate_staff_id_history, '订单微信下单付款，付款单号:' . $my_order_no);

                        break;
                    case 2:// 2 订单追加跑腿费
                        $amount = $params['amount'];// 追加跑腿费 单位元
                        if(!is_numeric($amount) && $amount < 1) throws('费用不能小于1!');

                        $status = $orderInfo['status'];// 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
                        if($status != 2) throws('订单非待接单状态!');

                        $pay_run_price = $orderInfo['pay_run_price'];// 是否支付跑腿费0未支付1已支付
                        if($pay_run_price != 1) throws('订单非已支付!');

                        $has_refund = $orderInfo['has_refund'];// 是否退费0未退费1已退费2待退费
                        if($has_refund != 0) throws('订单非未退费!');

                        // $total_run_price = $orderInfo['total_run_price'];

                        // 生成订单号
                        $body = config('public.webName') . $operate_text;// '-跑腿订单[' . $order_no . ']追加服务费';
                        $returnArr['body'] = $body;
                        $returnArr['total_fee'] = $amount;

                        $temData = [];
                        static::addOprate($temData, $operate_staff_id,$operate_staff_id_history);
                        $my_order_no = static::createSn($company_id , $operate_staff_id, 4);
                        // 生成支付记录
                        $saveData = [
                            'staff_id' => $operate_staff_id,// 用户id
                            'staff_id_history' => $operate_staff_id_history,// 用户历史id
                            'operate_type' => 5,// 操作类型1充值2提现3交压金/保证金4订单付款5追加付款8退款16冻结32解冻
                            // 'pay_config_id' => 0,// 支付配置ID--提现用
                            // 'pay_config_id_history' => 0,// 支付配置历史ID--提现用
                            'pay_type' => $pay_way,// 2,// 支付方式1余额支付2微信支付
                            'pay_order_no' => $order_no,// 支付订单号[有则填]-订单表的订单号
                            // 'my_order_no_old' => 'aaa',// 原我方单号--与第三方对接用--如退款时，需要原交易号
                            'my_order_no' => $my_order_no,// 我方单号--与第三方对接用
                            // 'third_order_no_old' => 'aaa',// 原第三方单号[有则填]--如退款时，需要原交易号
                            // 'third_order_no' => 'aaa',// 第三方单号[有则填]
                            'content' => $body,// 记录内容
                            'amount' => $amount,// 金额-具体金额
                            'refund_amount' => 0,// 已退费[所有退费]
                            'final_amount' => $amount,// 最终剩余金额[所有退费后]
                             'amount_frozen' => 0,// 金额[冻结]-具体金额
                            // 'total_money' => 'aaa',// 总金额[操作后]
                            // 'frozen_money' => 'aaa',// 冻结金额[操作后]
                            'status' => 2,// 状态1已关闭2待确认4成功8失败
                            // 'sure_time' => 'aaa',// 确认时间
                            'operate_staff_id' => $operate_staff_id,// 操作员工id
                            'operate_staff_id_history' => $operate_staff_id_history,// 操作员工历史id
                        ];
                        $wr_id = 0;
                        static::replaceById($saveData, $company_id, $wr_id, $operate_staff_id, 0);

                        $returnArr['out_trade_no'] = $my_order_no;

                        OrdersRecordDBBusiness::saveOrderLog($orderInfo , $operate_staff_id , $operate_staff_id_history, '订单微信加价[' . $amount .'元]崔单付款，付款单号:' . $my_order_no);
                        OrdersRecordDoingDBBusiness::saveOrderLog($orderInfo , $operate_staff_id , $operate_staff_id_history, '订单微信加价[' . $amount .'元]崔单付款，付款单号:' . $my_order_no);

                        break;
                    default:
                        $amount = $params['amount'];// 追加跑腿费 单位元
                        if(!is_numeric($amount) && $amount < 1) throws('费用不能小于1!');

                        // $total_run_price = $orderInfo['total_run_price'];

                        // 生成订单号
                        $body = config('public.webName') . '-' . $operate_text;
                        $returnArr['body'] = $body;
                        $returnArr['total_fee'] = $amount;

                        $temData = [];
                        static::addOprate($temData, $operate_staff_id,$operate_staff_id_history);
                        $my_order_no = static::createSn($company_id , $operate_staff_id, $order_type);
                        // 生成支付记录
                        $saveData = [
                            'staff_id' => $operate_staff_id,// 用户id
                            'staff_id_history' => $operate_staff_id_history,// 用户历史id
                            'operate_type' => $operate_type,// 操作类型1充值2提现3交压金/保证金4订单付款5追加付款8退款16冻结32解冻
                            // 'pay_config_id' => 0,// 支付配置ID--提现用
                            // 'pay_config_id_history' => 0,// 支付配置历史ID--提现用
                            'pay_type' => $pay_way,// 2,// 支付方式1余额支付2微信支付
                            // 'pay_order_no' => $order_no,// 支付订单号[有则填]-订单表的订单号
                            // 'my_order_no_old' => 'aaa',// 原我方单号--与第三方对接用--如退款时，需要原交易号
                            'my_order_no' => $my_order_no,// 我方单号--与第三方对接用
                            // 'third_order_no_old' => 'aaa',// 原第三方单号[有则填]--如退款时，需要原交易号
                            // 'third_order_no' => 'aaa',// 第三方单号[有则填]
                            'content' => $body,// 记录内容
                            'amount' => $amount,// 金额-具体金额
                            'refund_amount' => 0,// 已退费[所有退费]
                            'final_amount' => $amount,// 最终剩余金额[所有退费后]
                             'amount_frozen' => 0,// 金额[冻结]-具体金额
                            // 'total_money' => 'aaa',// 总金额[操作后]
                            // 'frozen_money' => 'aaa',// 冻结金额[操作后]
                            'status' => 2,// 状态1已关闭2待确认4成功8失败
                            // 'sure_time' => 'aaa',// 确认时间
                            'operate_staff_id' => $operate_staff_id,// 操作员工id
                            'operate_staff_id_history' => $operate_staff_id_history,// 操作员工历史id
                        ];
                        $wr_id = 0;
                        static::replaceById($saveData, $company_id, $wr_id, $operate_staff_id, 0);

                        $returnArr['out_trade_no'] = $my_order_no;
                        break;
                }
            } catch ( \Exception $e) {
                DB::rollBack();
                throws($e->getMessage());
                // throws($e->getMessage());
            }finally{
                $lockObj->unlock($lockState);//解锁
            }
            DB::commit();
        }else{
            throws('操作失败，请稍后重试!');
        }
        return $returnArr;
    }

    /**
     * 支付回调--微信
     *
     * @param array $message 回调的参数
        {
            "appid": "wxcb82783fe211782f",
            "bank_type": "CFT",// 银行类型
            "cash_fee": "1",// 现金
            "fee_type": "CNY",// 币种
            "is_subscribe": "N",// 是否订阅
            "mch_id": "1527642191",
            "nonce_str": "5c8e67b1d9bc3",
            "openid": "owfFF4ydu2HmuvmSDS4goIoAIYEs",
            "out_trade_no": "119108029350007",
            "result_code": "SUCCESS",// 支付结果 FAIL:失败;SUCCESS:成功
            "return_code": "SUCCESS",// 表示通信状态: SUCCESS 成功
            "sign": "C6ACF2C7C8AF999048094ED2264F0ABC",
            "time_end": "20190317232919",// 交易时间
            "total_fee": "1",// 交易金额
            "trade_type": "JSAPI",// 交易类型
            "transaction_id": "4200000288201903177135850941"// 交易号
        }
     * @param array $queryMessage 商户订单号查询 结果
     *
         商户订单号查询 结果
        {
            "return_code": "SUCCESS",
            "return_msg": "OK",
            "appid": "wxcb82783fe211782f",
            "mch_id": "1527642191",
            "nonce_str": "aA5oRYgVOf7osQv3",
            "sign": "DCD3A1790A8C4E1A4BBE2339E812AB3C",
            "result_code": "SUCCESS",
            "openid": "owfFF4ydu2HmuvmSDS4goIoAIYEs",
            "is_subscribe": "N",
            "trade_type": "JSAPI",
            "bank_type": "CFT",
            "total_fee": "1",
            "fee_type": "CNY",
            "transaction_id": "4200000288201903177135850941",
            "out_trade_no": "119108029350007",
            "attach": null,
            "time_end": "20190317232919",
            "trade_state": "SUCCESS",// 交易状态
            "cash_fee": "1",
            "trade_state_desc": "支付成功"
        }
     *
             * 交易状态
            SUCCESS—支付成功
            REFUND—转入退款
            NOTPAY—未支付
            CLOSED—已关闭
            REVOKED—已撤销（付款码支付）
            USERPAYING--用户支付中（付款码支付）
            PAYERROR--支付失败(其他原因，如银行返回失败)
            支付状态机请见下单API页面
     * @return  mixed string throws错误，请再通知我  正常返回 :不用通知我了
     * @author zouyan(305463219@qq.com)
     */
    public static function payWXNotify($message, $queryMessage){

        try{
            // 查询订单
            $out_trade_no = $message['out_trade_no'] ?? '';// 我方单号--与第三方对接用
            $out_trade_no = trim($out_trade_no);
            if(empty($out_trade_no)) throws('参数out_trade_no不能为空!');
            $transaction_id = $message['transaction_id'] ?? '';// 第三方单号[有则填]
            $transaction_id = trim($transaction_id);
            if(empty($transaction_id)) throws('参数transaction_id不能为空!');
            // 查询支付单
            $queryParams = [
                'where' => [
                    ['my_order_no', $out_trade_no],
                ],
                /*
                'select' => [
                    'id','title','sort_num','volume'
                    ,'operate_staff_id','operate_staff_id_history'
                    ,'created_at' ,'updated_at'
                ],
                */
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            // 查询记录
            $wrInfo = static::getInfoByQuery(1, $queryParams, []);
            if(empty($wrInfo)) return '记录不存在';// 1; //记录不存在

            $status = $wrInfo->status;// 状态1已关闭2待确认4成功8失败
            if(in_array($status, [1,4])) return '已关闭或成功';//  return 1;// 已关闭或成功

        } catch ( \Exception $e) {
            // throws('失败；信息[' . $e->getMessage() . ']');
//            return $e->getMessage();// $fail($e->getMessage());
            throws($e->getMessage());
        }
       // pr($wrInfo->toArray());
        // pr($message);
        // pr($queryMessage);

//            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
//                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
//            }

//            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
//
        $returnStr = '';
        if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态

            $lockObj = Tool::getLockRedisesLaravelObj();
            $lockState = $lockObj->lock('lock:' . Tool::getUniqueKey([Tool::getActionMethod(), __CLASS__, __FUNCTION__, $out_trade_no]), 2000, 2000);//加锁
            if($lockState)
            {
                DB::beginTransaction();
                try {
                    $wrInfo->third_order_no = $transaction_id;
                    // 用户是否支付成功
                    $payStatus = 1;// 1失败  2 成功
                    if ($message['result_code'] === 'SUCCESS' && $queryMessage['trade_state'] === 'SUCCESS') {
                        $payStatus = 2;// 1失败  2 成功
                        $wrInfo->status = 4;
                        $wrInfo->sure_time = date("Y-m-d H:i:s",time());
        //                    $order->paid_at = time(); // 更新支付时间为当前时间
        //                    $order->status = 'paid';
        //                $saveData['status'] = 2;
        //                $saveData['pay_run_price'] = 1;
        //                $saveData['pay_order_no'] = $transaction_id;
        //                $saveData['pay_time'] = date("Y-m-d H:i:s",time());

                        // 用户支付失败
                    } elseif ($message['result_code'] === 'FAIL') {
                        $wrInfo->status = 8;
        //                    $order->status = 'paid_fail';
                        // $saveData['pay_run_price'] = 4;
                    }
                     $wrInfo->save();

        //            try{
        //                $resultDatas = CTAPIOrdersDoingBusiness::replaceById($request, $this, $saveData, $id, false, 1);
        //                $resultOrder = CTAPIOrdersBusiness::replaceById($request, $this, $saveData, $order_id, false, 1);
        //
        //            } catch ( \Exception $e) {
        //                // throws('失败；信息[' . $e->getMessage() . ']');
        //                return $fail($e->getMessage());
        //            }

                    $operate_staff_id_history = 0;
                    // 订单类型编号 1 订单号 2 退款订单 3 支付跑腿费  4 追加跑腿费 5 冲值  6 提现 7 压金或保证金
                    $orderType = substr($out_trade_no,0, 1);

                    $temData = [];
                    static::addOprate($temData, $wrInfo->staff_id,$operate_staff_id_history);
                    switch($orderType){
                        case 3:// 支付跑腿费
                        case 4:// 4 追加跑腿费
                            $order_no = $wrInfo->pay_order_no;
                            $queryParams = [
                                'where' => [
                                    ['order_type', 1],
                                    // ['staff_id', $wrInfo->staff_id],
                                    ['order_no',$order_no],
                                ],
                                // 'select' => ['id', 'status', 'pay_run_price', 'has_refund', 'total_run_price' ]
                            ];
                            // 获得订单详情
                            $orderInfo = OrdersDoingDBBusiness::getInfoByQuery(1, $queryParams, []);
                            if(empty($orderInfo)) throws('订单信息不存在!!', 10);// throws(1);// throws('订单信息不存在!');
                            if($orderType == 3){// 支付跑腿费
                                $status = $orderInfo->status;// 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
                                if($status != 1) throws('订单非待支付状态!', 10);// throws(1);// throws('订单非待支付状态!');

                                $pay_run_price = $orderInfo->pay_run_price;// 是否支付跑腿费0未支付1已支付
                                if($pay_run_price != 0) throws('订单非未支付!', 10);// throws(1);// throws('订单非未支付!');

                                $has_refund = $orderInfo->has_refund;// 是否退费0未退费1已退费2待退费
                                if($has_refund != 0) throws('订单非未退费!', 10);// throws(1);// throws('订单非未退费!');

                                // $payStatus = 2;// 1失败  2 成功
                                if($payStatus == 2){
                                    // 更新订单饱和度
                                    CityDBBusiness::cityOrdersOperate($orderInfo->city_site_id, 2, 1);// 增加订单
                                    $orderSaveData = [
                                        'status' => 2,
                                        'pay_run_price' => 1,
                                        'pay_order_no' => $transaction_id,
                                        'pay_time' => date("Y-m-d H:i:s",time()),
                                        'pay_time_latest' => date("Y-m-d H:i:s",time()),
                                    ];
    //                                $orderInfo->status = 2;
    //                                $orderInfo->pay_run_price = 1;
    //                                $orderInfo->pay_order_no = $transaction_id;
    //                                $orderInfo->pay_time = date("Y-m-d H:i:s",time());
    //                                $orderInfo->save();
                                    OrdersDoingDBBusiness::updateOrders($orderSaveData,  $order_no, 1 + 2 + 4, $wrInfo->staff_id, $operate_staff_id_history
                                        , '回调通知付款成功！');
                                }else{// 失败日志
                                    OrdersRecordDBBusiness::saveOrderLog($orderInfo , $wrInfo->staff_id , $operate_staff_id_history, '回调通知付款失败');
                                    OrdersRecordDoingDBBusiness::saveOrderLog($orderInfo , $wrInfo->staff_id , $operate_staff_id_history, '回调通知付款失败');
                                }
                            }

                            if($orderType == 4) {// 4 追加跑腿费
                                // $status = $orderInfo->status;// 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
                                // if($status != 2) throws('订单非待接单状态!', 10);// throws(1);//throws('订单非待接单状态!');

                                $pay_run_price = $orderInfo->pay_run_price;// 是否支付跑腿费0未支付1已支付
                                if($pay_run_price != 1) throws('订单非已支付!', 10);// throws(1);// throws('订单非已支付!');

                                // $has_refund = $orderInfo->has_refund;// 是否退费0未退费1已退费2待退费
                                // if($has_refund != 0) throws('订单非未退费!', 10);// throws(1);// throws('订单非未退费!');
                                // $payStatus = 2;// 1失败  2 成功
                                if($payStatus == 2){
                                    // $orderInfo->total_run_price += $wrInfo->amount;
                                    // $orderInfo->save();
                                    $orderSaveData = [
                                        'total_run_price' =>  $orderInfo->total_run_price + $wrInfo->amount,
                                        'pay_run_amount'=>  $orderInfo->pay_run_amount + $wrInfo->amount,
                                        'pay_time_latest' => date("Y-m-d H:i:s",time()),
                                    ];
                                    OrdersDoingDBBusiness::updateOrders($orderSaveData,  $order_no, 1 + 2 + 4, $wrInfo->staff_id, $operate_staff_id_history
                                        , '回调通知加价崔单付款成功！金额:' . $wrInfo->amount);
                                }else{// 失败日志
                                    OrdersRecordDBBusiness::saveOrderLog($orderInfo , $wrInfo->staff_id , $operate_staff_id_history, '回调通知加价崔单付款失败！金额:' . $wrInfo->amount);
                                    OrdersRecordDoingDBBusiness::saveOrderLog($orderInfo , $wrInfo->staff_id , $operate_staff_id_history, '回调通知加价崔单付款失败！金额:' . $wrInfo->amount);

                                }
                            }
                            break;
                        case 5:// 5 冲值--钱包
                        case 7:// 7 压金或保证金
                            // $payStatus = 2;// 1失败  2 成功
                            if($payStatus == 2) {
                                $queryParams = [
                                    'where' => [
                                        ['staff_id', $wrInfo->staff_id],
                                    ],
                                    /**
                                     * 'select' => [
                                     * 'id','title','sort_num','volume'
                                     * ,'operate_staff_id','operate_staff_id_history'
                                     * ,'created_at' ,'updated_at'
                                     * ],
                                     * **/
                                    //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                                ];

                                // 查询记录
                                $walletInfo = WalletDBBusiness::getInfoByQuery(1, $queryParams, []);
                                if (empty($walletInfo)) {//记录不存在-- 新加
                                    $walletData = [
                                        'staff_id' => $wrInfo->staff_id,
                                        'staff_id_history' => $operate_staff_id_history,
                                        'total_money' => $wrInfo->amount,
                                        'frozen_money' => 0,
                                        'avail_money' => 0,// $wrInfo->amount,
                                        'is_frozen' => 0,
                                        // 'check_key' => $aaaa,
                                        'operate_staff_id' => $wrInfo->staff_id,
                                        'operate_staff_id_history' => $operate_staff_id_history,
                                    ];
                                    if ($orderType == 5) $walletData['avail_money'] = $wrInfo->amount;
                                    if ($orderType == 7) $walletData['frozen_money'] = $wrInfo->amount;

                                    $walletData['check_key'] = WalletDBBusiness::getCheckKey($walletData['staff_id'], $walletData['total_money'], $walletData['frozen_money'], $walletData['avail_money']);
                                    WalletDBBusiness::create($walletData);
                                } else {// 修改
                                    // 校验字串   钱包
                                    $oldCheckKey = WalletDBBusiness::getCheckKey($walletInfo->staff_id, $walletInfo->total_money, $walletInfo->frozen_money, $walletInfo->avail_money);

                                    if ($oldCheckKey != $walletInfo->check_key) throws('钱包信息可能被篡改！请联系系统管理员', 10);

                                    $walletInfo->total_money = $walletInfo->total_money + $wrInfo->amount;
                                    if ($orderType == 5) $walletInfo->avail_money += $wrInfo->amount;
                                    if ($orderType == 7) $walletInfo->frozen_money += $wrInfo->amount;

                                    $walletInfo->check_key = WalletDBBusiness::getCheckKey($walletInfo->staff_id, $walletInfo->total_money, $walletInfo->frozen_money, $walletInfo->avail_money);
                                    $walletInfo->operate_staff_id_history = $operate_staff_id_history;
                                    $walletInfo->save();
                                }
                            }
                            break;
                        default:
                            break;
                    }
                    // Log::info('微信支付日志 $orderDatas' . __FUNCTION__, [$saveData, $resultDatas, $resultOrder ]);
                    // return 1;
                } catch ( \Exception $e) {
                    DB::rollBack();
                    $errMsg = $e->getMessage();
                    $errCode = $e->getCode();
                    if($errCode == 10 ){
                        $returnStr = $errMsg;
                        return $returnStr;
                    }else{
    //                    throws('操作失败；信息[' . $e->getMessage() . ']');
                        throws($e->getMessage());
                    }
    //                if(is_numeric($errMsg) || $errMsg == 1){
    //
    //                }else{
    //                    DB::rollBack();
    ////                    throws('操作失败；信息[' . $e->getMessage() . ']');
    //                     throws($e->getMessage());
    //                }
                }finally{
                    $lockObj->unlock($lockState);//解锁
                }
                DB::commit();
            }else{
                throws('操作失败，请稍后重试!');
            }
            return $returnStr;
        } else {
//            return '通信失败，请稍后再通知我';// $fail('通信失败，请稍后再通知我');
            throws('通信失败，请稍后再通知我');
        }
        return '';
//
//            $order->save(); // 保存订单

//            return true; // 返回处理完成
    }


    /**
     * 申请退款--微信
     *
     * @param array $params 参数
        $params = [
            [
                'order_no' => '', // 订单号, 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
                'my_order_no' => '',//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
                'refund_amount' => 0,// 需要退款的金额--0为全退---单位元
                'refund_reason' => '',// 退款的原因--:为空，则后台自己组织内容
            ]
        ];
     * @param int  $company_id 企业id
     * @param int $operate_staff_id 操作人id
     * @return  array
        $returnArr = [
            [
                'pay_order_no' => '',// 我方的付款单号
                'refund_order_no' => '',// 我方生成的退款单号
                'pay_amount' => 0,// 我方付款的总金额[当前付款单]--单位元
                'refund_amount' => 0,// 需要退款的金额---单位元
                'config'=> [// 其它退款参数
                    'refund_desc' => '',// 退款的原因
                ]
            ],
        ];
     * @author zouyan(305463219@qq.com)
     */
    public static function refundApplyWX($params, $company_id, $operate_staff_id = 0){
        $returnArr = [];
        // 查询支付单
        $queryParams = [
            'where' => [
                ['status', 4],
            ],
            /*
            'select' => [
                'id','title','sort_num','volume'
                ,'operate_staff_id','operate_staff_id_history'
                ,'created_at' ,'updated_at'
            ],
            */
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        // 没有订单号的，查询结果，如果是订单，补上订单号
        $allOrders = [];
        $allMyOrders = [];
        foreach($params as $k => $v) {
            $order_no = $v['order_no'] ?? '';// 订单号 -- order_no 或 my_order_no 之一不能为空
            $my_order_no = $v['my_order_no'] ?? '';// 付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
            $refund_amount = $v['refund_amount'] ?? 0;// 需要退款的金额--0为全退---单位元
            $refund_reason = $v['refund_reason'] ?? '';// 退款的原因--:为空，则后台自己组织内容
            if (empty($order_no) && empty($my_order_no)) throws('参数order_no 或 my_order_no之一不能为空!');
            if(empty($order_no)){// 订单号为空，则读取记录，判断是否有订单号
                $query = $queryParams;
                $query['select'] = ['id', 'pay_order_no'];
                array_push($query['where'], ['my_order_no', $my_order_no]);
                $wrInfo = static::getInfoByQuery(1, $query, []);
                if(empty($wrInfo)) return throws('付款单[' . $my_order_no . '] 记录不存在'); //记录不存在
                $order_no = $wrInfo->pay_order_no;
                if(!empty($order_no)) $params[$k]['order_no'] = $order_no;
            }
            if(!empty($order_no) && empty($my_order_no) && in_array($order_no, $allOrders)) throws('订单[' . $order_no . '] 取消重复');
            if(!empty($order_no) && empty($my_order_no))  array_push($allOrders, $order_no);

            if(!empty($my_order_no) && in_array($my_order_no, $allMyOrders)) throws('付款单[' . $my_order_no . '] 取消重复');
            if(!empty($my_order_no))  array_push($allMyOrders, $my_order_no);

        }



        DB::beginTransaction();
        try {
            $temData = [];
            $operate_staff_id_history = 0;
            static::addOprate($temData, $operate_staff_id,$operate_staff_id_history);
            $operate_staff_id_historyArr = [];
            // 注意：只冻结，不扣除，回调扣除
            foreach($params as $v){
                $order_no = $v['order_no'] ?? '';// 订单号 -- order_no 或 my_order_no 之一不能为空
                $my_order_no = $v['my_order_no'] ?? '';// 付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
                $refund_amount = $v['refund_amount'] ?? 0;// 需要退款的金额--0为全退---单位元
                $refund_reason = $v['refund_reason'] ?? '';// 退款的原因--:为空，则后台自己组织内容
                if(empty($order_no) && empty($my_order_no) ) throws('参数order_no 或 my_order_no之一不能为空!');
                // 查询付款记录
                // $wrList = [];
                // 查询记录
                if(!empty($order_no)){// 订单的
                    // 获得订单对象
                    $queryOrderParams = [
                        'where' => [
                            ['order_type', 1],// 订单类型1普通订单/父订单4子订单
                            ['order_no', $order_no],
                            ['pay_run_price', 1],// 是否支付跑腿费0未支付1已支付
                        ],
                        /*
                        'select' => [
                            'id','title','sort_num','volume'
                            ,'operate_staff_id','operate_staff_id_history'
                            ,'created_at' ,'updated_at'
                        ],
                        */
                        //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                    ];
                    $orderInfo = OrdersDBBusiness::getInfoByQuery(1, $queryOrderParams, []);
                    if(empty($orderInfo)) throws('订单[' . $order_no . '] 记录不存在');

                    $query = $queryParams;
                    array_push($query['where'], ['pay_order_no', $order_no]);
                    if(!empty($my_order_no)){
                        array_push($query['where'], ['my_order_no', $my_order_no]);
                        $wrInfo = static::getInfoByQuery(1, $query, []);
                        if(empty($wrInfo)) return throws('付款单[' . $my_order_no . '] 记录不存在'); //记录不存在

                        if($refund_amount <= 0) $refund_amount = $wrInfo->final_amount - $wrInfo->amount_frozen;// 当前记录可退金额

                        $refundArr = static::createRefund($wrInfo ,$my_order_no, $refund_amount, $refund_reason, $company_id, $operate_staff_id, $operate_staff_id_history);
                        array_push($returnArr, $refundArr);
                    }else{
                        $isRefundAll = 0;
                        if($refund_amount <= 0) $isRefundAll = 1;// 是否全款退
                        $temWRList = static::getAllList($query, '');
                        if(is_object($temWRList) && count($temWRList) <= 0) throws('订单号[' . $order_no . '] 记录不存在'); //记录不存在
                        $refund_last_amount = 0;// 已退款金额
                        foreach($temWRList as $wrInfoObj){
                            $max_refund_amount = $wrInfoObj->final_amount - $wrInfoObj->amount_frozen;// 当前记录可退金额
                            $tem_refund_amount = ($isRefundAll == 1) ? $max_refund_amount : ( (($refund_amount - $refund_last_amount) > $max_refund_amount ) ? $max_refund_amount : ($refund_amount - $refund_last_amount));
                            $refund_last_amount += $tem_refund_amount;
                            if($tem_refund_amount > 0){// 可能有部分退过的，让过去
                                $refundArr = static::createRefund($wrInfoObj ,$wrInfoObj->my_order_no, $tem_refund_amount, $refund_reason, $company_id, $operate_staff_id, $operate_staff_id_history);
                                array_push($returnArr, $refundArr);
                            }
                        }
                    }
                    // array_push($wrList, $temWRList);
                }else{// 通过付款单号，查付款记录
                    $query = $queryParams;
                    array_push($query['where'], ['my_order_no', $my_order_no]);
                    $wrInfo = static::getInfoByQuery(1, $query, []);

                    if($refund_amount <= 0) $refund_amount = $wrInfo->final_amount - $wrInfo->amount_frozen;// 当前记录可退金额

                    $refundArr = static::createRefund($wrInfo ,$my_order_no, $refund_amount, $refund_reason, $company_id, $operate_staff_id, $operate_staff_id_history);
                    array_push($returnArr, $refundArr);
                }


            }
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
             throws($e->getMessage());
        }
        DB::commit();
        return $returnArr;
    }

    /**
     * 生成退款申请单--微信
     *
     * @param object $wrInfo 单条付款记录对象
     * @param string $my_order_no 付款单号
     * @param float $refund_amount 退款金额 > 0  ; <=0 为全款退
     * @param string $refund_reason 退款原因
     * @param int  $company_id 企业id
     * @return  array 单条退款数组
        [
            'pay_order_no' => '',// 我方的付款单号
            'refund_order_no' => '',// 我方生成的退款单号
            'pay_amount' => 0,// 我方付款的总金额[当前付款单]--单位元
            'refund_amount' => 0,// 需要退款的金额---单位元
            'config'=> [// 其它退款参数
                'refund_desc' => '',// 退款的原因
            ]
        ],
     * @author zouyan(305463219@qq.com)
     */
    public static function createRefund(&$wrInfo ,$my_order_no, $refund_amount, $refund_reason, $company_id, $operate_staff_id, $operate_staff_id_history){

        if(empty($wrInfo)) return throws('付款单[' . $my_order_no . '] 记录不存在'); //记录不存在

        //  array_push($wrList, [$wrInfo] );

        $final_amount = $wrInfo->final_amount - $wrInfo->amount_frozen;// 可退金额
        if($final_amount <= 0) throws('付款单[' . $my_order_no . '] 已经没有可退金额。');
        if($refund_amount <= 0) $refund_amount = $final_amount;
        if($refund_amount > $final_amount) throws('付款单[' . $my_order_no . '] 已经没有足够的可退金额。');
        if(empty($refund_reason)) $refund_reason = '付款单[' . $my_order_no . ']退款。';

        $refund_order_no = static::createSn($company_id , $wrInfo->staff_id, 2);

        $refundArr = [
            'pay_order_no' => $my_order_no,// 我方的付款单号
            'refund_order_no' => $refund_order_no,// 我方生成的退款单号
            'pay_amount' => $wrInfo->amount,// 我方付款的总金额[当前付款单]--单位元
            'refund_amount' => $refund_amount,// 需要退款的金额---单位元
            'config'=> [// 其它退款参数
                'refund_desc' => $refund_reason,// 退款的原因
            ]
        ];
        // 支付类型
        $operate_type = $wrInfo->operate_type;

        $lockObj = Tool::getLockRedisesLaravelObj();
        $lockState = $lockObj->lock('lock:' . Tool::getUniqueKey([Tool::getActionMethod(), __CLASS__, __FUNCTION__, $my_order_no]), 2000, 2000);//加锁
        if($lockState)
        {
            DB::beginTransaction();
            try {
                // 修改冻结金额
                $wrInfo->amount_frozen += $refund_amount;
                $wrInfo->save();
                switch($operate_type){
                    case 1:// 操作类型1充值 -  可用余额先冻结,成功减掉
                        $queryParams = [
                            'where' => [
                                ['staff_id', $wrInfo->staff_id],
                            ],
                            /**
                             * 'select' => [
                             * 'id','title','sort_num','volume'
                             * ,'operate_staff_id','operate_staff_id_history'
                             * ,'created_at' ,'updated_at'
                             * ],
                             * **/
                            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                        ];

                        // 查询记录
                        $walletInfo = WalletDBBusiness::getInfoByQuery(1, $queryParams, []);
                        if(empty($walletInfo))  throws('付款单[' . $my_order_no . '] 钱包不存在。');
                        // 校验字串   钱包
                        $oldCheckKey = WalletDBBusiness::getCheckKey($walletInfo->staff_id, $walletInfo->total_money, $walletInfo->frozen_money, $walletInfo->avail_money);
                        if ($oldCheckKey != $walletInfo->check_key) throws('钱包信息可能被篡改！请联系系统管理员');

                        $walletInfo->frozen_money +=  $refund_amount;// 冻结金额
                        $walletInfo->avail_money -=  $refund_amount;// 可用金额
                        $walletInfo->check_key = WalletDBBusiness::getCheckKey($walletInfo->staff_id, $walletInfo->total_money, $walletInfo->frozen_money, $walletInfo->avail_money);
                        $walletInfo->operate_staff_id_history = $operate_staff_id_history;
                        $walletInfo->save();

                        break;
                    case 2:// 2提现--无退款
                        throws('付款单[' . $my_order_no . '] [提现]没有退款功能。');
                        break;
                    case 3:// 3交压金/保证金--退款时已冻结，不处理，成功时减掉
                        break;
                    case 4:// 4订单付款
                    case 5:// 5追加付款--
                        // total_run_price 总跑腿费 减掉 ,
                        // 如果为0了 订单状态改为 16取消[系统取消]32取消[用户取消];--回调时
                        // has_refund 是否退费0未退费1已退费[这个]2待退费
                        // refund_price  退费--回调时
                        // refund_time 退费时间--回调时

                        // 获得订单对象
                        $order_no = $wrInfo->pay_order_no;
                        $queryOrderParams = [
                            'where' => [
                                ['order_type', 1],// 订单类型1普通订单/父订单4子订单
                                ['order_no', $order_no],
                                ['pay_run_price', 1],// 是否支付跑腿费0未支付1已支付
                            ],
                            /*
                            'select' => [
                                'id','title','sort_num','volume'
                                ,'operate_staff_id','operate_staff_id_history'
                                ,'created_at' ,'updated_at'
                            ],
                            */
                            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                        ];
                        $orderInfo = OrdersDBBusiness::getInfoByQuery(1, $queryOrderParams, []);
                        if(empty($orderInfo)) throws('订单[' . $order_no . '] 记录不存在');
                        $total_run_price = $orderInfo->total_run_price;// 总跑腿费
                        if($total_run_price < $refund_amount) throws('订单[' . $order_no . '] 总跑腿费金额不足');
                        $leave_run_price = $total_run_price - $refund_amount;

                        $orderSaveData = [
                            'total_run_price' => $total_run_price - $refund_amount,
                            'has_refund' => 2,// 是否退费0未退费1已退费2待退费
                            'cancel_time' => date("Y-m-d H:i:s",time()),// 作废时间
                        ];
                        $order_status = $orderInfo->status;// 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
                        if(!in_array($order_status, [2,4,8])) throws('订单[' . $order_no . '] 不可进行退款操作');
                        $update_type = (in_array($order_status, [2,4])) ? (1 + 2 + 4) :  (2 + 4); // 更新类型 1正在进行的订单2历史订单4 子订单
    //                    if($leave_run_price <= 0){// 订单状态变为 取消状态
    //                        if($operate_staff_id > 0){
    //                            $orderSaveData['status'] = 32;
    //                        }else{
    //                            $orderSaveData['status'] = 16;
    //                        }
    //                        $update_type = 2 + 4;
    //                        if(in_array($order_status, [2,4])) OrdersDoingDBBusiness::delDoingOrders( $order_no, $operate_staff_id , $operate_staff_id_history, '');// 删除正在进行的订单
    //                    }
                        OrdersDBBusiness::updateOrders($orderSaveData,  $order_no, $update_type, $operate_staff_id, $operate_staff_id_history
                        , '订单[' . $order_no . ']申请退款 ' . $refund_reason);

                        break;
                    case 8:// 8退款--无退款
                        throws('付款单[' . $my_order_no . '] [退款]没有退款功能。');
                        break;
                    case 16:// 16冻结--无退款
                        throws('付款单[' . $my_order_no . '] [冻结]没有退款功能。');
                        break;
                    case 32:// 32解冻--无退款
                        throws('付款单[' . $my_order_no . '] [解冻]没有退款功能。');
                        break;
                    default:
                        break;
                }

                // 生成支付记录
                $saveData = [
                    'staff_id' => $wrInfo->staff_id,// 用户id
                    'staff_id_history' => $wrInfo->staff_id_history,// 用户历史id
                    'operate_type' => 8,// 操作类型1充值2提现3交压金/保证金4订单付款5追加付款8退款16冻结32解冻
                    'pay_config_id' => $wrInfo->pay_config_id,// 支付配置ID--提现用
                    'pay_config_id_history' => $wrInfo->pay_config_id_history,// 支付配置历史ID--提现用
                    'pay_type' => $wrInfo->pay_type,// 2,// 支付方式1余额支付2微信支付
                    'pay_order_no' => $wrInfo->pay_order_no,// 支付订单号[有则填]-订单表的订单号
                    'my_order_no_old' => $wrInfo->my_order_no,// 原我方单号--与第三方对接用--如退款时，需要原交易号
                    'my_order_no' => $refund_order_no,// 我方单号--与第三方对接用
                    'third_order_no_old' => $wrInfo->third_order_no,// 原第三方单号[有则填]--如退款时，需要原交易号
                    // 'third_order_no' => 'aaa',// 第三方单号[有则填]
                    'content' => $refund_reason,// 记录内容
                    'amount' => $refund_amount,// 金额-具体金额
                    'refund_amount' => 0,// 已退费[所有退费]
                    'final_amount' => $refund_amount,// 最终剩余金额[所有退费后]
                    'amount_frozen' => $refund_amount,// 金额[冻结]-具体金额
                    // 'total_money' => 'aaa',// 总金额[操作后]
                    // 'frozen_money' => 'aaa',// 冻结金额[操作后]
                    'status' => 2,// 状态1已关闭2待确认4成功8失败
                    // 'sure_time' => 'aaa',// 确认时间
                    'operate_staff_id' => $operate_staff_id,// 操作员工id
                    'operate_staff_id_history' => $operate_staff_id_history,// 操作员工历史id
                ];
                $wr_id = 0;
                static::replaceById($saveData, $company_id, $wr_id, $operate_staff_id, 0);
            } catch ( \Exception $e) {
                DB::rollBack();
    //            throws('操作失败；信息[' . $e->getMessage() . ']');
                 throws($e->getMessage());
            }finally{
                $lockObj->unlock($lockState);//解锁
            }
            DB::commit();
        }else{
            throws('操作失败，请稍后重试!');
        }
        return $refundArr;
    }

    /**
     * 支付退款回调--微信
     *
     * @param array $reqInfo 回调的参数
        {
            "out_refund_no": "21903181737563502",// 商户退款单号
            "out_trade_no": "119109471350010",// 商户订单号
            "refund_account": "REFUND_SOURCE_RECHARGE_FUNDS",// 退款资金来源
            REFUND_SOURCE_RECHARGE_FUNDS 可用余额退款/基本账户
            REFUND_SOURCE_UNSETTLED_FUNDS 未结算资金退款
            "refund_fee": "1",// 申请退款金额  退款总金额,单位为分
            "refund_id": "50000009922019031808811970746",// 微信退款单号
            "refund_recv_accout": "工商银行借记卡6959",// 退款入账账户
                                        1）退回银行卡：
                                        {银行名称}{卡类型}{卡尾号}
                                        2）退回支付用户零钱:
                                        支付用户零钱
                                        3）退还商户:
                                        商户基本账户
                                        商户结算银行账户
                                        4）退回支付用户零钱通:
                                        支付用户零钱通
            "refund_request_source": "API",// 退款发起来源   API接口  VENDOR_PLATFORM商户平台
            "refund_status": "SUCCESS",// 退款状态  SUCCESS-退款成功   CHANGE-退款异常   REFUNDCLOSE—退款关闭
            "settlement_refund_fee": "1",// 退款金额  退款金额=申请退款金额-非充值代金券退款金额，退款金额<=申请退款金额
            "settlement_total_fee": "5",// 应结订单金额  当该订单有使用非充值券时，返回此字段。应结订单金额=订单金额-非充值代金券金额，应结订单金额<=订单金额。
            "success_time": "2019-03-18 17:38:37",// 退款成功时间  资金退款至用户帐号的时间，格式2017-12-15 09:46:01
            "total_fee": "5",// 订单金额 订单总金额，单位为分，只能为整数
            "transaction_id": "4200000279201903189120405440" // 微信订单号
        }
     * @return  mixed string throws 错误，请再通知我  正常返回 内容 不用通知我了
     * @author zouyan(305463219@qq.com)
     */
    public static function refundWXNotify($reqInfo){

        try{
            // 查询订单
            $out_refund_no = $reqInfo['out_refund_no'] ?? '';// 我方退款单号--与第三方对接用
            $out_refund_no = trim($out_refund_no);
            if(empty($out_refund_no)) throws('参数out_refund_no不能为空!');

            $refund_id = $reqInfo['refund_id'] ?? '';// 第三方退款单号[有则填]
            $refund_id = trim($refund_id);
            if(empty($refund_id)) throws('参数refund_id不能为空!');
            $refund_recv_accout = $reqInfo['refund_recv_accout'] ?? '';// 退款入账账户
            // 查询退款单
            $queryParams = [
                'where' => [
                    ['my_order_no', $out_refund_no],
                    ['operate_type', 8],
                ],
                /*
                'select' => [
                    'id','title','sort_num','volume'
                    ,'operate_staff_id','operate_staff_id_history'
                    ,'created_at' ,'updated_at'
                ],
                */
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            // 查询记录
            $wrInfo = static::getInfoByQuery(1, $queryParams, []);
            if(empty($wrInfo)) return '记录不存在';// return 1; //记录不存在

            $status = $wrInfo->status;// 状态1已关闭2待确认4成功8失败
            if(in_array($status, [1,4,8])) return '记录已关闭或成功或失败';// return 1;// 已关闭或成功或失败

            // 获得付款单记录
            $out_pay_no = $wrInfo->my_order_no_old;
            $queryParams = [
                'where' => [
                    ['my_order_no', $out_pay_no],
                    ['status', 4],
                ],
                /*
                'select' => [
                    'id','title','sort_num','volume'
                    ,'operate_staff_id','operate_staff_id_history'
                    ,'created_at' ,'updated_at'
                ],
                */
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            // 查询记录
            $wrPayInfo = static::getInfoByQuery(1, $queryParams, []);
            if(empty($wrPayInfo)) return '付款记录不存在';// return 1; //付款记录不存在


        } catch ( \Exception $e) {
            // throws('失败；信息[' . $e->getMessage() . ']');
            // return $e->getMessage();// $fail($e->getMessage());
            throws($e->getMessage());
        }
        $wrInfo->third_order_no = $refund_id;// 第三方退款单号
        $refund_status = $reqInfo['refund_status'];

        $result = '';
        try {
            $result = static::refundWXResult($wrInfo, $wrPayInfo, $refund_status, 1, $refund_recv_accout);
        } catch ( \Exception $e) {
            $errMsg = $e->getMessage();
            throws($errMsg);
        }
        return $result; // 1
//
//            $order->save(); // 保存订单

//            return true; // 返回处理完成
    }

    /**
     * 申请退款失败--微信
     *
     * @param string $out_refund_no 我方退款单号--与第三方对接用
     * @param string refund_status 退款状态  SUCCESS-退款成功   CHANGE-退款异常   REFUNDCLOSE—退款关闭
     * @param string $return_msg 失败原因
     * @param int  $company_id 企业id
     * @param int $operate_staff_id 操作人id
     * @return  mixed 单条退款数组
     * @author zouyan(305463219@qq.com)
     */
    public static function refundApplyWXFail($out_refund_no, $refund_status, $return_msg, $company_id, $operate_staff_id){
        $result = '';
        try {
            // 查询订单
            // $out_refund_no = $reqInfo['out_refund_no'] ?? '';// 我方退款单号--与第三方对接用
            $out_refund_no = trim($out_refund_no);
            if(empty($out_refund_no)) throws('参数out_refund_no不能为空!');

            // 查询支付单
            $queryParams = [
                'where' => [
                    ['my_order_no', $out_refund_no],
                    ['operate_type', 8],
                ],
                /*
                'select' => [
                    'id','title','sort_num','volume'
                    ,'operate_staff_id','operate_staff_id_history'
                    ,'created_at' ,'updated_at'
                ],
                */
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            // 查询记录
            $wrInfo = static::getInfoByQuery(1, $queryParams, []);
            if(empty($wrInfo)) return 1; //记录不存在

            $status = $wrInfo->status;// 状态1已关闭2待确认4成功8失败
            if(in_array($status, [1,4,8])) return 1;// 已关闭或成功 8失败

            // 获得付款单记录
            $out_pay_no = $wrInfo->my_order_no_old;
            $queryParams = [
                'where' => [
                    ['my_order_no', $out_pay_no],
                    ['status', 4],
                ],
                /*
                'select' => [
                    'id','title','sort_num','volume'
                    ,'operate_staff_id','operate_staff_id_history'
                    ,'created_at' ,'updated_at'
                ],
                */
                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            // 查询记录
            $wrPayInfo = static::getInfoByQuery(1, $queryParams, []);
            if(empty($wrPayInfo)) return 1; //付款记录不存在
            $result = static::refundWXResult($wrInfo, $wrPayInfo, $refund_status, 2, $return_msg);
        } catch ( \Exception $e) {
            $errMsg = $e->getMessage();
            throws($errMsg);
        }
        return $result;
    }

    /**
     * 生成退款结果处理--微信
     *
     * @param object $wrInfo 单条退款记录对象
     * @param object $wrPayInfo 单条支付记录对象
     * @param string refund_status 退款状态  SUCCESS-退款成功   CHANGE-退款异常   REFUNDCLOSE—退款关闭
     * @param int  $refund_type 类型 1回调, 2申请返回失败
     * @param string  $refund_recv_accout 退款成功/失败，说明
     * @return  mixed 单条退款数组
     * @author zouyan(305463219@qq.com)
     */
    public static function refundWXResult(&$wrInfo, &$wrPayInfo, $refund_status, $refund_type, $refund_recv_accout = ''){
        $resultStr = '';
        $out_refund_no = $wrInfo->my_order_no;// 我方退款单号

        $lockObj = Tool::getLockRedisesLaravelObj();
        $lockState = $lockObj->lock('lock:' . Tool::getUniqueKey([Tool::getActionMethod(), __CLASS__, __FUNCTION__, $out_refund_no]), 2000, 2000);//加锁
        if($lockState)
        {
            DB::beginTransaction();
            try {
                $refund_amount = $wrInfo->amount;
                $wrInfo->content = $wrInfo->content . $refund_recv_accout;// 退款说明
                // 用户是否支付成功
                $refundStatus = 1;// 1失败  2 成功
                if ($refund_status === 'SUCCESS') { // 退款状态  SUCCESS-退款成功   CHANGE-退款异常   REFUNDCLOSE—退款关闭
                    $refundStatus = 2;// 1失败  2 成功
                    $wrInfo->status = 4;
                    $wrInfo->sure_time = date("Y-m-d H:i:s",time());

                    $wrInfo->refund_amount += $refund_amount;// 已退费[所有退费]
                    $wrInfo->final_amount -= $refund_amount;// 最终剩余金额[所有退费后]
                    $wrInfo->amount_frozen -= $refund_amount;// 冻结的

                    // 修改付款记录信息
                    $wrPayInfo->refund_amount += $refund_amount;// 已退费[所有退费]
                    $wrPayInfo->final_amount -= $refund_amount;// 最终剩余金额[所有退费后]
                    $wrPayInfo->amount_frozen -= $refund_amount;// 冻结的

                } else {
                    $wrInfo->status = 8;
                    $wrInfo->amount_frozen -= $refund_amount;// 冻结的

                    // 修改付款记录信息
                    $wrPayInfo->amount_frozen -= $refund_amount;
                }
                $wrInfo->save();
                $wrPayInfo->save();

                // 支付类型
                $operate_type = $wrPayInfo->operate_type;// 支付类型
                $operate_staff_id = $wrInfo->operate_staff_id;

                $temData = [];
                $operate_staff_id_history = 0;
                static::addOprate($temData, $operate_staff_id,$operate_staff_id_history);

                switch($operate_type){// 付款类型
                    case 1:// 操作类型1充值 -  可用余额先冻结,成功减掉
                    case 3:// 3交压金/保证金--退款时已冻结，不处理，成功时减掉
                        $queryParams = [
                            'where' => [
                                ['staff_id', $wrInfo->staff_id],
                            ],
                            /**
                             * 'select' => [
                             * 'id','title','sort_num','volume'
                             * ,'operate_staff_id','operate_staff_id_history'
                             * ,'created_at' ,'updated_at'
                             * ],
                             * **/
                            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                        ];

                        // 查询记录
                        $walletInfo = WalletDBBusiness::getInfoByQuery(1, $queryParams, []);
                        if(empty($walletInfo)){
                            if($refund_type == 1){
                                throws('退款单[' . $out_refund_no . '] 钱包不存在。', 10);// throws(1);
                            }else{
                                throws('退款单[' . $out_refund_no . '] 钱包不存在。');
                            }
                        }
                        // 校验字串   钱包
                        $oldCheckKey = WalletDBBusiness::getCheckKey($walletInfo->staff_id, $walletInfo->total_money, $walletInfo->frozen_money, $walletInfo->avail_money);
                        if ($oldCheckKey != $walletInfo->check_key){
                            if($refund_type == 1){
                                throws('钱包信息可能被篡改！请联系系统管理员', 10);// throws(1);
                            }else{
                                throws('钱包信息可能被篡改！请联系系统管理员');
                            }

                        }

                        if($operate_type == 1){// 1充值
                            if($refundStatus == 2){// 退款成功
                                $walletInfo->total_money -=  $refund_amount;//  总金额
                                $walletInfo->frozen_money -=  $refund_amount;// 冻结金额
                                // $walletInfo->avail_money -=  $refund_amount;// 可用金额
                            }else{// 失败
                                $walletInfo->frozen_money -=  $refund_amount;// 冻结金额
                                $walletInfo->avail_money +=  $refund_amount;// 可用金额
                            }
                        }else{// 3交压金/保证金
                            if($refundStatus == 2) {// 退款成功
                                $walletInfo->frozen_money -=  $refund_amount;// 冻结金额
                            }
                        }

                        $walletInfo->check_key = WalletDBBusiness::getCheckKey($walletInfo->staff_id, $walletInfo->total_money, $walletInfo->frozen_money, $walletInfo->avail_money);
                        $walletInfo->operate_staff_id_history = $operate_staff_id_history;
                        $walletInfo->save();
                        break;
                    case 2:// 2提现--无退款
                        if($refund_type == 1){
                            throws('退款单[' . $out_refund_no . '] [提现]没有退款功能。', 10);// throws(1);
                        }else{
                            throws('退款单[' . $out_refund_no . '] [提现]没有退款功能。');
                        }
                        break;
                    case 4:// 4订单付款
                    case 5:// 5追加付款--
                        // total_run_price 总跑腿费 减掉 ,
                        // 如果为0了 订单状态改为 16取消[系统取消]32取消[用户取消];
                        // has_refund 是否退费0未退费1已退费[这个]2待退费
                        // refund_price  退费--回调时
                        // refund_time 退费时间--回调时

                        // 获得订单对象
                        $order_no = $wrInfo->pay_order_no;
                        $queryOrderParams = [
                            'where' => [
                                ['order_type', 1],// 订单类型1普通订单/父订单4子订单
                                ['order_no', $order_no],
                                ['pay_run_price', 1],// 是否支付跑腿费0未支付1已支付
                            ],
                            /*
                            'select' => [
                                'id','title','sort_num','volume'
                                ,'operate_staff_id','operate_staff_id_history'
                                ,'created_at' ,'updated_at'
                            ],
                            */
                            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
                        ];
                        $orderInfo = OrdersDBBusiness::getInfoByQuery(1, $queryOrderParams, []);
                        if(empty($orderInfo)){
                            if($refund_type == 1){
                                throws('订单[' . $order_no . '] 记录不存在', 10);// throws(1);
                            }else{
                                throws('订单[' . $order_no . '] 记录不存在');
                            }

                        }
                        $total_run_price = $orderInfo->total_run_price;// 总跑腿费


                        // if($total_run_price < $refund_amount) throws('订单[' . $order_no . '] 总跑腿费金额不足');
                        // $leave_run_price = $total_run_price - $refund_amount;
                        $order_status = $orderInfo->status;// 状态1待支付2等待接单4取货或配送中8订单完成16取消[系统取消]32取消[用户取消]64作废[非正常完成]
                        // 特别说明，回调时不要做此判断，因为如果多次支付，全退时，第一次回调就已经变状态了取消了，后面的回调就会卡在此处
                        // if(!in_array($order_status, [2,4,8]))  throws('订单[' . $order_no . '] 不可进行退款操作');
                        $update_type = (in_array($order_status, [2,4])) ? (1 + 2 + 4) :  (2 + 4); // 更新类型 1正在进行的订单2历史订单4 子订单
                        if($refundStatus == 2){// 退款成功
                            $orderSaveData = [
                                // 'total_run_price' => $total_run_price - $refund_amount,
                                // 'has_refund' => 1,// 是否退费0未退费1已退费2待退费
                                'refund_price' =>$orderInfo->refund_price + $refund_amount,// 退费
                                'refund_time' =>  date("Y-m-d H:i:s",time()),// 退费时间
                            ];
                            if($wrPayInfo->amount_frozen <= 0){// 冻结<=0,才改是否退费状态
                                $orderSaveData['has_refund'] = 1;// 是否退费0未退费1已退费2待退费
                            }
                            if($total_run_price <= 0 && $wrPayInfo->amount_frozen <= 0){// 订单状态变为 取消状态  // 冻结<=0,才改状态
                                // 更新订单饱和度
                                CityDBBusiness::cityOrdersOperate($orderInfo->city_site_id, 1, 1);// 减订单
                                if($operate_staff_id > 0){
                                    $orderSaveData['status'] = 32;
                                }else{
                                    $orderSaveData['status'] = 16;
                                }
                                $update_type = 2 + 4;
                                if(in_array($order_status, [2,4])) OrdersDoingDBBusiness::delDoingOrders( $order_no, $operate_staff_id , $operate_staff_id_history, '');// 删除正在进行的订单
                            }
                            OrdersDBBusiness::updateOrders($orderSaveData,  $order_no, $update_type, $operate_staff_id, $operate_staff_id_history
                                , '订单[' . $order_no . ']退款成功!' . $refund_recv_accout );

                        }else {// 失败
                            $orderSaveData = [
                                'total_run_price' => $total_run_price + $refund_amount,
                                // 'has_refund' => 0,// 是否退费0未退费1已退费2待退费
                            ];
                            if($wrPayInfo->amount_frozen <= 0 && $orderInfo->refund_price <= 0 ){// 冻结<=0,才改是否退费状态
                                $orderSaveData['has_refund'] = 0;// 是否退费0未退费1已退费2待退费
                            }
                            OrdersDBBusiness::updateOrders($orderSaveData,  $order_no, $update_type, $operate_staff_id, $operate_staff_id_history
                                , '订单[' . $order_no . ']退款失败!' . $refund_recv_accout);
                        }
                        break;
                    case 8:// 8退款--无退款
                        if($refund_type == 1){
                            throws('退款单[' . $out_refund_no . '] [退款]没有退款功能。', 10);/// throws(1);
                        }else{
                            throws('退款单[' . $out_refund_no . '] [退款]没有退款功能。');
                        }
                        break;
                    case 16:// 16冻结--无退款
                        if($refund_type == 1){
                            throws('退款单[' . $out_refund_no . '] [冻结]没有退款功能。', 10); // throws(1);
                        }else{
                            throws('退款单[' . $out_refund_no . '] [冻结]没有退款功能。');
                        }
                        break;
                    case 32:// 32解冻--无退款
                        if($refund_type == 1){
                            throws('退款单[' . $out_refund_no . '] [解冻]没有退款功能。', 10);// throws(1);
                        }else{
                            throws('退款单[' . $out_refund_no . '] [解冻]没有退款功能。');
                        }
                        break;
                    default:
                        break;
                }

            } catch ( \Exception $e) {
                DB::rollBack();
                $errMsg = $e->getMessage();
                $code = $e->getCode();
                if($code == 10){
                    $resultStr = $errMsg;
                    return $resultStr;
                }else{
    //                throws('操作失败；信息[' . $e->getMessage() . ']');
                    throws($e->getMessage());
                }
    //            if(is_numeric($errMsg) || $errMsg == 1){
    //
    //            }else{
    //                DB::rollBack();
    ////                throws('操作失败；信息[' . $e->getMessage() . ']');
    //                 throws($e->getMessage());
    //            }
            }finally{
                $lockObj->unlock($lockState);//解锁
            }
            DB::commit();
        }else{
            throws('操作失败，请稍后重试!');
        }
        return $resultStr;
    }
}
