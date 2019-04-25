<?php

namespace XiaoYun\Tentcent;


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
    /**
     * IM constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!defined('THINK_VERSION')) {
            $config = \think\facade\Config::pull('im');
        } else {
            $config = \think\Config::get('im');
        }
        if (!isset($config['private_key']) or !isset($config['public_key'])) {
            throw new \Exception('请配置private_key及public_key！');
        }
        if (!openssl_pkey_get_private($config['private_key'])) {
            throw new \Exception(openssl_error_string());
        }
        if (!openssl_pkey_get_public($config['public_key'])) {
            throw new \Exception(openssl_error_string());
        }
    }

    public static function getAccountType()
    {

    }

    /**
     * @return mixed
     */
    private static function config($x = null)
    {
        if (!defined('THINK_VERSION')) {
            $config = \think\facade\Config::pull('im');
        } else {
            $config = \think\Config::get('im');
        }
        return $x ? $config[$x] : $config;
    }

    /**
     * @param $identifier 用户名
     * @param $userbuf
     * @param int $expire 超时时间
     * @return string 生成的UserSig 失败时为false
     * @throws \Exception
     */
    public static function genSigWithUserbuf($identifier, $userbuf, $expire = 15552000)
    {
        $json = Array(
            'TLS.account_type' => '0',
            'TLS.identifier' => (string)$identifier,
            'TLS.appid_at_3rd' => '0',
            'TLS.sdk_appid' => (string)self::config('appid'),
            'TLS.expire_after' => (string)$expire,
            'TLS.version' => '201512300000',
            'TLS.time' => (string)time(),
            'TLS.userbuf' => base64_encode($userbuf)
        );
        $err = '';
        $content = self::genSignContentWithUserbuf($json, $err);
        $signature = self::sign($content, $err);
        $json['TLS.sig'] = base64_encode($signature);
        if ($json['TLS.sig'] === false) {
            throw new \Exception('base64_encode error');
        }
        $json_text = json_encode($json);
        if ($json_text === false) {
            throw new \Exception('json_encode error');
        }
        $compressed = gzcompress($json_text);
        if ($compressed === false) {
            throw new \Exception('gzcompress error');
        }
        return self::base64Encode($compressed);
    }

    /**
     * 验证usersig
     * @param type $sig usersig
     * @param type $identifier 需要验证用户名
     * @param type $init_time usersig中的生成时间
     * @param type $expire_time usersig中的有效期 如：3600秒
     * @param type $error_msg 失败时的错误信息
     * @return boolean 验证是否成功
     */
    public static function verifySigWithUserbuf($sig, $identifier, &$init_time, &$expire_time, &$userbuf, &$error_msg)
    {
        try {
            $error_msg = '';
            $decoded_sig = self::base64Decode($sig);
            $uncompressed_sig = gzuncompress($decoded_sig);
            if ($uncompressed_sig === false) {
                throw new \Exception('gzuncompress error');
            }
            $json = json_decode($uncompressed_sig);
            if ($json == false) {
                throw new \Exception('json_decode error');
            }
            $json = (array)$json;
            if ($json['TLS.identifier'] !== $identifier) {
                throw new \Exception("identifier error sigid:{$json['TLS.identifier']} id:{$identifier}");
            }
            if ($json['TLS.sdk_appid'] != self::config('appid')) {
                throw new \Exception("appid error sigappid:{$json['TLS.appid']} thisappid:{" . self::config('appid') . "}");
            }
            $content = self::genSignContentWithUserbuf($json);
            $signature = base64_decode($json['TLS.sig']);
            if ($signature == false) {
                throw new \Exception('sig json_decode error');
            }
            $succ = self::verify($content, $signature);
            if (!$succ) {
                throw new \Exception('verify failed');
            }
            $init_time = $json['TLS.time'];
            $expire_time = $json['TLS.expire_after'];
            $userbuf = base64_decode($json['TLS.userbuf']);
            return true;
        } catch (\Exception $ex) {
            $error_msg = $ex->getMessage();
            return false;
        }
    }

    /**
     * 用于url的base64encode
     * '+' => '*', '/' => '-', '=' => '_'
     * @param string $string 需要编码的数据
     * @return string 编码后的base64串，失败返回false
     */
    private static function base64Encode($string)
    {
        static $replace = Array('+' => '*', '/' => '-', '=' => '_');
        $base64 = base64_encode($string);
        if ($base64 === false) {
            throw new \Exception('base64_encode error');
        }
        return str_replace(array_keys($replace), array_values($replace), $base64);
    }

    /**
     * 用于url的base64decode
     * '+' => '*', '/' => '-', '=' => '_'
     * @param string $base64 需要解码的base64串
     * @return string 解码后的数据，失败返回false
     */
    private static function base64Decode($base64)
    {
        static $replace = Array('+' => '*', '/' => '-', '=' => '_');
        $string = str_replace(array_values($replace), array_keys($replace), $base64);
        $result = base64_decode($string);
        if ($result == false) {
            throw new \Exception('base64_decode error');
        }
        return $result;
    }

    /**
     * 根据json内容生成需要签名的buf串
     * @param array $json 票据json对象
     * @return string 按标准格式生成的用于签名的字符串
     * 失败时返回false
     */
    private static function genSignContent(array $json)
    {
        $content = '';
        static $aid3rd = 'TLS.appid_at_3rd';
        if (isset($json[$aid3rd])) {
            $content .= "{$aid3rd}:{$json[$aid3rd]}\n";
        }
        static $members = Array(
            'TLS.account_type',
            'TLS.identifier',
            'TLS.sdk_appid',
            'TLS.time',
            'TLS.expire_after'
        );
        foreach ($members as $member) {
            if (!isset($json[$member])) {
                throw new \Exception('json need ' . $member);
            }
            $content .= "{$member}:{$json[$member]}\n";
        }
        return $content;
    }

    /**
     * ECDSA-SHA256签名
     * @param string $data 需要签名的数据
     * @return string 返回签名 失败时返回false
     */
    private static function sign($data)
    {
        $signature = '';
        if (!openssl_sign($data, $signature, self::config('private_key'), 'sha256')) {
            throw new \Exception(openssl_error_string());
        }
        return $signature;
    }

    /**
     * 验证ECDSA-SHA256签名
     * @param string $data 需要验证的数据原文
     * @param string $sig 需要验证的签名
     * @return int 1验证成功 0验证失败
     */
    private static function verify($data, $sig)
    {
        $ret = openssl_verify($data, $sig, self::config('private_key'), 'sha256');
        if ($ret == -1) {
            throw new \Exception(openssl_error_string());
        }
        return $ret;
    }

    /**
     * 生成usersig
     * @param string $identifier 用户名
     * @param uint $expire usersig有效期 默认为180天
     * @return string 生成的UserSig 失败时为false
     */
    public static function genSig($identifier, $expire = 15552000)
    {
        $json = Array(
            'TLS.account_type' => '0',
            'TLS.identifier' => (string)$identifier,
            'TLS.appid_at_3rd' => '0',
            'TLS.sdk_appid' => (string)self::config('appid'),
            'TLS.expire_after' => (string)$expire,
            'TLS.version' => '201512300000',
            'TLS.time' => (string)time()
        );
        $err = '';
        $content = self::genSignContent($json, $err);
        $signature = self::sign($content, $err);
        $json['TLS.sig'] = base64_encode($signature);
        if ($json['TLS.sig'] === false) {
            throw new \Exception('base64_encode error');
        }
        $json_text = json_encode($json);
        if ($json_text === false) {
            throw new \Exception('json_encode error');
        }
        $compressed = gzcompress($json_text);
        if ($compressed === false) {
            throw new \Exception('gzcompress error');
        }
        return self::base64Encode($compressed);
    }

    /**
     * 验证usersig
     * @param type $sig usersig
     * @param type $identifier 需要验证用户名
     * @param type $init_time usersig中的生成时间
     * @param type $expire_time usersig中的有效期 如：3600秒
     * @param type $error_msg 失败时的错误信息
     * @return boolean 验证是否成功
     */
    public static function verifySig($sig, $identifier, &$init_time, &$expire_time, &$error_msg)
    {
        try {
            $error_msg = '';
            $decoded_sig = self::base64Decode($sig);
            $uncompressed_sig = gzuncompress($decoded_sig);
            if ($uncompressed_sig === false) {
                throw new \Exception('gzuncompress error');
            }
            $json = json_decode($uncompressed_sig);
            if ($json == false) {
                throw new \Exception('json_decode error');
            }
            $json = (array)$json;
            if ($json['TLS.identifier'] !== $identifier) {
                throw new \Exception("identifier error sigid:{$json['TLS.identifier']} id:{$identifier}");
            }
            if ($json['TLS.sdk_appid'] != self::config('appid')) {
                throw new \Exception("appid error sigappid:{$json['TLS.appid']} thisappid:{" . self::config('appid') . "}");
            }
            $content = self::genSignContent($json);
            $signature = base64_decode($json['TLS.sig']);
            if ($signature == false) {
                throw new \Exception('sig json_decode error');
            }
            $succ = self::verify($content, $signature);
            if (!$succ) {
                throw new \Exception('verify failed');
            }
            $init_time = $json['TLS.time'];
            $expire_time = $json['TLS.expire_after'];
            return true;
        } catch (\Exception $ex) {
            $error_msg = $ex->getMessage();
            return false;
        }
    }

    /**
     * 根据json内容生成需要签名的buf串
     * @param array $json 票据json对象
     * @return string 按标准格式生成的用于签名的字符串
     * 失败时返回false
     */
    private static function genSignContentWithUserbuf(array $json)
    {
        static $members = Array(
            'TLS.appid_at_3rd',
            'TLS.account_type',
            'TLS.identifier',
            'TLS.sdk_appid',
            'TLS.time',
            'TLS.expire_after',
            'TLS.userbuf'
        );
        $content = '';
        foreach ($members as $member) {
            if (!isset($json[$member])) {
                throw new \Exception('json need ' . $member);
            }
            $content .= "{$member}:{$json[$member]}\n";
        }
        return $content;
    }

}
