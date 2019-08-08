<?php
namespace Peak\MicroService\Auth;
/**
 * Middleware for Laravel&Lumen
 * */
trait TokenMiddleware {

	protected static function check (array $credetial, array $config)
	{
		$auth = new Token($config);
		return $auth->check($credetial) ?: $auth->debug();

	}

}

