<?php
namespace Peak\MicroService\Integration;

class Test extends \Peak\MicroService\Core
{


	const API_URL = 'http://sd-laravel/ms/';

	public function test($id)
	{
		return $this->handle(
			self::API_URL.'product/detail',
			['id' => $id],
			'post'
		);
	}


}
