<?php

/*
 * This file is part of the nilsir/laravel-esign.
 *
 * (c) nilsir <nilsir@qq.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Ice\Pay;

use Ice\Pay\Constants\ConfigTypeConstant;
use Ice\Tool\Di;
use Ice\Tool\Singleton;
use Ice\Tool\Support\Collection;

class RechargeConfig extends Collection
{
    use Singleton;


    /**
     * @return RechargeConfig|callable
     */
    public function getXinruitaiConfig()
    {
        return Di::gI()->get(ConfigTypeConstant::XINRUITAI_CONFIG_NAME);
    }


    /**
     * @return RechargeConfig|callable
     */
    public function getBaoFuConfig()
    {
        return Di::gI()->get(ConfigTypeConstant::BAOFU_CONFIG_NAME);
    }
}
