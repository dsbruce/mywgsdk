<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/19
 * Time: 10:46
 */

namespace Zwb\Mywgsdk\AliyunMqtt;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class AliyunIotRpc{

    protected $regionId ;

    public function __construct($accessId, $accessKey, $regionId){

        $this->regionId = $regionId;

        // 设置一个全局客户端
        try {
            AlibabaCloud::accessKeyClient($accessId, $accessKey)
                ->regionId($regionId) // replace regionId as you need
                ->asDefaultClient();

        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**
     * 向指定Topic发布消息, 一个设备的topic是唯一的, 有 $productKey/$deviceName 组成
     * @param $productKey
     */
    function pub($topic, $productKey, $pubMsgArr, $qos){

        $result = $this->pubRpc($topic, $productKey, $pubMsgArr, $qos, 'Pub');

        if (!$result['Success']) {
            echo '向指定Topic发布消息失败'.PHP_EOL;
        }

    }

    /**
     * 向订阅了指定Topic的所有设备发布广播消息
     * @param $productKey
     */
    function pubBroadcast($topic, $productKey, $pubMsgArr, $qos){

        $result =  $this->pubRpc($topic, $productKey, $pubMsgArr, $qos, 'PubBroadcast');

        if (!$result['Success']) {
            echo '向指定Topic发布消息失败'.PHP_EOL;
        }
    }

    /**
     * Notes: 统一发布处理
     * @param $topic
     * @param $productKey
     * @param $pubMsgArr
     * @param $qos
     * @param $action
     * Author: weibin.zhang
     * Date: 2021/4/19
     * Time: 11:22
     */
    protected function pubRpc($topic, $productKey, $pubMsgArr, $qos, $action){

        try {
            $query['RegionId']       = $this->regionId;
            $query['ProductKey']     = $productKey;
            $query['TopicFullName']  = $topic;
            $query['MessageContent'] = base64_encode($pubMsgArr);
            $query['Qos']             = $qos ?? 1;

            $result = AlibabaCloud::rpc()
                ->host('iot.'.$this->regionId.'.aliyuncs.com')
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->action($action)
                ->options([
                    'query' => $query,
                ])
                ->request();

            return  $result->toArray();

        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }

//    /**
//     * 向指定设备发送请求消息，并同步返回响应
//     * @param $productKey
//     */
//    function rrpcDevice($productKey)
//    {
//        try {
//            $query['RegionId'] = REGION_ID;
//            $query['ProductKey'] = $productKey;
//            $query['DeviceName'] = 'device1';
//            $query['RequestBase64Byte'] = 'aGVsbG8gd29ybGQ';
//            $query['Timeout'] = 1000;
////        $query['Topic'] = '';//不传入此参数，则使用系统默认的RRPC Topic。
//            $result = AlibabaCloud::rpc()
//                ->product('Iot')
////        ->scheme('https') // https | http
//                ->method('POST')
//                ->version('2018-01-20')
//                ->host('iot.cn-shanghai.aliyuncs.com')
//                ->action('RRpc')
//                ->options([
//                    'query' => $query,
//                ])
//                ->request();
//            $result2Array = $result->toArray();
//            echo "向指定设备发送请求消息，并同步返回响应:".PHP_EOL;
//            print_r($result2Array);
//            if (!$result2Array['Success']) {
//                echo '向指定设备发送请求消息，并同步返回响应失败'.PHP_EOL;
//            }
//
//        } catch (ClientException $e) {
//            echo $e->getErrorMessage() . PHP_EOL;
//        } catch (ServerException $e) {
//            echo $e->getErrorMessage() . PHP_EOL;
//        }
//    }
}