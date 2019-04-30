<?php
namespace Peak\MicroService;

use \Peak\Tool\Api;

abstract class Core
{

	protected $auth;
	private static $http;

	/**
	 * @param $auth array , key is the class name of authenticate method, val is certificate
	 * */
	function __construct(Auth $auth)
	{
		$this->auth = $auth;
		self::$http = new \Curl\Curl();
	}


	public function attempt ()
	{
		return $this->auth->check(self::response(true));
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
	 * 请求（标准化）
	 * @param $url string
	 * @param $param array
	 * @param $method string get或post(默认)
	 * @return bool
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
			$this->result = $http->response;
			return true;

		} catch ( \Exception $e) {
			$this->result = json_decode($e->getMessage());
			return false;
		}
	}


	/**
	 * 获取响应返回值
	 * @param $header bool true, return body data;false, return header data.
	 * @return mixed array,string,object
	 * */
	final protected function response ($header=false)
	{
		if ($header) {
			$header = [];
			foreach (self::$http->responseHeaders as $key=>$val) {
				$header[$key] = $val;
			}
			return $header;
		}
		return self::$http->response;
	}

	/**
	 * 处理请求和响应的返回值
	 * @param $url string
	 * @param $param array 数组
	 * @param $method string post或get
	 * @return boolean
	 * */
	protected function handle($url, array $param=[], $method='post'):bool
	{
		if ($this->request($url, $param, $method)) {

			if (is_string($this->result) ) {
				$this->result = self::response();
				return false;
			}

			if (self::response()->res==1) {
				$this->result = self::response()->dat;
				return true;
			}
			$this->result = self::response();
		}

		return false;
	}


}
