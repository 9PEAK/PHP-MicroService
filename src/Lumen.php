<?php
namespace SD;

use Peak\TimeToken;

class Lumen extends Api {

	const API_ID = 'yt';
	const API_KEY = 'leokdj850fld93';
	const API_ROUTE = 'sd';

	/**
	 * 获取api_id 参数
	 * */
	private static function api_id ()
	{
		return @static::API_ID ?: self::API_ID;
	}

	/**
	 * 获取api_key 参数
	 * */
	private static function api_key ()
	{
		return @static::API_KEY ?: self::API_KEY;
	}



	/**
	 * 获取api_route 参数
	 * */
	private static function api_route ()
	{
		return @static::API_ROUTE ?: '';
	}


	public static function setUrl ($act)
	{
		$token = new TimeToken();

		$param = [
			'api_id' => self::api_id(),
			'api_key' => self::api_key(),
		];
		$param['token'] = $token->makeToken($param) ;
		$param = $token->outputString($param, ['token']);

		parent::setUrl(self::api_route().$act.'?'.$param);
	}



	/**
	 * 标准化业务流程
	 * */
	final public function handle ()
	{

	}





}