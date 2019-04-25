## xiaoyun/tp-tencent-im

### 使用方法
#### 1、安装扩展
```
composer require xiaoyun/tp-apidoc
```

#### 2、调用接口
- 5.0安装好扩展后在 application\extra\ 文件夹下会生成 im.php 配置文件
- 5.1安装好扩展后在 application\config\ 文件夹下会生成 im.php 配置文件
- 在im.php中配置
```
    'controller' => [
        //  需要生成文档的类 如 'app\index\controller\index'
        'app\index\controller\index'
    ]
```
#### 3、默认过期时间
```
   $api = new Tencent\TLSSigAPI();
   $api->SetAppid(140000000);
   $private = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'private_key');
   $api->SetPrivateKey($private);
   $sig = $api->genSig('xiaojun');
   var_export($sig);
```
#### 4、在浏览器访问http://你的域名/doc 或者 http://你的域名/index.php/doc 查看接口文档

### 更多支持 xiaoyun.studio

#### 2019年4月20日 v1.0.0