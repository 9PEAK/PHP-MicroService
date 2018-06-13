<?php
namespace SD ;

use Peak\TimeToken;

class SD extends Api {

	const API_ID = 'yt';
	const API_KEY = 'leokdj850fld93';

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



	public static function setUrl ($act)
	{
		$token = new TimeToken();

		$param = [
			'api_id' => self::api_id(),
			'api_key' => self::api_key(),
		];
		$param['token'] = $token->makeToken($param) ;
		$param = $token->outputString($param, ['token']);

		parent::setUrl($act.'?'.$param);
	}




}