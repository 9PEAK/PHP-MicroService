<?php
namespace Peak\MicroService;

use \Peak\Tool\Api;

class Core {

	protected static $auth;
	protected static $http;


	/**
	 * @param $auth array , key is the class name of authenticate method, val is certificate
	 * */
	function __construct(array $auth, array $config=[])
	{
		self::$auth = $auth;

		foreach ($config as $key=>$val) {
			if (property_exists(static::class, $key) ) {
				static::$$key = $val;
			}
		}

		self::$http = new \Curl\Curl();

	}


	/**
	 * 生成提交验证
	 * */
	private static function attempt ()
	{
		return (__NAMESPACE__.'\Auth\\'.ucfirst(key(self::$auth)))::attempt(current(self::$auth));
	}


	/**
	 * 设置设置时间格式
	 * */
	private static function set_time ($val)
	{
		if ( !is_numeric($val)) {
			$val = strtotime($val);
		}
		return date($form, $val);
	}


	public $debug;

	/**
	 * 4 获取请求的数据
	 * @param $key 支持链式调用 默认null，整个请求结果
	 * @return mixed array | string
	 * */
	private function response ($key=null)
	{
		return \Peak\Tool\Arr::array_key_chain(json_decode(json_encode(self::$http->response), 1), $key, '.');
	}


	private static function set_url_query ($query)
	{
		if (!$query) return '';

		if (is_array($query)) {
			foreach ($query as $k=>&$v) {
				$v = $k.'='.$v;
			}
			return '?'.join('&', $query);
		} else {
			$query = trim($query);
			return strpos($query, '?')===0 ? $query : '?'.$query;
		}
	}


	protected static $req_param = [];

	/**
	 * 跨应用标准化请求业务
	 * @param $func method name of request
	 * @param $param param of request
	 * */
	final public function request ($func, array $param, $query=null, $method='post')
	{
		$http =& self::$http;

		try {
			Api::reset();

			#1 设置url
			$url = function () use (&$func, &$query) {
				Api::url(static::$api_url);
				Api::url($func);
				return Api::url(self::set_url_query($query));
			};

//			$url = static::$api_url.$func.self::set_url_query($query);

			#2 设置参数
			$param = function () use (&$func, &$param) {
				Api::param(self::$req_param,0);
				return Api::param(static::$func($param),0);
			};

			#3 设置验证数据
			$http->setHeaders(self::attempt());

			#4 发送请求
			$http->$method($url, $param);

			#5 获取返回值
			if ($http->error) {
				throw new \Exception(json_encode([
					'url' => $url,
					'method' => $method,
					'param' => $param,
					'error' => 'Error: ' . $http->errorCode . ': ' . $http->errorMessage,
					'response' => self::response()
				]));
			}

			if (self::response('res')==1) {
				return self::response('dat');
			}

			throw new \Exception(json_encode(self::$http->response));

		} catch ( \Exception $e) {
			$this->debug = json_decode($e->getMessage(), 1);
		}

	}


	protected static function test(array $param)
	{
		return $param;
	}



	public function __call($func, $arguments)
	{
		return @$this->handle($func, $arguments[0], $arguments[1], $arguments[2]);
	}

}
