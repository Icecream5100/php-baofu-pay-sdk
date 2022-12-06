<?php

namespace Ice\Pay\Server\BaoFu;


use Ice\Pay\RechargeConfig;
use Ice\Pay\Constants\ConfigTypeConstant;
use Ice\Tool\Di;


/**
 * Class XinRuiTai.
 *
 * @property \Ice\Pay\Server\BaoFu\Order\Client $order
 * @property \Ice\Pay\Server\BaoFu\Refund\Client $refund
 * @property \Ice\Pay\Server\BaoFu\Account\Client $account
 * @property \Ice\Pay\Server\BaoFu\Trans\Client $trans
 */
class BaoFu
{
    const PayId = [
        6001 => "招商银行",
        6002 => "工商银行",
        6003 => "建设银行",
        6005 => "农业银行",
        6006 => "民生银行",
        6009 => "兴业银行",
//        6020 => "交通银行",
//        6022 => "光大银行",
        6026 => "中国银行",
//        6032 => "北京银行",
//        6033 => "东亚银行",
        6035 => "平安银行",
        6036 => "广发银行",
//        6037 => "上海农商银行",
        6038 => "邮政储蓄银行",
        6039 => "中信银行",
//        6050 => "华夏银行",
        6059 => "上海银行",
//        6060 => "天津银行",
    ];

    protected $providers = [
        'order' => Order\Client::class,
        'account' => Account\Client::class,
        'trans' => Trans\Client::class,
        'refund' => Refund\Client::class,
    ];

    public function __construct(array $config = [])
    {
        Di::gI()->set(ConfigTypeConstant::BAOFU_CONFIG_NAME, new RechargeConfig($config ?? []));
        foreach ($this->providers as $key => $provider) {
            Di::gI()->set($key, new $provider());
            $this->$key = Di::gI()->get($key);
        }
    }

}
