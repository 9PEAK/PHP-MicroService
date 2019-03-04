<?php
namespace Peak\MicroService;

//use Peak\Plugin\;

abstract class Auth {

	private $config;

	function __construct($config)
	{
		$this->config = $config;
	}


	use \Peak\Plugin\Debuger;

	/**
	 * set a new credentials with config
	 * */
	abstract public function attempt ();

	/**
	 * check if the credential is valid
	 * */
	abstract public function check ($credentials):bool;

}

