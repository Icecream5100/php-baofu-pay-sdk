<?php

namespace Ice\Pay\Server\BaoFu\Order;

use Ice\Pay\Core\BaoFuHttpCall;
use Ice\Pay\RechargeConfig;

class Client
{


    /**
     * @param string $loginNo 登录号
     * @param string $outOrderNo 提现订单号
     * @param string $amount 金额
     * @param string $agreementNo 签约协议号
     * @param string $notifyUrl 回调地址
     * @return bool|string
     * 委任提现交易
     */
    public function entrustWithdraw(
        string $loginNo,
        string $outOrderNo,
        string $amount,
        string $agreementNo,
        string $notifyUrl
    ) {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());
        $params["outOrderNo"] = $outOrderNo;
        $params["amount"] = $amount;
        $params["notifyUrl"] = $notifyUrl;
        $params["agreementNo"] = BaoFuHttpCall::gI()->encryptByCERFile($agreementNo);
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . $params["outOrderNo"] . $MARK . $params["amount"] . $MARK . $params["agreementNo"];
        return BaoFuHttpCall::gI()->call("order/v3.0.0/entrustWithdraw", $params, $OsignDataString);
    }


    /**
     * @param array $params
     * @param array $splitInfoList 分账订单明细
     * @param string $trade_type JSAPI MINI APP
     * @return array|string
     * 统一下单接口
     */
    public function unify(array $params, array $splitInfoList, $trade_type = "JSAPI")
    {
        $paidType = "WECHAT_JSGZH";
        switch ($trade_type) {
            case "JSAPI":
                $paidType = "WECHAT_JSGZH";
                break;
            case "MINI":
                $paidType = "WECHAT_JSXCX";
                break;
            case "APP":
                $paidType = "WECHAT_APP";
                break;
        }

        $data_content_parms["loginNo"] = $params["uid"] ?? null;
        $data_content_parms["notifyUrl"] = $params["notifyUrl"] ?? "";
        $data_content_parms["chanalId"] = $params["openid"] ?? "";
        $data_content_parms["orgNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("member_id");
        $data_content_parms["merchantNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("member_id");
        $data_content_parms["terminalNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("terminal_id");
        $data_content_parms["requestDate"] = date('YmdHis', time());
        $data_content_parms["callType"] = "ASSURE_PAYMENT";
        $data_content_parms["appId"] = $params["appId"] ?? null;
        $data_content_parms["simId"] = $params["simId"] ?? null;
        $dataContent = [];//分账明细数据1
        $dataContent["paidType"] = $paidType;
        $dataContent["goodsName"] = $params["goodsName"] ?? 0;
        $dataContent["amount"] = $params["amount"] ?? 0;
        $dataContent["outOrderNo"] = $params["outOrderNo"] ?? 0;
        $dataContent["notifyUrl"] = $params["notifyUrl"] ?? "";
        $dataContent["expireDate"] = $params["expireDate"] ?? "";
        $dataContent["validDate"] = $params["validDate"] ?? "";
        $dataContent["splitInfoList"] = $splitInfoList;
        $data_content_parms["dataContent"] = str_replace("\\/", "/", json_encode($dataContent));//转JSON;
        $OsignDataString = $data_content_parms["orgNo"] . "|" . $data_content_parms["merchantNo"] . "|" . $data_content_parms["terminalNo"] . "|" . $data_content_parms["callType"] . "|" .
            $data_content_parms["loginNo"] . "|" . $data_content_parms["requestDate"] . "|" . $data_content_parms["dataContent"];
        return BaoFuHttpCall::gI()->call("wallet/v3.0.0/payment", $data_content_parms, $OsignDataString);
    }


    /**
     * @param string $loginNo 登录号
     * @param string $notifyUrl 回调地址
     * @param $params2
     * @param $assureConfirmSplitInfoList
     * @return bool|string
     * 确认分账
     */
    public function confirmAssurePay(
        string $loginNo,
        string $notifyUrl,
        $params2,
        $assureConfirmSplitInfoList
    ) {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());
        $params["notifyUrl"] = $notifyUrl;
        $params["dataContent"] = [];
        $dataContent["amount"] = $params2["amount"] ?? 0;
        $dataContent["outOrderNo"] = $params2["outOrderNo"] ?? 0;
        $dataContent["oldOutOrderNo"] = $params2["oldOutOrderNo"] ?? 0;
        $dataContent["assureConfirmSplitInfoList"] = $assureConfirmSplitInfoList;
        $params["dataContent"] = str_replace("\\/", "/", json_encode($dataContent));//转JSON;
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["loginNo"] . $MARK . $params["terminalNo"] . $MARK . $params["requestDate"] . $MARK . $params["dataContent"];
        return BaoFuHttpCall::gI()->call("wallet/v3.0.0/confirmAssurePay", $params, $OsignDataString);
    }


    /**
     * @param array $params
     * @param array $splitInfoList
     * @return mixed
     * b2b 担保支付
     */
    public function cloudB2cAssureRecharge(array $params, array $splitInfoList)
    {
        $data_content_parms["orgNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("member_id");
        $data_content_parms["merchantNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("member_id");
        $data_content_parms["terminalNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("terminal_id");
        $data_content_parms["loginNo"] = $params["loginNo"] ?? null;
        $data_content_parms["requestDate"] = date('YmdHis', time());
        $data_content_parms["payId"] = $params["payId"] ?? null;

        $dataContent = [];
        $dataContent["goodsName"] = $params["goodsName"] ?? 0;
        $dataContent["amount"] = $params["amount"] ?? 0;
        $dataContent["outOrderNo"] = $params["outOrderNo"] ?? 0;
        $dataContent["notifyUrl"] = $params["notifyUrl"] ?? "";
        $dataContent["expireDate"] = $params["expireDate"] ?? "";
        $dataContent["validDate"] = $params["validDate"] ?? "";
        $dataContent["splitInfoList"] = $splitInfoList;

        $data_content_parms["dataContent"] = str_replace("\\/", "/", json_encode($dataContent));//转JSON;
        $OsignDataString = $data_content_parms["orgNo"] . "|" . $data_content_parms["merchantNo"] . "|" . $data_content_parms["loginNo"] .
            "|" . $data_content_parms["terminalNo"] . "|" . $data_content_parms["requestDate"] . "|" . $data_content_parms["dataContent"];
        return BaoFuHttpCall::gI()->call("order/v3.0.0/cloudB2cAssureRecharge", $data_content_parms, $OsignDataString);
    }


    /**
     * @param array $params
     * @param array $splitInfoList
     * @return mixed
     * b2b 余额支付
     */
    public function assurePaymentSplit(array $params, array $splitInfoList)
    {
        $data_content_parms["orgNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("member_id");
        $data_content_parms["merchantNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("member_id");
        $data_content_parms["terminalNo"] = RechargeConfig::gI()->getBaoFuConfig()->get("terminal_id");
        $data_content_parms["loginNo"] = $params["loginNo"] ?? null;
        $data_content_parms["requestDate"] = date('YmdHis', time());
        $data_content_parms["amount"] = $params["amount"] ?? null;
        $data_content_parms["outOrderNo"] = $params["outOrderNo"] ?? null;
        $data_content_parms["paidType"] = "B_BALANCE";
        $data_content_parms["agreementNo"] = $params["agreementNo"] ?? null;
        $data_content_parms["splitInfoList"] = $splitInfoList;
        $data_content_parms["notifyUrl"] = $params["notifyUrl"] ?? "";
        $data_content_parms["expireDate"] = $params["expireDate"] ?? "";
        $data_content_parms["validDate"] = $params["validDate"] ?? "";

        $OsignDataString = $data_content_parms["orgNo"] . "|" . $data_content_parms["merchantNo"] . "|" . $data_content_parms["terminalNo"] .
            "|" . $data_content_parms["loginNo"] . "|" . $data_content_parms["requestDate"] . "|" . $data_content_parms["outOrderNo"] .
            "|" . $data_content_parms["amount"] . "|" . $data_content_parms["paidType"] . "|" . $data_content_parms["splitInfoList"];
        return BaoFuHttpCall::gI()->call("order/v3.0.0/cloudB2cAssureRecharge", $data_content_parms, $OsignDataString);
    }

}
