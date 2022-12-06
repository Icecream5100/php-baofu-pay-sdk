<?php

namespace Ice\Pay\Core;

use Exception;
use Illuminate\Support\Facades\Http;
use Ice\Pay\RechargeConfig;
use Ice\Tool\Singleton;

use Ice\Tool\Support\Log;


class BaoFuHttpCall
{
    use Singleton;

    protected $config = [];

    public $common_params = [];


    public function __construct()
    {
        $config = RechargeConfig::gI()->getBaoFuConfig();
        $this->config = $config;
    }


    public function call($action, array $params, string $OsignDataString, $method = 'post', $form = false)
    {
        $Sign = $this->sign(
            $OsignDataString,
            $this->config->get("pfxfilename"),
            $this->config->get("private_key_password")
        );
        $params["signData"] = $Sign;//签名字段
        $url = $this->config->get("host") . '/' . $action;

        $log = Log::gI("宝付请求信息");
        $log->appendRequest($url, $params, $method);

        if ($form) {
            $response = Http::asForm()->post($url, $params);
        } else {
            if ($method == "post" || $method == "POST") {
                $response = Http::post($url, $params);
            } else {
                $response = Http::get($url, $params);
            }
        }
        $result = $response->body();
        $result = json_decode($result, true);
        $log->appendResponse($url, $result, $response->status());
        $log->save();
        return $result;
    }

    public function getParams(): array
    {
        $params = [];
        $params["orgNo"] = $this->config->get("member_id");
        $params["merchantNo"] = $this->config->get("member_id");
        $params["terminalNo"] = $this->config->get("terminal_id");
        return $params;
    }

    public function Sign($Data, $PfxPath, $Pwd)
    {
        if (!function_exists('bin2hex')) {
            throw new Exception("bin2hex PHP5.4及以上版本支持此函数，也可自行实现！");
        }
        if (!file_exists($PfxPath)) {
            throw new Exception("私钥文件不存在！");
        }

        $pkcs12 = file_get_contents($PfxPath);
        $PfxPathStr = array();
        if (openssl_pkcs12_read($pkcs12, $PfxPathStr, $Pwd)) {
            $PrivateKey = $PfxPathStr['pkey'];
            $BinarySignature = null;
            if (openssl_sign($Data, $BinarySignature, $PrivateKey, OPENSSL_ALGO_SHA1)) {
                return bin2hex($BinarySignature);
            } else {
                throw new Exception("加签异常！");
            }
        } else {
            throw new Exception("私钥读取异常【密码和证书不匹配】！");
        }
    }

    public function encryptByCERFile($Data)
    {
        try {
            $PublicPath = $this->config->get("cerfilename");
            if (!function_exists('bin2hex')) {
                throw new Exception("bin2hex PHP5.4及以上版本支持此函数，也可自行实现！");
            }
            $public_key = self::ReadPublicKey($PublicPath);
            $BASE64EN_DATA = base64_encode($Data);
            $EncryptStr = "";
            $blockSize = 117;//分段长度
            $totalLen = strlen($BASE64EN_DATA);
            $EncryptSubStarLen = 0;
            $EncryptTempData = "";
            while ($EncryptSubStarLen < $totalLen) {
                openssl_public_encrypt(
                    substr($BASE64EN_DATA, $EncryptSubStarLen, $blockSize),
                    $EncryptTempData,
                    $public_key
                );
                $EncryptStr .= bin2hex($EncryptTempData);
                $EncryptSubStarLen += $blockSize;
            }
            return $EncryptStr;
        } catch (Exception $exc) {
            return $exc->getTraceAsString();
        }
    }

    /**
     * 读取公钥
     */
    private static function ReadPublicKey($PublicKeyPath)
    {
        $keyFile = file_get_contents($PublicKeyPath);
        $public_key = openssl_get_publickey($keyFile);
        if (empty($public_key)) {
            throw new Exception("读取本地公钥异常，请检查证书、密码或路径是否正确");
        }
        return $public_key;
    }


    private static function ReadPrivateKey($private_key_path, $private_pwd)
    {
        $pkcs12 = file_get_contents($private_key_path);
        $private_key = array();
        openssl_pkcs12_read($pkcs12, $private_key, $private_pwd);
        if (empty($private_key)) {
            throw new Exception("读取本地私钥异常，请检查证书、密码或路径是否正确");
        }
        return $private_key["pkey"];
    }

    /**
     * 私钥加密
     */
    public function encryptedByPrivateKey($src, $private_key_path, $private_pwd)
    {
        $private_key = self::ReadPrivateKey($private_key_path, $private_pwd);
        $base64_str = base64_encode($src);
        $encrypted = "";
        $totalLen = strlen($base64_str);
        $encryptPos = 0;
        $blockSize = 117;
        while ($encryptPos < $totalLen) {
            openssl_private_encrypt(substr($base64_str, $encryptPos, $blockSize), $encryptData, $private_key);
            $encrypted .= bin2hex($encryptData);
            $encryptPos += $blockSize;
        }
        return $encrypted;
    }

    /**
     * 公钥解密
     * @param type $encrypted
     * @param type $Public_Key_Path
     * @return type
     */
    public function decryptByPublicKey($encrypted, $Public_Key_Path)
    {
        $public_key = self::ReadPublicKey($Public_Key_Path);
        $decrypt = "";
        $totalLen = strlen($encrypted);
        $decryptPos = 0;
        $blockSize = 256;//分段长度
        while ($decryptPos < $totalLen) {
            openssl_public_decrypt(hex2bin(substr($encrypted, $decryptPos, $blockSize)), $decryptData, $public_key);
            $decrypt .= $decryptData;
            $decryptPos += $blockSize;
        }
        $decrypt = base64_decode($decrypt);
        return $decrypt;
    }


    public function transCall($params, $url, $method = "POST")
    {
        $log = Log::gI("宝付划款请求信息");
        $log->appendRequest($url, $params, $method);
        $postData = $params;
        $curl = curl_init(); // 启动一个CURL会话
        $postDataString = http_build_query($postData);//格式化参数
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //从证书中检查SSL加密算法是否存在
        curl_setopt(
            $curl,
            CURLOPT_SSLVERSION,
            6
        ); //CURL_SSLVERSION_DEFAULT (0), CURL_SSLVERSION_TLSv1 (1), CURL_SSLVERSION_SSLv2 (2), CURL_SSLVERSION_SSLv3 (3), CURL_SSLVERSION_TLSv1_0 (4), CURL_SSLVERSION_TLSv1_1 (5)， CURL_SSLVERSION_TLSv1_2 (6) 中的其中一个。
        curl_setopt($curl, CURLOPT_POST, true); //发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postDataString); //Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 60); //设置超时限制防止死循环返回
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            $tmpInfo = curl_error($curl);//捕抓异常
        }
        curl_close($curl); //关闭CURL会话
        if (empty($result)) {
            throw new Exception("返回为空，确认是否网络原因！");
        }
        $result = $this->decryptByPublicKey($result, $this->config->get("cerfilename"));
        $result = json_decode($result,TRUE);
        $log->appendResponse($url, $result, 200);
        $log->save();
        return $result; //返回数据
    }

}
