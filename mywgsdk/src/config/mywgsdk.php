<?php
/**
 * Created by PhpStorm.
 * User: zwb
 * Date: 2021/12/6
 * Time: 14:41
 */

return [
    // 默认选择 aliyun oss 驱动
    'default' => env('IOT_DRIVER', 'aliyuniot'),

    'rpc' => [
        'aliyuniot' => [
            'driver'     => 'aliyuniot',
            'access_id'  => env('ALIYUN_OSS_ACCESS_ID', null),
            'access_key' => env('ALIYUN_OSS_ACCESS_KEY', null),
            'region_id'  => env('ALIYUN_REGION_ID', 'cn-shanghai'),
        ],
        'localiot' => [

        ]
    ],
];