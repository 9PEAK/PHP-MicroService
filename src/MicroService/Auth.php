<?php
namespace Peak\MicroService;

abstract class Auth {

	protected $config;

	function __construct($config)
	{
		$this->config = $config;
	}

	use \Peak\Plugin\Debuger\Base;

	/**
	 * set a new credentials with config
	 * */
	abstract public function attempt ();

	/**
	 * check if the credential is valid
	 * */
	abstract public function check ($credentials):bool;

}

