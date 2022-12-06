<?php

namespace Ice\Pay;

use Ice\Pay\Server\XinRuiTai\XinRuiTai;
use Ice\Pay\Server\BaoFu\BaoFu;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $apps = [
            'XinRuiTai' => XinRuiTai::class,
            'BaoFu' => BaoFu::class,
        ];
        foreach ($apps as $name => $class) {
            $accounts = config('pay.' . $name);

            foreach ($accounts as $account => $config) {
                $this->app->singleton(
                    "pay.{$name}.{$account}",
                    function () use ($class, $name, $config) {
                        return new $class($config);
                    }
                );
            }
            $this->app->alias("pay.{$name}.default", 'pay.'.$name);
            $this->app->alias("pay.{$name}.default", 'pay.'.$name);

        }
    }


    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/config.php' => config_path('pay.php'),
            ]
        );
    }
}
