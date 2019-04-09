<?php
namespace Peak\MicroService;

abstract class Auth {

	private $config;

	function __construct($config)
	{
		$this->config = $config;
	}

	use \Peak\Plugin\Debuger\Standard;

	/**
	 * set a new credentials with config
	 * */
	abstract public function attempt ();

	/**
	 * check if the credential is valid
	 * */
	abstract public function check ($credentials):bool;

}

