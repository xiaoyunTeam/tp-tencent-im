## xiaoyun/tp-tencent-im

### 使用方法
基于腾讯云IM
##### 云通信（Instant Messaging）承载亿级 QQ 用户即时通信技术，数十年技术积累，腾讯云为您提供超乎寻常即时通信聊天服务。针对开发者的不同阶段需求及不同场景，云通信提供了一系列解决方案，包括： Android/iOS/Windows/Web 的 SDK 组件、服务端集成接口、第三方回调接口等，利用这些组件，可以在应用中构建自己的即时通信产品，解决开发者面临的高并发、高可用性的一系列问题。


[腾讯云IM传送门](https://cloud.tencent.com/document/product/269)
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
        'private_key' => '', // 设置私钥 如果要生成usersig则需要私钥
        'public_key' => '', // 设置公钥 如果要验证usersig则需要公钥
    ];
    
```
#### 3、默认过期时间
```
   use XiaoYun\Tencent\IM;
   
   $user = 'xiaoyun01';
   $sign = IM::genSign($user);
   return $sign;
```

#### 4、指定过期时间
```
   use XiaoYun\Tencent\IM;
   
   $user = 'xiaoyun02';
   $sign = IM::genSignWithUserbuf($user);
   return $sign;
```

### 更多支持 xiaoyun.studio

#### 2019年4月20日 v1.0.0