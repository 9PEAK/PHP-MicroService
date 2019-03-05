# PHP-MicroService

该仓库为简洁微服务框架，内置核心层、验证方法、集成拓展预留，可作为系统间通信的基础服务组件。

#### 安装
> composer require 9peak/micro-service


#### 核心&集成扩展
开发者可在项目中集成扩展微服务，但务必遵循下列事项，
<ul>
	<li>建议一个模块一个Class文件，但必须必须继承 <b>\Peak\MicroService\Core</b>；</li>
	<li>可自定义Auth验证方法，以Class的形式编写（必须继承<b>\Peak\MicroService\Auth</b>）,微服务对象初始化时调入；</li>
	<li>必须复写handle方法，打包封装成功、失败、异常等中间业务逻辑；</li>
</ul>


#### 封装&业务
组件封装了底层请求和返回值的处理，因此开发者无需关注这些底层细节，编写代码时只需要专注通信中的路由、参数、方法——即业务逻辑本身；
除此之外，为方便调用业务方法务必以“public”进行封装。
<br> 假设，需要在BBS模块中封装查询今日回帖Top10的主题，编写如下
```php
class BBS extends \Peak\MicroService\Core {

    // 配置模块域名
    const API_URL = 'http://域名/api/bbs/';

	public function listHotestTheme ($date) {
		return self::handle (
            self::API_URL.'hotestTheme', // 补齐路由
            [
                'date' => $date // 请求参数
            ],
            'post' // 请求方法
		);
	}

}

```



#### 使用

```php
// 初始化
$api = new \Peak\MicroService\Integration\BBS(
	new \Peak\MicroService\Auth\Token([
		'id' => 'bbs',
		'key' => 'md5-bbs',
		'exp' => 6,
	])
);

// 通信请求
// handle方法总是返回boolean 以体现通信或业务成功与否
if ($api->listHotestTheme('2066-06-06')) {
    // result属性用于存放返回的结果和异常，在复写handle的方法时务必注意
	print_r($api->result);
}
```


