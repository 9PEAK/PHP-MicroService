<?php
namespace SD\Api;

class MicroService {


	protected static $http;
	protected static $auth;


	/**
	 * @param $auth array , key is the class name of authenticate method, val is certificate
	 * */
	function __construct(array $auth)
	{
		self::$auth = $auth;
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


	/**
	 * 4 获取请求的数据
	 * @param $key 支持链式调用 默认null，整个请求结果
	 * @return mixed array | string
	 * */
	private function response ($key=null)
	{
		$res = self::$http->response;
		if ($key) {
			$res = json_decode($res, 1);
			$key = explode('.', $key);
			foreach ($key as $k) {
				$res = @$res[$k];
			}
		}
		return $res;
	}


	/**
	 * 跨应用标准化请求业务
	 * @param $func method name of request
	 * @param $param param of request
	 * */
	final public function handle ($func, array $param, $method='post')
	{
		$http =& self::$http;

		#1 设置url
		$url = static::APP_URL.$func;

		#2 设置参数
		self::$func($param);

		try {
			#3 设置验证数据
			$http->setHeaders(self::attempt());

			#4 发送请求
			$http->post($url, $param);

			#5 获取返回值

			if ($http->error) {
				throw new \Exception(json_encode([
					'url' => $url,
					'method' => $method,
					'param' => $param,
					'error' => 'Error: ' . $http->errorCode . ': ' . $http->errorMessage,
				]));
			}

			if (self::response('res')==1) {
				return self::response('dat');
			}

			throw new \Exception(self::response());

		} catch ( \Exception $e) {
			echo 'ERROR: '.$e->getMessage();
		}
	}

}
