This is the Chinese Introduction for this Repo. [Click to read the English version.](https://github.com/9PEAK/PHP-MicroService/blob/master/README-en.md)

# PHP-MicroService 2.3

这是一个简洁的微服务组件（简称MS），封装了请求、校验、返回值预处理等完整的通信流程，可作为项目中的基础服务组件，并根据项目需要集成、拓展。<br>
下图是微服务流程中，应用间通信的生命周期，本组件封装了图中“实线”部分的流程，并允许对已封装的部分进行复写、自定义；接下来，将基于从安装、初始化、方法编写的角度说明组件的使用。

#### 安装
以图中X、UserCenter应用为例，假设我们在X应用上使用组件。
> composer require 9peak/micro-service


#### 准备：核心继承、集成扩展、初始化
核心 <b>\Peak\MicroService\Core</b>（简称核心Core）预置了“handel”方法，它打包了请求、接收相应、异常处理的基础流程，开箱即用。也可根据外部应用的实际情况重定义该方法。<br>
“handel”方法的使用不是必须的，但这种将整个流程打包封装的方法不失为一个好思路。

```php
class UserCenter extends \Peak\MicroService\Core
{

    // 自定义域名前缀
    const API_URL = 'http://UserCenter-domain/api/';

    /**
     * 处理请求和响应的返回值
     * @param $url string
     * @param $param array 数组
     * @param $method string post或get
     * @return boolean
     * */
	protected function handle($url, array $param=[], $method='post'):bool
    {
        // 发起请求，返回boolen，无论请求是否成功，响应结果都存储于$this->result中
        if ($this->request(statid::API_URL.$url, $param, $method)) {
            
            // respons异常 http请求失败报错
            if (is_string($this->result) ) {
                $this->result = self::response();
                return false;
            }

            // response.res==1：http请求成功，response()方法将自动把json返回值转成stdClass对象或Array
            if (self::response()->res==1) {
                $this->result = self::response()->dat;
                return true;
            }
            $this->result = self::response();
        }

        return false;
    }

}
```

核心Core为抽象类，并不包含具体的业务方法，因此必须由业务模块类继承——例如，针对应用UserCenter的模块为 <b>\App\Service\MS\UserCenter</b>。<br>
下列示例同时体现了“handle”方法的优势：开发者无需关注底层流程，只需专注于业务逻辑接口的路由、参数、方法。<br>
另外，建议使用一个外部应用对应一个模块文件的代码方式。

```php
namespace App\Service\MS;

class UserCenter extends \Peak\MicroService\Core
{
    /**
     * 从用户中心获取指定账号资料信息
     * @param $account string 帐号.
     * @return bool
     * */
    public function getUserInfo ($account):bool
    {
        return self::handle (
            self::API_URL.'user/detail', // 访问路由
            [
                'account' => $account // 请求参数
            ],
            'post' // 请求方法
        );
    }
    
}
```

初始化组件时，必须以注入<b>\Peak\MicroService\Auth</b>实例，可根据实际情况，继承该类以自定义校验参数和规则；请求时，组件将把校验参数置于HTTP-HEADER一并发送。<br>
另外，组件自带的\Peak\MicroService\Auth\Token类开箱即用。

```php
$auth = new \Peak\MicroService\Auth\Token([
    'id' => 'UCenter',
    'key' => 'md5-UCenter',
    'exp' => 6,
]);

$api = new UserCenter($auth);
```



#### 请求

```php
// handle方法总是返回boolean 以体现通信或业务成功与否
if ($api->getUserInfo('abc@email.com')) {
    echo '成功：';
	print_r($api->result);
} else {
    echo '失败：';
    print_r($api->result);
}

```


#### 响应
上述说明都是基于请求方（应用X）的。而作为响应方UserCenter则主要使用“身份校验”功能，以Laravel中间件示例如下。
```php
namespace App\Http\Middleware;

class MicroService {

    public function handle($req, Closure $next)
    {
        $auth = new \Peak\MicroService\Auth\Token([
            'id' => 'user-center',
            'key' => config('services.ms.user-center'),
            'exp' => 6,
        ]);

        $res = $auth->check([
            'id' => $req->header('id'),
            'timestamp' => $req->header('timestamp'),
            'token' => $req->header('token'),
        ])
        
        if ($res) {
            return $next($req);
        } else {
            return response()->json([
                'res' => -1,
                'msg' => '签名错误。'
            ]);
        }
    }

}
```
