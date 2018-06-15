<?php
namespace SD\Api\Auth;

abstract class TokenMiddleware {


	protected static $con = [
		'API_ID', 'API_KEY', 'API_EXP'
	];


	function __construct()
	{
		foreach (self::$con as $val) {
			if ( !defined('static::'.$val)) {
				echo 'Const of "'.$val.'" is not defined.';
				exit;
			}
		}
	}



	protected static function check ($req)
	{
		if ( $req->header('id')==static::API_ID ) {
			return Token::login(
				[
					'id' => static::API_ID,
					'key' => static::API_KEY,
					'timestamp' => $req->header('timestamp'),
				],
				$req->header('token'),
				static::API_EXP
			);
		}
	}

}

