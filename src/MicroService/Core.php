<?php
namespace Peak\MicroService;

use \Peak\Tool\Api;

abstract class Core {

	protected $auth;
	protected static $http;

	/**
	 * @param $auth array , key is the class name of authenticate method, val is certificate
	 * */
	function __construct(Auth $auth)
	{
		$this->auth = $auth;
		self::$http = new \Curl\Curl();
	}



	public $result;

	/**
	 * 设置URL查询参数
	 * */
	private static function set_url_query ($param=null)
	{
		if (!$param) return '';
		return is_array($param) ? \Peak\Plugin\Arr::joinKeyValToString($param) : trim($param);
	}



	/**
	 * 跨应用标准化请求业务
	 * @param $func method name of request
	 * @param $param param of request
	 * */
	protected function request ($route, array $param, $query=null, $method='post'):bool
	{
		$http =& self::$http;

		try {
			#1 设置url
			$url = static::API_URL.$route;
			$url.= '?'.self::set_url_query($query);

			#2 设置参数

			#3 设置验证数据
			$http->setHeaders($this->auth->attempt());

			#4 发送请求
			$http->$method($url, $param);

			#5 获取返回值
			if ($http->error) {
				throw new \Exception(json_encode([
					'url' => $url,
					'method' => $method,
					'param' => $param,
					'error' => 'Error: ' . $http->errorCode . ': ' . $http->errorMessage,
					'response' => $http->response
				]));
			}

			return true;

		} catch ( \Exception $e) {
			$this->result = $http->response;
			return false;
		}

	}



	/**
	 * 4 获取请求的数据
	 * @param $key 支持链式调用 默认null，整个请求结果
	 * @return mixed array | string
	 * */
	protected function response ($key=null)
	{

		return \Peak\Tool\Arr::array_key_chain(
			json_decode(json_encode(self::$http->response), 1),
			$key, '.'
		);
	}



	public function test ()
	{
		$res = self::request('test', [], [], 'post');

		if (self::response('res')==1) {
			$this->result = self::response('dat');
			return true;
		}

		throw new \Exception(json_encode(self::$http->response));
	}

/*
	public function __call($func, $arguments)
	{
		return @$this->handle($func, $arguments[0], $arguments[1], $arguments[2]);
	}*/

}
