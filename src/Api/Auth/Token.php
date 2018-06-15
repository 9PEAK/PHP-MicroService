<?php
namespace SD\Api\Auth;

use Peak\TimeToken;

trait Token {

	static function attempt ($credentials)
	{
		$obj = new TimeToken();
		return [
			'id' => $credentials['id'],
			'token' => $obj->sign([
				'app_id' => $credentials['id'],
				'app_key' => $credentials['key'],
			]),
			'timestamp' => $obj->time()
		];
	}



	static function login ($credentials, $token, $exp)
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

