<?php
namespace SD\Api\Auth;

abstract class TokenMiddleware {


	protected static $con = [
		'APP_ID', 'APP_KEY', 'APP_EXP'
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
		if ( $req->get('id')==static::APP_ID ) {
			return Token::validate(
				[
					'id' => static::APP_ID,
					'key' => static::APP_KEY,
					'timestamp' => $req->get('timestamp'),
				],
				$req->get('token'),
				static::APP_EXP
			);
		}
	}

}

