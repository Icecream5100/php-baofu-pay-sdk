<?php

namespace Ice\Pay\Server\XinRuiTai;


use Ice\Pay\RechargeConfig;
use Ice\Pay\Constants\ConfigTypeConstant;
use Ice\Tool\Di;


/**
 * Class XinRuiTai.
 *
 * @property \Ice\Pay\Server\XinRuiTai\Order\Client $order
 */
class XinRuiTai
{

    protected $providers = [
        'order' => Order\Client::class,
    ];

    public function __construct(array $config = [])
    {
        Di::gI()->set(ConfigTypeConstant::XINRUITAI_CONFIG_NAME, new RechargeConfig($config ?? []));
        foreach ($this->providers as $key => $provider) {
            Di::gI()->set($key, new $provider());
            $this->$key = Di::gI()->get($key);
        }
    }

}
