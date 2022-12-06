<?php

namespace Ice\Pay\Core;

use Illuminate\Support\Facades\Http;
use Ice\Pay\RechargeConfig;
use Ice\Tool\Singleton;

use Ice\Tool\Support\Log;

use function Ice\Pay\Support\arr_to_xml;
use function Ice\Pay\Support\generate_sign;

class XinRuiTaiHttpCall
{
    use Singleton;

    protected $config = [];

    public $host = "https://pay.xrtpay.cn";


    public function __construct()
    {
        $config = RechargeConfig::gI()->getXinruitaiConfig();
        $this->config = $config;
    }

    public function call($action, array $params, $method = 'post')
    {
        $params = array_merge(
            $params,
            [
                'version' => '2.0',
                'mch_id' => $this->config->get("mchId"),
                'nonce_str' => uniqid(),
            ]
        );

        $secretKey = $this->config->get("key");
        $url = $this->host . '/' . $action;


        $log = Log::gI("信瑞泰请求信息");
        $log->appendRequest($url, $params, $method);

        $params['sign'] = generate_sign($params, $secretKey);

        $response = Http::send($method,$url, ["body" => arr_to_xml($params)]);
        $result = $response->body();
        if (strstr($result, "GBK")) {
            $result = iconv("GBK", "UTF-8", $result);
            $result = str_replace("GBK", "UTF-8", $result);
        }

        $log->appendResponse($url, $result, $response->status());
        $log->save();

        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $result, true)) {
            xml_parser_free($xml_parser);
            return $result;
        }
        $result = json_decode(json_encode(simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }


}
