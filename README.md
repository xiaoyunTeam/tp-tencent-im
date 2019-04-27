## xiaoyun/tp-tencent-im

### 使用方法
[腾讯云IM](https://cloud.tencent.com/document/product/269)

##### 云通信（Instant Messaging）承载亿级 QQ 用户即时通信技术，数十年技术积累，腾讯云为您提供超乎寻常即时通信聊天服务。

#
#### 1、安装扩展
```
composer require xiaoyun/tp-tencent-im
```

#### 2、配置文件
- 5.0安装好扩展后在 application\extra\ 文件夹下会生成 im.php 配置文件
- 5.1安装好扩展后在 application\config\ 文件夹下会生成 im.php 配置文件
- 在im.php中配置
```
    return [
        'SDKAppid' => '',  // 填入创建腾讯云通讯应用获取到的 sdkappid
        'accountType' => '', // 填入在帐号体系集成配置中获取到的 accountType 前端使用
        'rootAccount' => '', // 管理员帐号 
        'private_key' => '', // 设置私钥 如果要生成usersig则需要私钥
        'public_key' => '', // 设置公钥 如果要验证usersig则需要公钥
    ];
    
```
#### 3、默认过期时间获取签名
```
   use XiaoYun\Tencent\IM;
   
   $user = 'xiaoyun01';
   $sign = IM::genSign($user);
   return $sign;
```

#### 4、指定过期时间获取签名
```
   use XiaoYun\Tencent\IM;
   
   $user = 'xiaoyun02';
   $time = 3600 * 8 * 24;
   $sign = IM::genSignWithUserbuf($user,$time);
   return $sign;
```

#### 5、使用API示例：用户是否在线

```
use XiaoYun\Tencent\IM;

$result = IM::openim()->queryState(['user1','user2'])
```

### 更多支持 xiaoyun.studio


### curl error 60 问题
* 下载 CA 证书
你可以从 [http://curl.haxx.se/ca/cacert.pem][http://curl.haxx.se/ca/cacert.pem] 下载 或者 使用微信官方提供的证书中的 CA 证书 rootca.pem 也是同样的效果。
* 在 `php.ini` 中配置 CA 证书
只需要将上面下载好的 CA 证书放置到您的服务器上某个位置，然后修改 php.ini 的 curl.cainfo 为该路径（绝对路径！），重启 php-fpm 服务即可。
```
curl.cainfo = /path/to/downloaded/cacert.pem
```


#### 2019年4月20日 v1.1.2

[http://curl.haxx.se/ca/cacert.pem]: http://curl.haxx.se/ca/cacert.pem