<?php

namespace Ice\Pay\Server\BaoFu\Account;

use Ice\Pay\Core\BaoFuHttpCall;

class Client
{

    /**
     * @param string $loginNo 用户登录注册号，为用户在商户下唯一编号
     * @param string $notifyUrl 回调地址
     * @return bool|string
     * 个人实名认证(H5)
     */
    public function openAccountH5(
        string $loginNo,
        string $notifyUrl
    ) {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());;
        $params["callType"] = "REGISTER";
        $dataContent["notifyUrl"] = $notifyUrl;
        $params["dataContent"] = str_replace("\\/", "/", json_encode($dataContent));
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"] .
            $MARK . $params["callType"] . $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . $params["dataContent"];
        return BaoFuHttpCall::gI()->call("wallet/v3.0.0/login", $params, $OsignDataString, "POST", true);
    }


    /**
     * @param string $loginNo 用户登录注册号，为用户在商户下唯一编号
     * @param string $customerName 客户姓名
     * @param string $certificateNo 身份证号
     * @param string $base64imageFront 身份证头像面 base64 图片
     * @param string $base64imageBack 身份证国徽面 base64 图片
     * @param string $notifyUrl 回调地址
     * @return bool|string
     * 个人实名认证
     */
    public function openAccount(
        string $loginNo,
        string $customerName,
        string $certificateNo,
        string $base64imageFront,
        string $base64imageBack,
        string $notifyUrl
    ) {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestNo"] = "Request" . time();
        $params["requestDate"] = date('YmdHis', time());;
        $params["customerName"] = BaoFuHttpCall::gI()->encryptByCERFile($customerName);
        $params["certificateNo"] = $certificateNo;
        $params["base64imageFront"] = $base64imageFront;
        $params["base64imageBack"] = $base64imageBack;
        $params["notifyUrl"] = $notifyUrl;
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"] .
            $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . $params["customerName"] . $MARK . $params["certificateNo"];
        return BaoFuHttpCall::gI()->call("cust/v3.0.0/openAccount", $params, $OsignDataString);
    }


    /**
     * @param string $loginMobile 用户登录注册号，为用户在商户下唯一编号
     * @return bool|string
     * 个人开户信息查询
     */
    public function findCustInfo(string $loginMobile)
    {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginMobile"] = $loginMobile;
        $params["requestNo"] = "Request" . time();
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"] .
            $MARK . $params["loginMobile"] . $MARK . $params["requestNo"];
        return BaoFuHttpCall::gI()->call("cust/v2.0.0/findCustInfo", $params, $OsignDataString);
    }


    /**
     * @param $loginNo
     * @return bool|string
     * 游客注册
     */
    public function preRegister($loginNo)
    {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginMobile"] = $loginNo;
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"] . $MARK . $params["loginMobile"] . "|null|null";
        return BaoFuHttpCall::gI()->call("cust/v2.0.0/preRegister", $params, $OsignDataString);
    }


    /**
     * @param $loginNo
     * @param $nofity_url
     * @return bool|string
     * 委托提现授权
     */
    public function authWithdrawal($loginNo, $nofity_url)
    {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());;
        $params["callType"] = "SIGN_WITHDRAW_ENTRUST";
        $params["dataContent"] = $nofity_url;
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["callType"] . $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . $params["dataContent"];
        return BaoFuHttpCall::gI()->call("wallet/v3.0.0/login", $params, $OsignDataString, "POST", true);
    }


    /**
     * @param $loginNo
     * @return bool|string
     * 授权信息查询
     */
    public function findSignEntrustResult($loginNo)
    {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["agreementType"] = "W_ENTRUST";
        $params["requestDate"] = date('YmdHis', time());
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["loginNo"] . $MARK . $params["requestDate"];
        return BaoFuHttpCall::gI()->call("cust/v3.0.0/findSignEntrustResult", $params, $OsignDataString);
    }


    /**
     * @param $loginNo
     * @param $contractNo
     * @return bool|string
     * 获取已绑定卡信息
     */
    public function findBindBankCards($loginNo, $contractNo)
    {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["tradeType"] = "BASE_LIST";
        $params["contractNo"] = $contractNo;
        $params["requestDate"] = date('YmdHis', time());
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["loginNo"] . $MARK . $params["contractNo"];
        return BaoFuHttpCall::gI()->call("cust/v2.0.0/findBindBankCards", $params, $OsignDataString);
    }


    /**
     * @param string $loginNo
     * @param int $callType 1个人 2企业
     * @return bool|string
     * 现web h5
     */
    public function withdrawWeb(string $loginNo, $callType = 1)
    {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());
        $params["callType"] = $callType == 1 ? "WITHDRAW":"BM_WITHDRAW";
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["merchantNo"] . $MARK . $params["terminalNo"] .
            $MARK . $params["callType"] . $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . null;
        return BaoFuHttpCall::gI()->call("wallet/v3.0.0/login", $params, $OsignDataString, "POST", true);
    }


    /**
     * @param $loginNo
     * @return
     * 企业认证 PC
     */
    public function certification($loginNo, $email, $notifyUrl)
    {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["email"] = $email;
        $params["notifyUrl"] = $notifyUrl;
        $params["requestDate"] = date('YmdHis', time());
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["terminalNo"]
            . $MARK . $params["loginNo"] . $MARK . $params["requestDate"] . $MARK . $params["email"];
        return BaoFuHttpCall::gI()->call("merchant/v1/apply", $params, $OsignDataString, "POST", true);
    }


    /**
     * @param $loginNo
     * @return
     * 重发邮件
     */
    public function sendBindCardEmail($loginNo)
    {
        $params = BaoFuHttpCall::gI()->getParams();
        $params["loginNo"] = $loginNo;
        $params["requestDate"] = date('YmdHis', time());
        $MARK = "|";
        $OsignDataString = $params["orgNo"] . $MARK . $params["terminalNo"] . $MARK . $params["requestDate"]
            . $MARK . $params["loginNo"];
        return BaoFuHttpCall::gI()->call("merchant/v1/sendBindCardEmail", $params, $OsignDataString);
    }
}
