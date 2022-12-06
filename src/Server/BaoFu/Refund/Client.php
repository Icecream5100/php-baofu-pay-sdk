<?php

namespace Ice\Pay\Server\BaoFu\Refund;

use Ice\Pay\Core\BaoFuHttpCall;
use Ice\Pay\RechargeConfig;

class Client
{


    /**
     * @param string $loginNo 登录号
     * @param string $refundTradeId
     * @param string $orgTradeId
     * @param int $refundAmount
     * @param string $refundReason
     * @param $refundSplitInfoList
     * @return bool|string
     * 退款申请（分账后）
     */
    public function profitShareRefundApply(
        string $loginNo,
        string $refundTradeId,
        string $orgTradeId,
        int $refundAmount,
        string $refundReason,
        array $refundSplitInfoList
    ) {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());
        $params["refundTradeId"] = $refundTradeId;
        $params["refundTradeType"] = "REFUND_APPLY";
        $params["orgTradeId"] = $orgTradeId;
        $params["refundAmount"] = $refundAmount;
        $params["refundReason"] = $refundReason;
        $params["refundSplitInfoList"] = $refundSplitInfoList;
        $refundSplitInfoListStr = str_replace("\\/", "/", json_encode($refundSplitInfoList));
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . $params["refundTradeId"]
            . $MARK . $params["orgTradeId"] . $MARK . $params["refundAmount"] . $MARK . $refundSplitInfoListStr;
        return BaoFuHttpCall::gI()->call("trade/v3.0.0/profitShareRefundApply", $params, $OsignDataString);
    }


    /**
     * @param string $loginNo 登录号
     * @param string $refundTradeId
     * @param string $orgTradeId
     * @param int $refundAmount
     * @param string $notifyUrl
     * @return bool|string
     * 确认退款（分账后）
     */
    public function profitShareRefundConfirm(
        string $loginNo,
        string $refundTradeId,
        string $orgTradeId,
        int $refundAmount,
        string $notifyUrl
    ) {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());
        $params["refundTradeId"] = $refundTradeId;
        $params["refundTradeType"] = "REFUND_CONFIRM";
        $params["orgTradeId"] = $orgTradeId;
        $params["refundAmount"] = $refundAmount;
        $params["notifyUrl"] = $notifyUrl;
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . $params["refundTradeId"]
            . $MARK . $params["refundAmount"];
        return BaoFuHttpCall::gI()->call("trade/v3.0.0/profitShareRefundConfirm", $params, $OsignDataString);
    }


    /**
     * @param string $loginNo 登录号
     * @param string $refundTradeId
     * @return bool|string
     * 退款撤销（分账后）
     */
    public function profitShareRefundCancel(
        string $loginNo,
        string $refundTradeId
    ) {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());
        $params["refundTradeId"] = $refundTradeId;
        $params["refundTradeType"] = "REFUND_CANCEL";
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . $params["refundTradeId"];
        return BaoFuHttpCall::gI()->call("trade/v3.0.0/profitShareRefundConfirm", $params, $OsignDataString);
    }


    /**
     * @param string $loginNo 登录号
     * @param string $refundTradeId
     * @param string $orgTradeId
     * @param int $refundAmount
     * @param string $refundReason
     * @param array $refundSplitInfoList
     * @param string $notifyUrl
     * @return bool|string
     * 退款申请（分账前）
     */
    public function refundRequest(
        string $loginNo,
        string $refundTradeId,
        string $orgTradeId,
        int $refundAmount,
        string $refundReason,
        string $notifyUrl,
        string $oldSubOutOrderNo = null

    ) {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());
        $params["refundTradeId"] = $refundTradeId;
        $params["orgTradeId"] = $orgTradeId;
        $params["refundMoney"] = $refundAmount;
        $params["refundReason"] = $refundReason;
        $params["notifyUrl"] = $notifyUrl;
        if ($oldSubOutOrderNo) {
            $params["oldSubOutOrderNo"] = $oldSubOutOrderNo;
        }
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["requestDate"] . $MARK . $params["refundTradeId"] . $MARK . $params["orgTradeId"]
            . $MARK . $params["refundMoney"] . $MARK . $params["refundReason"] . $MARK . $params["notifyUrl"];
        return BaoFuHttpCall::gI()->call("trade/v3.0.0/refundRequest", $params, $OsignDataString);
    }
}
