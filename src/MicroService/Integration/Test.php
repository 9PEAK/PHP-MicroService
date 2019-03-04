<?php
namespace Peak\MicroService\Integration;

class Test extends \Peak\MicroService\Core
{

	const API_URL = 'http://abc.com/';


	protected function handle($url, $param, $method)
	{
		if ($this->request($url, $param, $method)) {
			if (self::response('res')==1) {
				$this->result = self::response('dat');
				return true;
			} else {
				$this->result = self::$http->response;
			}
		}

		return false;
	}



	public function test($param):bool
	{
		return $this->handle(
			self::API_URL.'sfsfsdf',
			$param,
			'post'
		);
	}


}
