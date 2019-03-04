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
	private static function url_query ($param=null)
	{
		if (!$param) return '';
		return is_array($param) ? \Peak\Plugin\Arr::joinKeyValToString($param) : trim($param);
	}



	/**
	 * 跨应用标准化请求业务
	 * @param $url
	 * @param $param
	 * @param $method
	 * */
	final protected function request ($url, array $param, $method='post'):bool
	{
		$http =& self::$http;

		try {
			#1 设置验证数据
			$http->setHeaders($this->auth->attempt());

			#2 发送请求
			$http->$method($url, $param);

			#3 获取返回值
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
	 * 获取请求的数据
	 * @param $key 支持链式调用 默认null，整个请求结果
	 * @return mixed array | string
	 * */
	final protected function response ($key=null)
	{
		return \Peak\Tool\Arr::array_key_chain(
			json_decode(json_encode(self::$http->response), 1),
			$key, '.'
		);
	}


	abstract protected function handle($url, $param, $method);


}
