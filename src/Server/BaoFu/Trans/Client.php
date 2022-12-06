<?php

namespace Ice\Pay\Server\BaoFu\Trans;

use Ice\Pay\Core\BaoFuHttpCall;
use Ice\Pay\RechargeConfig;

class Client
{

    public function trans($param)
    {
        $config = RechargeConfig::gI()->getBaoFuConfig();
        /* Header 参数*/
        $HeaderPost = array();
        $HeaderPost["memberId"] = $config->get("member_id");//宝付提供给商户的唯一编号
        $HeaderPost["terminalId"] = $config->get("terminal_id");//宝付提供给商户终端编号
        $HeaderPost["serviceTp"] = "T-1001-003-01";
        $HeaderPost["verifyType"] = "1";//加密方式目前只有1种，请填：1

        $BodyData = array();
        $BodyData["transNo"] = isset($param["transNo"]) ? trim($param["transNo"]) : "";//商户订单号
        $BodyData["transMoney"] = isset($param["transMoney"]) ? trim($param["transMoney"]) : "";//交易金额(元) 0.00必须保留两位
        $BodyData["transType"] = isset($param["transType"]) ? trim($param["transType"]) : "";//付款类型(0:银行卡 1:一级商户 2:二级会员)
        $BodyData["transAccNo"] = isset($param["transAccNo"]) ? trim($param["transAccNo"]) : "";//收款人账号(银行卡号, 一级商户id, 二级会员id)根据transType传对应值
        $BodyData["transAccName"] = isset($param["transAccName"]) ? trim($param["transAccName"]) : "";//收款人名称(银行卡收款人姓名,一级商户 名称,二级会员名称)根据transType传对应值
        $BodyData["transIdCard"] = isset($param["transIdCard"]) ? trim($param["transIdCard"]) : "";//证件号
        $BodyData["transMobile"] = isset($param["transMobile"]) ? trim($param["transMobile"]) : "";//预留手机号
        $BodyData["cardBankName"] = isset($param["cardBankName"]) ? trim($param["cardBankName"]) : "";//收款人银行名称
        $BodyData["cardProName"] = isset($param["cardProName"]) ? trim($param["cardProName"]) : "";//银行卡收款人开户行省名  （对公必传）
        $BodyData["cardCityName"] = isset($param["cardCityName"]) ? trim($param["cardCityName"]) : "";//银行卡收款人开户行市名（对公必传）
        $BodyData["cardAccDept"] = isset($param["cardAccDept"]) ? trim($param["cardAccDept"]) : "";//银行卡收款人开户行机构名（对公必传）
        $BodyData["cardCnap"] = isset($param["cardCnap"]) ? trim($param["cardCnap"]) : "";//银行卡联行号  （对公必传）
        $BodyData["transSummary"] = isset($param["transSummary"]) ? trim($param["transSummary"]) : "";//摘要

        $ListBodyData = array();
        array_push($ListBodyData, $BodyData);  //加入分账LIST
        $transContentStr["transContent"] = $ListBodyData;

        $contentData = array();
        $contentData["header"] = $HeaderPost;
        $contentData["body"] = $transContentStr;

        $Jsonstr = str_replace("\\/", "/", json_encode($contentData));//转JSON

        $Encrypted = BaoFuHttpCall::gI()->encryptedByPrivateKey(
            $Jsonstr,
            $config->get("pfxfilename"),
            $config->get("private_key_password")
        );
        $HeaderPost["content"] = $Encrypted;//发送的密文

        $url = $config->get("trans_host") . "/" . $HeaderPost["serviceTp"] . "/transReq.do";

        return BaoFuHttpCall::gI()->transCall($HeaderPost, $url);
    }


    public function transQuery($transBatchId, $transNo){

        $config = RechargeConfig::gI()->getBaoFuConfig();
        /* Header 参数*/
        $HeaderPost = array();
        $HeaderPost["memberId"] = $config->get("member_id");//宝付提供给商户的唯一编号
        $HeaderPost["terminalId"] = $config->get("terminal_id");//宝付提供给商户终端编号
        $HeaderPost["serviceTp"]="T-1001-003-02";
        $HeaderPost["verifyType"]="1";//加密方式目前只有1种，请填：1
        $BodyData = array();
        $BodyData["transBatchId"]= $transBatchId;
        $BodyData["transNo"]= $transNo;
        $ListBodyData = array();
        array_push($ListBodyData,$BodyData);  //加入分账LIST
        $transContentStr["transContent"]=$ListBodyData;
        $contentData= array();
        $contentData["header"]= $HeaderPost;
        $contentData["body"]=$transContentStr;
        $Jsonstr = str_replace("\\/", "/",json_encode($contentData));//转JSON
        $Encrypted = BaoFuHttpCall::gI()->encryptedByPrivateKey(
            $Jsonstr,
            $config->get("pfxfilename"),
            $config->get("private_key_password")
        );
        $HeaderPost["content"]=$Encrypted;//发送的密文
        $url = $config->get("trans_host") . "/" . $HeaderPost["serviceTp"] . "/transReq.do";
        return BaoFuHttpCall::gI()->transCall($HeaderPost, $url);
    }

}
