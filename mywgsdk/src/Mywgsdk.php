<?php
/**
 * Created by PhpStorm.
 * User: zwb
 * Date: 2021/12/6
 * Time: 14:38
 */

namespace Zwb\Mywgsdk;


use http\Exception\InvalidArgumentException;
use Illuminate\Support\Arr;
use Zwb\Mywgsdk\AliyunMqtt\AliyunIotRpc;

class Mywgsdk
{
    /**
     * Notes: 反控滴答RTU
     * Author: weibin.zhang
     * Date: 2021/4/19
     * Time: 10:18
     */
    public function rpc($name = null, $topic, $productKey, $pubMsgArr, $qos = null){

        $name = $name ?: $this->getDefaultDriver();

        return $this->get($name, $topic, $productKey, $pubMsgArr, $qos);
    }

    /**
     * Notes: 获取默认驱动
     * Author: weibin.zhang
     * Date: 2021/4/19
     * Time: 10:22
     */
    public function getDefaultDriver(){
        return config('mywgsdk.default');
    }

    /**
     * Notes: 获取处理结果
     * @param $name
     * @return \Illuminate\Contracts\Cache\Repository
     * Author: weibin.zhang
     * Date: 2021/4/19
     * Time: 10:24
     */
    public function get($name, $topic, $productKey, $pubMsgArr, $qos){
        return $this->resolve($name, $topic, $productKey, $pubMsgArr, $qos);
    }

    /**
     * Notes: 获取配置
     * @param $name
     * @return \Illuminate\Config\Repository|mixed
     * Author: weibin.zhang
     * Date: 2021/4/19
     * Time: 10:34
     */
    protected function getConfig($name){
        return config("mywgsdk.rpc.{$name}");
    }

    /**
     * Notes: 处理
     * @param $name
     * @return mixed
     * Author: weibin.zhang
     * Date: 2021/4/19
     * Time: 10:25
     */
    protected function resolve($name, $topic, $productKey, $pubMsgArr, $qos){

        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Iot rpc [{$name}] is not defined.");
        }

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config, $topic, $productKey, $pubMsgArr, $qos);
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");

        }
    }

    /**
     * Notes: 创建阿里云iot
     * Author: weibin.zhang
     * Date: 2021/4/19
     * Time: 10:33
     */
    protected function createAliyuniotDriver($config, $topic, $productKey, $pubMsgArr, $qos){

        $accessId  = Arr::get($config, 'access_id', null);
        $accessKey = Arr::get($config, 'access_key', null);
        $regionId  = Arr::get($config, 'region_id', null);

        if (empty($accessId) || empty($accessKey) || empty($regionId)){
            throw new InvalidArgumentException("Driver access_id or access_key or region_id is not supported.");
        }
        $aliyunIotRpc = new AliyunIotRpc($accessId, $accessKey, $regionId);
        $aliyunIotRpc->pub($topic, $productKey, $pubMsgArr, $qos);

        return true;
    }

    /**
     * Notes: 创建阿里云iot
     * Author: weibin.zhang
     * Date: 2021/4/19
     * Time: 10:33
     */
    protected function createLocaliotDriver(){

        // 创建 本地iot 服务
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->rpc()->$method(...$parameters);
    }

}