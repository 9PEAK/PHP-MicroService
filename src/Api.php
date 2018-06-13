<?php
namespace SD ;

use Curl\Curl ;

abstract class Api {

	# 参数

	const API_URL = 'http://sd-api.9peak.net/';
	const API_TIME_FORMAT = '';

	# 基础方法

	/**
	 * 获取api_url 参数
	 * */
	private static function api_url ($route='')
	{
		return @static::API_URL.$route ?: self::API_URL.$route;
	}


	/**
	 * 获取API_TIME_FORMAT 参数
	 * */
	private static function api_time_format ()
	{
		return @static::API_TIME_FORMAT ?: self::API_TIME_FORMAT;
	}


	/**
	 * 设置api接口时间参数格式
	 * */
	public static function setTime ($val)
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
	private static $req_url;

	public static function setUrl ($route) {
		$route = trim(trim($route), '/');
		self::$req_url = self::api_url($route ?: '');
	}



	/**
	 * 2 设置生成api的请求参数
	 * */
	private static $req_param = [];

	public static function setParam ($k, $v) {
		self::$req_param[$k] = $v ;
	}

	public static function setParamInGroup (array $arr) {
		foreach ( $arr as $k=>&$v) {
			self::setParam($k, $v);
		}
	}



	/**
	 * 3 发起请求
	 * @param $method post或get请求
	 * @return boolean true 请求成功 false 请求失败
	 * */
	protected static function request ($method='post'):bool
	{
		$curl =& self::$curl;
		$curl->$method (self::$req_url , self::$req_param);
		if ($curl->error) {
			self::$debug = 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage ;
			return false;
		}
		return true;
	}



	/**
	 * 4 获取请求的数据
	 * @param $key 支持链式调用 默认null，整个请求结果
	 * @return mixed array | string
	 * */
	public static function response ($key=null)
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



	# 初始化

	protected static $curl;
	static $debug;

	function __construct()
	{
		self::$curl = new Curl();
	}

}
