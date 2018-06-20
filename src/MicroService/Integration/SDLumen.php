<?php
namespace Peak\MicroService\Integration;

class SDLumen extends \Peak\MicroService\Core {

	use Base;

	function __construct(array $auth, array $config = [], $apiKey)
	{
		parent::__construct($auth, $config);

		self::$req_param['apikey'] = $apiKey;
	}



	protected static function searchProduct (array &$param)
	{
		return [

		];
	}


	// 物流方式
	protected static function transportType (array &$param)
	{
		return [
			'store_name' => $param['store_name']
		];
	}

}
