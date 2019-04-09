<?php
namespace Peak\MicroService\Integration;

class Test extends \Peak\MicroService\Core
{

	/**
	 * 请求&返回值处理
	 * */
	protected function handle($url, $param, $method):bool
	{
		if ($this->request($url, $param, $method)) {

			if (is_string($this->result) ) {
				$this->result = self::response();
				return false;
			}

			if (self::response()->res==1) {
				$this->result = self::response()->dat;
				return true;
			}
			$this->result = self::response();
		}

		return false;
	}


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
