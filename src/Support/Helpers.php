<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ice\Pay\Support;

/*
 * helpers.
 *
 * @author overtrue <i@overtrue.me>
 */

/**
 * Generate a signature.
 *
 * @param array $attributes
 * @param string $key
 * @param string $encryptMethod
 *
 * @return string
 */
function generate_sign(array $attributes, $key, $encryptMethod = 'md5')
{
    ksort($attributes);

    $attributes['key'] = $key;


    return strtoupper(call_user_func_array($encryptMethod, [urldecode(http_build_query($attributes))]));
}

/**
 * @param string $signType
 * @param string $secretKey
 *
 * @return \Closure|string
 */
function get_encrypt_method(string $signType, string $secretKey = '')
{
    if ('HMAC-SHA256' === $signType) {
        return function ($str) use ($secretKey) {
            return hash_hmac('sha256', $str, $secretKey);
        };
    }

    return 'md5';
}

/**
 * Get client ip.
 *
 * @return string
 */
function get_client_ip()
{
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        // for php-cli(phpunit etc.)
        $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
}

/**
 * Get current server ip.
 *
 * @return string
 */
function get_server_ip()
{
    if (!empty($_SERVER['SERVER_ADDR'])) {
        $ip = $_SERVER['SERVER_ADDR'];
    } elseif (!empty($_SERVER['SERVER_NAME'])) {
        $ip = gethostbyname($_SERVER['SERVER_NAME']);
    } else {
        // for php-cli(phpunit etc.)
        $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
}

/**
 * Return current url.
 *
 * @return string
 */
function current_url()
{
    $protocol = 'http://';

    if ((!empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http') === 'https') {
        $protocol = 'https://';
    }

    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Return random string.
 *
 * @param string $length
 *
 * @return string
 */
function str_random($length)
{
    return Str::random($length);
}

/**
 * @param string $content
 * @param string $publicKey
 *
 * @return string
 */
function rsa_public_encrypt($content, $publicKey)
{
    $encrypted = '';
    openssl_public_encrypt($content, $encrypted, openssl_pkey_get_public($publicKey), OPENSSL_PKCS1_OAEP_PADDING);

    return base64_encode($encrypted);
}

function arr_to_xml($data, $root = true)
{
    $str = "";
    if ($root) {
        $str .= "<xml>";
    }
    foreach ($data as $key => $val) {
        if (is_array($val)) {
            $child = arr_to_xml($val, false);
            $str .= "<$key>$child</$key>";
        } else {
            $str .= "<$key><![CDATA[$val]]></$key>";
        }
    }
    if ($root) {
        $str .= "</xml>";
    }
    return $str;
}


function formPost($url, $params)
{
    $headers = array('Content-Type: application/x-www-form-urlencoded');
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] ?? null); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params)); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        return 'Errno' . curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $result;
}

function jsonPost($url, $postData, $DataType = "json")
{
    $curl = curl_init(); // 启动一个CURL会话
    $postDataString = "";
    if ($DataType == "json") {
        $postDataString = json_encode($postData);;//格式化参数
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array('Content-Type: application/json', 'Content-Length: ' . strlen($postDataString))
        );
    } else {
        $postDataString = http_build_query($postData);//格式化参数
    }
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

    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        $tmpInfo = curl_error($curl);//捕抓异常
    }
    curl_close($curl); //关闭CURL会话

    return $tmpInfo; //返回数据

}
