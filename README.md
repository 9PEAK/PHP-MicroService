This is the Chinese Introduction for this Repo. [Click to read the English version.](https://github.com/9PEAK/PHP-MicroService/blob/master/README-en.md)

# PHP-MicroService 2.3

该仓库为简洁的微服务框架，内置核心层、验证方法、集成拓展预留，可作为系统间通信的基础服务组件。
它封装了请求、校验、返回值预处理等完整的通信流程。


#### 安装
> composer require 9peak/micro-service


#### 核心继承&扩展
开发者可在项目中集成扩展微服务，但务必遵循下列事项：
<ul>
	<li>继承：建议一个模块一个Class文件，但必须必须继承 <b>\Peak\MicroService\Core</b>；</li>
	<li>校验：可自定义Auth验证方法，以Class的形式编写（必须继承<b>\Peak\MicroService\Auth</b>）,微服务对象初始化时调入，另外，自带的\Peak\MicroService\Auth\Token类开箱即用；</li>
	<li>预处理：Core已预置handle方法，打包封装抽象业务逻辑，如成功、失败、异常等，可根据实际项目情况沿用或者重定义方法；</li>
</ul>

```php

class UserCenter extends \Peak\MicroService\Core
{

    // 自定义域名前缀
    const API_URL = 'http://域名/api/bbs/';

    // 重定义handle方法
	protected function handle($url, array $param=[], $method='post'):bool
    {
        // 发起请求，返回boolen，无论请求是否成功，响应结果都存储于$this->result中
        if ($this->request(statid::API_URL.$url, $param, $method)) {
            
            // http请求失败报错
            if (is_string($this->result) ) {
                $this->result = self::response();
                return false;
            }

            // http请求成功，response()方法将自动把json返回值转成stdClass对象或数据
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


#### 具体业务
上述代码封装了底层逻辑：请求、返回值处理、异常处理，开发者无需关注这些细节，编写具体业务时只需专注于业务逻辑本身：接口的路由、参数、方法；
除此之外，为方便调用业务方法务必以“public”进行封装。

```php
class UserCenter extends \Peak\MicroService\Core
{

    // 配置模块域名
    const API_URL = 'http://域名/api/';

    
    /**
     * 从用户中心获取指定账号资料信息
     * @param $account string 帐号.
     * @return bool
     * */
	public function getUserInfo ($account):bool
	{
		return self::handle (
            self::API_URL.'user/detail', // 补齐路由
            [
                'account' => $account // 请求参数
            ],
            'post' // 请求方法
		);
	}

}

```


#### 完整DEMO

```php

# 请求
// 初始化
$api = new UserCenter(
	new \Peak\MicroService\Auth\Token([
		'id' => 'bbs',
		'key' => 'md5-bbs',
		'exp' => 6,
	])
);

// 通信请求
// handle方法总是返回boolean 以体现通信或业务成功与否
if ($api->getUserInfo('abc@email.com')) {
    echo '成功：';
	print_r($api->result);
} else {
    echo '失败：';
    print_r($api->result);
}


# 响应
new \Peak\MicroService\Auth\Token([
    'id' => 'bbs',
    'key' => 'md5-bbs',
    'exp' => 6,
])

```


