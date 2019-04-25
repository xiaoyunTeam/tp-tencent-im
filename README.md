## xiaoyun/tp-tencent-im

### 使用方法
#### 1、安装扩展
```
composer require xiaoyun/tp-apidoc
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
   use XiaoYun\Tentcent\IM;
   
   $user = 'xiaoyun01';
   $sign = IM::genSign($user);
   var_export($sign);
```

#### 4、指定过期时间
```
   use XiaoYun\Tentcent\IM;
   
   $user = 'xiaoyun02';
   $sign = IM::genSignWithUserbuf($user);
   var_export($sign);
```

### 更多支持 xiaoyun.studio

#### 2019年4月20日 v1.0.0