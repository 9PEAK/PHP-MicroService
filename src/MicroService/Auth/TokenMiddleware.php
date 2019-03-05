<?php
namespace Peak\MicroService\Auth;
/**
 * Middleware for Laravel&Lumen
 * */
trait TokenMiddleware {

	use \Peak\Plugin\Debuger;

	protected static function check (array $credetial, array $config)
	{
		$auth = new Token ($config);
		return $auth->check($credetial) ?: self::debug ($auth->debug()->getMessage(), $auth->debug()->getCode());
	}

}

