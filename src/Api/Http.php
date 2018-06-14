<?php
namespace SD\Api;

use Curl\Curl ;

class Http {


	# 初始化&参数

	static $debug;
	protected static $curl;
	protected $api_url;
	protected $api_time_zone;
	protected $api_time_format;

	function __construct(array $con)
	{
		self::$curl = new Curl();
		foreach ($con as $key=>&$val) {
			if (property_exists(static::class, $key)) {
				$this->{$key} = $val;
			}
		}
	}



	/**
	 * 设置api接口时间参数格式
	 * */
	public function setTime ($val)
	{
		if ( !is_numeric($val)) {
			$val = strtotime($val);
		}
//		$tz = self::api_time_zone();
		$form = self::api_time_format();
		return date($form, $val);
	}


	# 标准化业务
	/**
	 * 1 设置生成api的url
	 * */
	private $req_url;

	public function setUrl ($route='') {
		$route = $route ? trim(trim($route), '/') : '';
		$this->req_url = $this->api_url.$route;
	}



	/**
	 * 2 设置生成api的请求参数
	 * */
	private $req_param = [];

	public function setParam ($k, $v) {
		$this->req_param[$k] = $v ;
	}

	public function setParamInGroup (array $arr) {
		foreach ( $arr as $k=>&$v) {
			$this->setParam($k, $v);
		}
	}



	/**
	 * 3 发起请求
	 * @param $method post或get请求
	 * @return boolean true 请求成功 false 请求失败
	 * */
	public function request ($method='post'):bool
	{
		$curl =& self::$curl;
		$curl->$method ($this->req_url, $this->req_param);
		if ($curl->error) {
			self::$debug = [
				'url' => $this->req_url,
				'method' => $method,
				'param' => $this->req_param,
				'error' => 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage,
			];
			return false;
		}
		return true;
	}



	/**
	 * 4 获取请求的数据
	 * @param $key 支持链式调用 默认null，整个请求结果
	 * @return mixed array | string
	 * */
	public function response ($key=null)
	{
		$res = self::$curl->response;
		if ($key) {
			$res = json_decode($res, 1);
			$key = explode('.', $key);
			foreach ($key as $k) {
				$res = @$res[$k];
			}
		}
		return $res;
	}


}
