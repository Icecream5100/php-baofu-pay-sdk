<?php

/*
 * This file is part of the nilsir/laravel-esign.
 *
 * (c) nilsir <nilsir@qq.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

return [
    'XinRuiTai' => [
        'default' => [
//            'mchId' => "80121839646206",
//            'key' => "50537800DBDBB389E588876AAB43F80E",
            'mchId' => env('XINRUITAI_MCH', '80121839646206'),
            'key' => env('XINRUITAI__KEY', '50537800DBDBB389E588876AAB43F80E'),
        ]
    ],

    'BaoFu' => [
        'default' => [
            'host' => env('BAOFU_API_HOST', 'https://account.baofoo.com/api'),
            'trans_host' => env('BAOFU_TRANS_API_HOST', 'http://vgw.baofoo.com/union-gw/api'),
            'member_id' => env('BAOFU_MEMBER_ID', '100026136'),
            'terminal_id' => env('BAOFU_TERMINAL_ID', '200001259'),
            'simId' => env('BAOFU_SIMID', ''),
            'fenZhang_wvId' => env('BAOFU_FENZHANG_WVID', ''),
            'private_key_password' => env('BAOFU_PRIVATE_PASS', '100026136_259652'),
            'pfxfilename' => storage_path("cert/baofu/") . env('BAOFU_PFX_PATH', 'bfkey_100026136@@200001259.pfx'),
            'cerfilename' => storage_path("cert/baofu/") . env('BAOFU_CERT_PATH', 'bfkey_100026136@@200001259.cer'),
        ]
    ]
];
