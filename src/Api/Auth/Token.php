<?php
namespace SD\Api\Auth;

use Peak\TimeToken;

trait Token {


	static function attempt ($credentials)
	{
		$obj = new TimeToken();
		return $obj->outputString(
			[
				'id' => $credentials['id'],
				'token' => $obj->makeToken([
					'app_id' => $credentials['id'],
					'app_key' => $credentials['key'],
				])
			]
		);
	}



	static function validate ($credentials, $token, $exp)
	{
		$obj = new TimeToken();
		return $obj->validate(
			[
				'app_id' => $credentials['id'],
				'app_key' => $credentials['key'],
				'timestamp' => $credentials['timestamp'],
			],
			$token,
			$exp
		);
	}

}
