<?php

namespace App\Http\Controllers\WX;

use App\Business\Controller\API\RunBuy\CTAPIOrdersBusiness;
use App\Business\Controller\API\RunBuy\CTAPIOrdersDoingBusiness;
use App\Services\pay\weixin\easyWechatPay;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PayController extends BaseController
{

    //  统一下单 -- 根据订单号
    public function unifiedorderByNo(Request $request)
    {
        $this->InitParams($request);
        $order_no = CommonRequest::get($request, 'order_no');
        if(empty($order_no)) throws('订单号不能为空!');
        // 日志
//        $requestLog = [
//            'files'       => $request->file(),
//            'posts'  => $request->post(),
//            'input'      => $request->input(),
//            'post_data' => apiGetPost(),
//        ];
//        Log::info('微信支付日志' . __FUNCTION__,$requestLog);
        // $url = config('public.wxPayURL') . 'pay/unifiedorder';

        $userInfo = $this->user_info;

        // 根据订单号查询订单

        $user_id = $this->user_id;

        $queryParams = [
            'where' => [
                ['order_type', '=', 1],
                ['staff_id', '=', $user_id],
                ['order_no', '=', $order_no],
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
            // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
        ];
        $resultDatas = CTAPIOrdersDoingBusiness::getInfoByQuery($request, $this, '', $this->company_id, $queryParams);

        if(empty($resultDatas)) throws('订单信息不存在!');
        $pay_run_price = $resultDatas['pay_run_price'] ?? '';// 是否支付跑腿费0未支付1已支付
        if($pay_run_price == 1) throws('订单已支付!');
        $total_run_price = $resultDatas['total_run_price'];
        $total_run_price = ceil($total_run_price * 100);

        // 生成订单号
        // 重新发起一笔支付要使用原订单号，避免重复支付；已支付过或已调用关单、撤销（请见后文的API列表）的订单号不能重新发起支付。--支付未成功的订单号，可以重新发起支付
        $orderNum = $order_no;// CTAPIOrdersBusiness::createSn($request, $this, 1);

        $app = app('wechat.payment');
        $params = [
            'body' => config('public.webName') . '-跑腿订单[' . $order_no . ']服务费',
            'out_trade_no' => $orderNum,
            'total_fee' => $total_run_price,
            // 'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            // 'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $userInfo['mini_openid'], // 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
        ];
        try{
            $result = easyWechatPay::miniProgramunify($app, $params, 1);
        } catch ( \Exception $e) {
            throws('失败；信息[' . $e->getMessage() . ']');
        }

        // 去掉敏感信息
        Tool::formatArrKeys($result, Tool::arrEqualKeyVal(['timeStamp', 'nonceStr', 'package', 'signType', 'paySign']), true );
        return ajaxDataArr(1, $result, '');
    }

    //  统一下单--- 测试支付用
    public function unifiedorder(Request $request)
    {
         $this->InitParams($request);
        // 日志
        $requestLog = [
            'files'       => $request->file(),
            'posts'  => $request->post(),
            'input'      => $request->input(),
            'post_data' => apiGetPost(),
        ];
        Log::info('微信支付日志' . __FUNCTION__,$requestLog);
        // $url = config('public.wxPayURL') . 'pay/unifiedorder';

        $userInfo = $this->user_info;

        // 查询退款单
//        try{
//        $app = app('wechat.payment');
//         $result = easyWechatPay::queryByOutRefundNumber($app, '21903181737563502');
        // $result = easyWechatPay::queryByRefundId($app, '50000009922019031808811970746');
//         $result = easyWechatPay::queryRefundByOutTradeNumber($app, '119109471350010');
//         $result = easyWechatPay::queryRefundByTransactionId($app, '4200000279201903189120405440');
        //return ajaxDataArr(1, $result, '');
//        } catch ( \Exception $e) {
//            throws('失败；信息[' . $e->getMessage() . ']');
//        }

        // 生成订单号
        // 重新发起一笔支付要使用原订单号，避免重复支付；已支付过或已调用关单、撤销（请见后文的API列表）的订单号不能重新发起支付。--支付未成功的订单号，可以重新发起支付
        $orderNum = CTAPIOrdersBusiness::createSn($request, $this, 1);

        $app = app('wechat.payment');
        $params = [
             'body' => '测试支付',
             'out_trade_no' => $orderNum,
             'total_fee' => 1,
             // 'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
             // 'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
             'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
             'openid' => $userInfo['mini_openid'], // 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
        ];
        try{
            $result = easyWechatPay::miniProgramunify($app, $params, 1);
        } catch ( \Exception $e) {
            throws('失败；信息[' . $e->getMessage() . ']');
        }

        // 去掉敏感信息
        Tool::formatArrKeys($result, Tool::arrEqualKeyVal(['timeStamp', 'nonceStr', 'package', 'signType', 'paySign']), true );
        return ajaxDataArr(1, $result, '');
    }

    //  退单测试
    public function refundOrder(Request $request)
    {
        $this->InitParams($request);

        // 生成退订单号
        $refundOrderNum = CTAPIOrdersBusiness::createSn($request, $this, 2);

        $app = app('wechat.payment');
        $out_trade_no = '119109471350010';
        $transactionId = '4200000279201903189120405440';
        $refundNumber = $refundOrderNum;
        $totalFee = 5;
        $refundFee = 1;
        $config = [
            'refund_desc' => '测试退款',// 退款原因 若商户传入，会在下发给用户的退款消息中体现退款原因  ；注意：若订单退款金额≤1元，且属于部分退款，则不会在退款消息中体现退款原因
            'notify_url' => config('public.wxNotifyURL') . 'api/pay/refundNotify' ,// 退款结果通知的回调地址
        ];

        try{
            // 根据商户订单号退款
            $result = easyWechatPay::refundByOutTradeNumber($app, $out_trade_no, $refundNumber, $totalFee, $refundFee, $config);
            // 根据微信订单号退款
            // $result = easyWechatPay::refundByTransactionId($app, $transactionId, $refundNumber, $totalFee, $refundFee, $config);
        } catch ( \Exception $e) {
            throws('失败；信息[' . $e->getMessage() . ']');
        }


        return ajaxDataArr(1, $result, '');
    }
//
//    //  支付结果通知--回调
//    public function wechatNotify(Request $request)
//    {
//        // $this->InitParams($request);
//        // 日志
//        $requestLog = [
//            'files'       => $request->file(),
//            'posts'  => $request->post(),
//            'input'      => $request->input(),
//            'post_data' => apiGetPost(),
//        ];
//        Log::info('微信支付日志 回调' . __FUNCTION__,$requestLog);
//        $app = app('wechat.payment');
//        /* $message 的内容
//        {
//            "appid": "wxcb82783fe211782f",
//            "bank_type": "CFT",// 银行类型
//            "cash_fee": "1",// 现金
//            "fee_type": "CNY",// 币种
//            "is_subscribe": "N",// 是否订阅
//            "mch_id": "1527642191",
//            "nonce_str": "5c8e67b1d9bc3",
//            "openid": "owfFF4ydu2HmuvmSDS4goIoAIYEs",
//            "out_trade_no": "119108029350007",
//            "result_code": "SUCCESS",// 支付结果 FAIL:失败;SUCCESS:成功
//            "return_code": "SUCCESS",// 表示通信状态: SUCCESS 成功
//            "sign": "C6ACF2C7C8AF999048094ED2264F0ABC",
//            "time_end": "20190317232919",// 交易时间
//            "total_fee": "1",// 交易金额
//            "trade_type": "JSAPI",// 交易类型
//            "transaction_id": "4200000288201903177135850941"// 交易号
//        }
//        */
//        $response = $app->handlePaidNotify(function($message, $fail) use(&$app) {
//
//            Log::info('微信支付日志 $message' . __FUNCTION__, [$message]);
//            Log::info('微信支付日志 $fail' . __FUNCTION__, [$fail]);
//            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
//
//            try{
//                // 查询订单
//                $out_trade_no = $message['out_trade_no'] ?? '';
//                // Log::info('微信支付日志 $order' . __FUNCTION__, [$out_trade_no]);
//                if(!empty($out_trade_no)){
//                    $queryResult = easyWechatPay::queryByOutTradeNumber($app, $out_trade_no);
//                }
//
//                // 根据微信订单号查询
//    //            $transaction_id = $message['transaction_id'] ?? '';
//    //            if(!empty($out_trade_no)) {
//    //                $queryResult = easyWechatPay::queryByTransactionId($app, $transaction_id);
//    //            }
//            } catch ( \Exception $e) {
//                // throws('失败；信息[' . $e->getMessage() . ']');
//                return $fail($e->getMessage());
//            }
//
//
//            return true;
//
////            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
////                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
////            }
//
////            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
////
////            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
////                // 用户是否支付成功
////                if (array_get($message, 'result_code') === 'SUCCESS') {
////                    $order->paid_at = time(); // 更新支付时间为当前时间
////                    $order->status = 'paid';
////
////                    // 用户支付失败
////                } elseif (array_get($message, 'result_code') === 'FAIL') {
////                    $order->status = 'paid_fail';
////                }
////            } else {
////                return $fail('通信失败，请稍后再通知我');
////            }
////
////            $order->save(); // 保存订单
//
////            return true; // 返回处理完成
//        });
//        return $response;//return $response->send();
//        // return ajaxDataArr(1, 'wechatNotify', '');
//    }

    //  支付结果通知--回调
    public function wechatNotify(Request $request)
    {
        // $this->InitParams($request);
        // 日志
        $requestLog = [
            'files'       => $request->file(),
            'posts'  => $request->post(),
            'input'      => $request->input(),
           // 'post_data' => apiGetPost(),
        ];
        Log::info('微信支付日志 回调' . __FUNCTION__,$requestLog);
        $app = app('wechat.payment');
        /* $message 的内容
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
        */
        $response = $app->handlePaidNotify(function($message, $fail) use(&$request, &$app) {

            Log::info('微信支付日志 $message' . __FUNCTION__, [$message]);
            Log::info('微信支付日志 $fail' . __FUNCTION__, [$fail]);
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单

            try{
                // 查询订单
                $out_trade_no = $message['out_trade_no'] ?? '';
                $transaction_id = $message['transaction_id'] ?? '';
                $queryParams = [
                    'where' => [
                        ['order_type', '=', 1],
                        // ['staff_id', '=', $user_id],
                        ['order_no', '=', $out_trade_no],
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
                    // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
                ];
                $resultDatas = CTAPIOrdersDoingBusiness::getInfoByQuery($request, $this, '', $this->company_id, $queryParams);

                Log::info('微信支付日志 $resultDatas' . __FUNCTION__, [$resultDatas]);
                $orderDatas = CTAPIOrdersBusiness::getInfoByQuery($request, $this, '', $this->company_id, $queryParams);

                Log::info('微信支付日志 $orderDatas' . __FUNCTION__, [$orderDatas]);
                if(empty($resultDatas)) return true;// return $fail('订单信息不存在!');
                $pay_run_price = $resultDatas['pay_run_price'] ?? '';// 是否支付跑腿费0未支付1已支付
                if($pay_run_price == 1) return true;// 订单已支付
                // Log::info('微信支付日志 $order' . __FUNCTION__, [$out_trade_no]);
                if(!empty($out_trade_no)){
                    $queryResult = easyWechatPay::queryByOutTradeNumber($app, $out_trade_no);
                }

                // 根据微信订单号查询
                //            $transaction_id = $message['transaction_id'] ?? '';
                //            if(!empty($out_trade_no)) {
                //                $queryResult = easyWechatPay::queryByTransactionId($app, $transaction_id);
                //            }
            } catch ( \Exception $e) {
                // throws('失败；信息[' . $e->getMessage() . ']');
                return $fail($e->getMessage());
            }

//            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
//                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
//            }

//            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
//
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                $saveData = [] ;
                $id = $resultDatas['id'];
                $order_id = $orderDatas['id'];
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
//                    $order->paid_at = time(); // 更新支付时间为当前时间
//                    $order->status = 'paid';
                    $saveData['status'] = 2;
                    $saveData['pay_run_price'] = 1;
                    $saveData['pay_order_no'] = $transaction_id;
                    $saveData['pay_time'] = date("Y-m-d H:i:s",time());

                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
//                    $order->status = 'paid_fail';
                    $saveData['pay_run_price'] = 4;
                }

                try{
                    $resultDatas = CTAPIOrdersDoingBusiness::replaceById($request, $this, $saveData, $id, false, 1);
                    $resultOrder = CTAPIOrdersBusiness::replaceById($request, $this, $saveData, $order_id, false, 1);

                } catch ( \Exception $e) {
                    // throws('失败；信息[' . $e->getMessage() . ']');
                    return $fail($e->getMessage());
                }
                Log::info('微信支付日志 $orderDatas' . __FUNCTION__, [$saveData, $resultDatas, $resultOrder ]);
                return true;
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
//
//            $order->save(); // 保存订单

//            return true; // 返回处理完成
        });
        return $response;//return $response->send();
        // return ajaxDataArr(1, 'wechatNotify', '');
    }


    //  退款结果通知--回调
    public function refundNotify(Request $request)
    {
        // $this->InitParams($request);
        // 日志
        $requestLog = [
            'files'       => $request->file(),
            'posts'  => $request->post(),
            'input'      => $request->input(),
            'post_data' => apiGetPost(),
        ];
        Log::info('微信支付日志 退款结果通知--回调' . __FUNCTION__,$requestLog);
        $app = app('wechat.payment');
        /**  $message
        {
            "return_code": "SUCCESS",// 返回状态码  SUCCESS/FAIL  此字段是通信标识，非交易标识，交易是否成功需要查看trade_state来判断
                return_msg  当return_code为FAIL时返回信息为错误原因 ，例如  签名失败  参数格式校验错误
            "appid": "wxcb82783fe211782f",
            "mch_id": "1527642191",
            "nonce_str": "da42c1396000ea4d42c7714e4c6cf19d",
            "req_info": "uTmREqV8NkWXBdG32TJhJlA2LGVzCROHjGnKaIYfPNCjovTEeQtNQljda8RthGNbg6efS2qx3zf79vg4ORX5JMfYLa2YWBOpYvhjK1RovUpAgjcLiyqdx7Dgd2yyn6uBmu1Kp/1qJWEIlP7ctmAFYw+l2Xa9OXdKDkNdn0PX4RjJA8Npvg92pyGclbLKRoxWjnWhrmofJbDmRVrFQfIGTxzAj0JXrIPcbhGs+ybIAD0D3DAi5i51KL5dndw9YS0s5C2wgCEFkHNpYHRfsLY/XD6XMAvqKPUYNSQuAd3f35lDUfdOUuse3zce2kVjjJW/HueA22MVJb/Fs/zHElFp+/vR2hubi4zgb4pmffLHTCh6O/0o+zCG4v6lyPkfv07t+uG34tM63Z/pgoYF6FVca9YcrYEOP4IuZf9hskEProY6lvCoNn0pcOTUYjfeuZ2iKWJLmbOApkCiyg5yND1KlmBHRbHVocZOLq03s55PD459uFc3Nkn9eXRzCOWLu+jCsaqvaSCG5WMAv19RMiq/rQRRv0adFGCE5thxfcDDPutLHzAKUAw3V72QflCLFZe+M6p/psLux8Ssu4SV+od20kAbZjXTLYyKoeJ3oAu/aufgt9ndxasP+bGH+mnEg8gGUWrHbBYz2fBAZK3ASYlMevIAW1/dqfS2405FRCZWAlp6NlVDsNEcD3HRu0bjY4nMF0hoLFio225jzYb2VMei/WwLUx7XHH+9dGZL7JJuVj+oUjwmce3CwXhJ4rpZH/aYpByq3mFHgkTeazl4i6TUvUAYj1INon1Kk111IPFDNjOxmFP8hQ1+VOETMlHrtLRnw3AXZk1Z9EjbJqgA4cnyvEfScKSWvTDOgxXwtkGlmBAZh59tTEKg+eu4Th8jtdP54VF8xnbUJTFRXNuLC2/HjalSjeGSjgaIh5/moD78ZvBYXBKQ5iNEwaEx8st2DZhxGvJCrcB6bfci7v2iMuU6GaRY5YRaQDyJl5d/22vWMYqEFmmXuMduxxaHaWL/DZvN5l1jsd6n8HKlK6ef/HxPTSsSRlmmRnpEJYgCgqrPmNE="
        }
         */
        /**   $reqInfo
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
         **/

        $response = $app->handleRefundedNotify(function ($message, $reqInfo, $fail) {
            Log::info('微信支付日志 退款结果通知--回调$message' . __FUNCTION__, [$message]);
            Log::info('微信支付日志 退款结果通知--回调$reqInfo' . __FUNCTION__, [$reqInfo]);
            Log::info('微信支付日志 退款结果通知--回调 $fail' . __FUNCTION__, [$fail]);
            // 其中 $message['req_info'] 获取到的是加密信息
            // $reqInfo 为 message['req_info'] 解密后的信息
            // 你的业务逻辑...
            return true; // 返回 true 告诉微信“我已处理完成”
            // 或返回错误原因 $fail('参数格式校验错误');
        });
        return $response;// $response->send();
        // return ajaxDataArr(1, 'wechatNotify', '');
    }

    //  扫码支付通知
    public function sweepCodePayNotify(Request $request)
    {
        // $this->InitParams($request);
        // 日志
        $requestLog = [
            'files'       => $request->file(),
            'posts'  => $request->post(),
            'input'      => $request->input(),
            'post_data' => apiGetPost(),
        ];
        Log::info('微信支付日志 扫码支付通知' . __FUNCTION__,$requestLog);
        $app = app('wechat.payment');
        // 扫码支付通知接收第三个参数 `$alert`，如果触发该函数，会返回“业务错误”到微信服务器，触发 `$fail` 则返回“通信错误”
        $response = $app->handleScannedNotify(function ($message, $fail, $alert) use ($app) {
            Log::info('微信支付日志 退款结果通知--回调$message' . __FUNCTION__, [$message]);
            Log::info('微信支付日志 退款结果通知--回调 $fail' . __FUNCTION__, [$fail]);
            Log::info('微信支付日志 退款结果通知--回调$alert' . __FUNCTION__, [$alert]);
            // 如：$alert('商品已售空');
            // 如业务流程正常，则要调用“统一下单”接口，并返回 prepay_id 字符串，代码如下
            $result = $app->order->unify([
                'trade_type' => 'NATIVE',
                'product_id' => $message['product_id'],
                // ...
            ]);

            return $result['prepay_id'];
        });

        return $response;// $response->send();
    }

}
