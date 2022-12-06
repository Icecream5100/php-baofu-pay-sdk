<?php

namespace Ice\Pay\Server\XinRuiTai\Order;

use Ice\Pay\Core\XinRuiTaiHttpCall;

use function Ice\Pay\Support\get_client_ip;

class Client
{

    /**
     * @param array $params
     * @param string $trade_type JSAPI WAP APP
     * @return array|string
     * 统一下单接口
     */
    public function unify(array $params, $trade_type = "JSAPI")
    {
        $service = "pay.weixin.jspay";
        switch ($trade_type) {
            case "JSAPI":
                $service = "pay.weixin.jspay";
                break;
            case "WAP":
                $service = "pay.weixin.wap";
                break;
            case "APP":
                $service = "unified.trade.pay";
                break;
        }
        $params = array_merge(
            [
                'service' => $service,
                'mch_create_ip' => get_client_ip(),

            ],
            $params
        );
        return XinRuiTaiHttpCall::gI()->call(
            "xrtpay/gateway",
            $params
//            [
//                'out_trade_no' => $out_trade_no,
//                'is_minipg' => %is_minipg, //值为1，表示小程序支付；不传或值不为1，表示公众账号内支付
//                'body' => $body,
//                'sub_openid' => $openid,
//                'sub_appid' => $appid,
//                'total_fee' => $total_fee,
//                'notify_url' => $notify_url,
//            ]
        );
    }


    /**
     * @param string $transactionId
     * @return array|string
     * 三方订单号查询
     */
    public function queryByTransactionId(string $transactionId)
    {
        return XinRuiTaiHttpCall::gI()->call(
            "xrtpay/gateway",
            [
                'service' => 'unified.trade.query',
                'transaction_id' => $transactionId,
            ]
        );
    }


    /**
     * @param string $out_trade_no
     * @return array
     * 商户订单号查询
     */
    public function queryByOutTradeNumber(string $out_trade_no)
    {
        return XinRuiTaiHttpCall::gI()->call(
            "xrtpay/gateway",
            [
                'service' => 'unified.trade.query',
                'out_trade_no' => $out_trade_no,
            ]
        );
    }
}
