<?php
/**
 * By shuxiaoxian
 * 接口文档：https://cloud.tencent.com/document/product/269/1520
 * 2019年4月26日14:05:15
 */

namespace XiaoYun\Tencent;

use XiaoYun\Tencent\lib\ConfigSvr;
use XiaoYun\Tencent\lib\Dirtywords;
use XiaoYun\Tencent\lib\GroupSvc;
use XiaoYun\Tencent\lib\LoginSvc;
use XiaoYun\Tencent\lib\OpenIM;
use XiaoYun\Tencent\lib\Profile;
use XiaoYun\Tencent\lib\SignTools;
use XiaoYun\Tencent\lib\Snsim;

if (version_compare(PHP_VERSION, '5.6.0') < 0 &&
    version_compare(PHP_VERSION, '5.5.10') < 0 &&
    version_compare(PHP_VERSION, '5.4.29') < 0) {
    trigger_error('need php 5.4.29|5.5.10|5.6.0 or newer', E_USER_ERROR);
}

if (!extension_loaded('openssl')) {
    trigger_error('need openssl extension', E_USER_ERROR);
}
if (!in_array('sha256', openssl_get_md_methods(), true)) {
    trigger_error('need openssl support sha256', E_USER_ERROR);
}
if (version_compare(PHP_VERSION, '7.1.0') >= 0 && !in_array('secp256k1', openssl_get_curve_names(), true)) {
    trigger_error('not support secp256k1', E_USER_NOTICE);
}


class IM
{
    public $config; // Config

    private $site = 'https://console.tim.qq.com';  // 固定域名

    private $ver = 'v4'; // 协议版本号

    /**
     * IM constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!defined('THINK_VERSION')) {
            $this->config = (array)\think\facade\Config::pull('im');
        } else {
            $this->config = (array)\think\Config::get('im');
        }
        if (!isset($this->config['private_key']) or !isset($this->config['public_key'])) {
            throw new \Exception('请配置private_key及public_key！');
        }
        if (!openssl_pkey_get_private($this->config['private_key'])) {
            throw new \Exception(openssl_error_string());
        }
        if (!openssl_pkey_get_public($this->config['public_key'])) {
            throw new \Exception(openssl_error_string());
        }
    }

    /**
     * 获取配置详情
     * @param $name
     * @return array|mixed
     */
    public static function getConfig($name)
    {
        if (!defined('THINK_VERSION')) {
            $config = (array)\think\facade\Config::pull('im');
        } else {
            $config = (array)\think\Config::get('im');
        }
        return $name ? $config[$name] : $config;
    }

    /**
     * 签名工具
     * @return SignTools
     * @throws \Exception
     */
    public static function signSvc()
    {
        return new SignTools();
    }

    /**
     * 帐号管理
     * @return LoginSvc
     * @throws \Exception
     */
    public static function loginSvc()
    {
        return new LoginSvc();
    }

    /**
     * 消息发生器
     * @return OpenIM
     * @throws \Exception
     */
    public static function openim()
    {
        return new OpenIM();
    }

    /**
     * 资料管理
     * @return Profile
     * @throws \Exception
     */
    public static function profile()
    {
        return new Profile();
    }

    /**
     * 关系链管理
     * @return Snsim
     * @throws \Exception
     */
    public static function sns()
    {
        return new Snsim();
    }

    /**
     * 群组管理
     * @return Snsim
     * @throws \Exception
     */
    public static function group()
    {
        return new GroupSvc();
    }

    /**
     * 脏字管理
     * @return Snsim
     * @throws \Exception
     */
    public static function dirty()
    {
        return new Dirtywords();
    }

    /**
     * 运营数管理
     * @return Snsim
     * @throws \Exception
     */
    public static function configSvr()
    {
        return new ConfigSvr();
    }

    /**
     * 统一请求HTTPS客户端
     * @param $servicename
     * @param $command
     * @param $query
     * @param string $contenttype
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpsClient($servicename, $command, $query, $contenttype = 'json')
    {
        $sdkappid = $this->config['SDKAppid'];
        $identifier = $this->config['rootAccount']; // App 管理员帐号
        if (empty($sdkappid) or empty($identifier)) {
            throw new \Exception('请配置rootAccount管理员账号！');
        }
        $tools = new SignTools();
        $usersig = $tools->genSign($identifier);  // 管理员帐号生成的签名
        $random = rand(10000000001000000000100000000000, 99999999999999999999999999999999); // 32位无符号整数
        $attr = '?sdkappid=' . $sdkappid . '&identifier=' . $identifier . '&usersig=' . $usersig . '&random=' . $random . '&contenttype=' . $contenttype;
        $client = new \GuzzleHttp\Client(['base_uri' => $this->site]);
        return $client->request('POST', '/' . $this->ver . '/' . $servicename . '/' . $command . '/' . $attr, [
            'json' => $query
        ]);
    }

}
