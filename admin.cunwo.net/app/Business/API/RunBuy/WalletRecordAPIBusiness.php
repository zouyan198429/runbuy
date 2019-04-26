<?php
// 钱包操作记录
namespace App\Business\API\RunBuy;


use App\Services\pay\weixin\easyWechatPay;
use Illuminate\Support\Facades\Log;

class WalletRecordAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'RunBuy\WalletRecord';
    public static $table_name = 'wallet_record';// 表名称


    /**
     * 申请退款--微信
     *
     * @param int $company_id 企业id
     * @param int $user_id 操作用户id
     * @param array $params 参数
        $params = [
            [
            'order_no' => '', // 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
            'my_order_no' => '',//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
            'refund_amount' => 0,// 需要退款的金额--0为全退---单位元
            'refund_reason' => '',// 退款的原因--:为空，则后台自己组织内容
            ]
        ];
     * @param int $notLog 是否需要登陆 0需要1不需要
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
    public static function refundApplyWX($company_id, $user_id, $params,  $notLog = 0)
    {
        // $company_id = $controller->company_id;
        // $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }

        // 调用新加或修改接口
        $apiParams = [
            'params' => $params,
            'company_id' => $company_id,
            'operate_staff_id' => $user_id,
        ];
        $result = static::exeDBBusinessMethodBS('', 'refundApplyWX', $apiParams, $company_id, $notLog);
        return $result;
    }


    /**
     * 申请退款--微信 --成功/失败手动修改数据
     *
     * @param int $company_id 企业id
     * @param int $user_id 操作用户id
     * @param string $out_refund_no 我方退款单号
     * @param string $refund_status 业务结果  SUCCESS/FAIL SUCCESS/FAIL  SUCCESS退款申请接收成功，结果通过退款查询接口查询  FAIL 提交业务失败
     * @param string $return_msg 失败原因
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function refundApplyWXFail($company_id, $user_id, $out_refund_no, $refund_status, $return_msg, $notLog = 0)
    {
//        $company_id = $controller->company_id;
//        $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }

        // 调用新加或修改接口
        $apiParams = [
            'out_refund_no' => $out_refund_no,
            'refund_status' => $refund_status,
            'return_msg' => $return_msg,
            'company_id' => $company_id,
            'operate_staff_id' => $user_id,
        ];
        $result = static::exeDBBusinessMethodBS('', 'refundApplyWXFail', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 单个或批量 根据订单号取消订单-- 如果是订单，调用此方法前，请注意先判断下订单状态，是否是可以进行些操作的
     *
     * @param int $company_id 企业id
     * @param int $user_id 操作用户id
     * @param array $params 需要退款的信息数组 二维
    //        $params = [
    //            [
    //                'order_no' => $order_no, // 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
    //                'my_order_no' => $my_order_no,//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
    //                'refund_amount' => $amount,// 需要退款的金额--0为全退---单位元
    //                'refund_reason' => $refund_reason,// 退款的原因--:为空，则后台自己组织内容
    //            ]
    //        ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed  $out_refund_nos 我方生成的退款单号 数组 --  一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function orderCancel($company_id = 0, $user_id = 0, $params = [], $notLog = 0)
    {

//        if(!is_numeric($amount)) $amount = 0;
//        if(!is_numeric($amount) && $amount < 0) throws('费用不能小于0!');
//
//        $params = [
//            [
//                'order_no' => $order_no, // 订单号 , 如果是订单操作必传-- order_no 或 my_order_no 之一不能为空
//                'my_order_no' => $my_order_no,//付款 我方单号--与第三方对接用 -- order_no 或 my_order_no 之一不能为空
//                'refund_amount' => $amount,// 需要退款的金额--0为全退---单位元
//                'refund_reason' => $refund_reason,// 退款的原因--:为空，则后台自己组织内容
//            ]
//        ];

        Log::info('微信支付日志 退款申请-->' . __FUNCTION__,$params);
        $out_refund_nos = [];
        try{
            $returnArr = static::refundApplyWX($company_id, $user_id, $params,  $notLog);
            Log::info('微信支付日志 退款申请返回参数-->' . __FUNCTION__,$returnArr);
            $config = [
                'refund_desc' => '',// $refund_desc,//'测试退款',// 退款原因 若商户传入，会在下发给用户的退款消息中体现退款原因  ；注意：若订单退款金额≤1元，且属于部分退款，则不会在退款消息中体现退款原因
                'notify_url' => config('public.wxNotifyURL') . 'api/pay/refundNotify' ,// 退款结果通知的回调地址
            ];
            Log::info('微信支付日志 退款申请参数config-->' . __FUNCTION__,$config);
            // 根据商户订单号退款
            $app = app('wechat.payment');
            foreach($returnArr as $v){
                $out_refund_no = $v['refund_order_no'];//  我方生成的退款单号
                $pay_order_no = $v['pay_order_no'];//我方的付款单号

                $out_trade_no =  $pay_order_no;//我方的付款单号
                $refundNumber = $out_refund_no;//我方生成的退款单号
                $totalFee = floor($v['pay_amount'] * 100);//我方付款的总金额[当前付款单]--单位元
                $refundFee = floor($v['refund_amount'] * 100);// 需要退款的金额---单位元
                $refund_desc = $v['config']['refund_desc'];// 其它退款参数  退款的原因
                $config['refund_desc'] = $refund_desc ;
                $result = easyWechatPay::refundByOutTradeNumber($app, $out_trade_no, $refundNumber, $totalFee, $refundFee, $config);
                // $result['result_code'] = 'FAIL';
                Log::info('微信支付日志 退款申请返回结果-->' . __FUNCTION__,[$result]);
                // 业务结果  SUCCESS/FAIL SUCCESS/FAIL  SUCCESS退款申请接收成功，结果通过退款查询接口查询  FAIL 提交业务失败
                $result_code = $result['result_code'];
                if($result_code != 'SUCCESS'){// FAIL 提交业务失败,回退
                    $return_msg = $result['return_msg'] ?? '';// 失败原因
                    $err_code = $result['err_code'] ?? '';// 错误代码
                    $err_code_des = $result['err_code_des'] ?? '';// 错误代码描述
                    $errMsg = '错误代码[' . $err_code . '];错误代码描述[' . $err_code_des . ']';
                    $resultFail = static::refundApplyWXFail($company_id, $user_id, $out_refund_no, $result_code, $errMsg, $notLog);
                    Log::info('微信支付日志 退款申请业务失败回退$resultFail-->' . __FUNCTION__,[$resultFail]);
                    throws('退款申请失败:' . $errMsg);
                }else{// 成功，查询是否成功
                    // 重试 3次 6秒
//                    $queryNum = 0;
//                    while(true)   #循环获取锁
//                    {
//                        $queryNum++;
//                        $delay = mt_rand(2 * 1000 * 1000, 3 * 1000 * 1000);
//                        usleep($delay);//usleep($delay * 1000);

//                        $resultQuery = easyWechatPay::queryByOutRefundNumber($app, $out_refund_no);
//                        Log::info('微信支付日志 退款结果查询情况$resultQuery-->' . __FUNCTION__,[$resultQuery]);
//                        // 如果成功，则修改退款单为成功
//                        $quest_result_code = $resultQuery['result_code'] ?? '';
//                        $quest_refund_status = $resultQuery['refund_status_0'] ?? '';
//                        Log::info('微信支付日志 退款结果查询情况 $quest_result_code-->' . __FUNCTION__,[$quest_result_code]);
//                        Log::info('微信支付日志 退款结果查询情况 $quest_refund_status-->' . __FUNCTION__,[$quest_refund_status]);
//                        if($quest_result_code == 'SUCCESS' && $quest_refund_status == 'SUCCESS' ) {
//                            $quest_return_msg = $resultQuery['return_msg'] ?? '';// 失败原因
//                            $resultSuccess = CTAPIWalletRecordBusiness::refundApplyWXFail($request, $controller, $out_refund_no, $quest_refund_status, $quest_return_msg);
//                            Log::info('微信支付日志 退款申请业务成功自动更新记录-->' . __FUNCTION__,[$resultSuccess]);
//                        }
//                        if($quest_refund_status == 'SUCCESS' || $queryNum >= 3) break;
//                    }

                }
                array_push($out_refund_nos, $out_refund_no);
                // 根据微信订单号退款
                // $result = easyWechatPay::refundByTransactionId($app, $transactionId, $refundNumber, $totalFee, $refundFee, $config);
            }
        } catch ( \Exception $e) {
            throws('失败；信息[' . $e->getMessage() . ']');
        }
        return $out_refund_nos;
    }

 }