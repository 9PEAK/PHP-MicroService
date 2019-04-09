<?php
namespace Peak\MicroService\Auth;

class Token extends \Peak\MicroService\Auth
{

	private $config = [
		'id' => null,
		'key' => null,
		'exp' => null,
	];


	/**
	 * sign the credential config param
	 * @param id
	 * @param key
	 * @param $str string random string to make sign token
	 * */
	private static function sign ($id, $key, $time)
	{
		return md5(
			\Peak\Plugin\Arr::joinKeyValToString([
				'app_id' => $id,
				'app_key' => $key,
				'timestamp' => $time,
			])
		);
	}



	public function attempt ()
	{
		return [
			'id' => $this->config['id'],
			'token' => self::sign($this->config['id'], $this->config['key'], time()),
			'timestamp' => time()
		];
	}



	public function check ($credential):bool
	{
		if ($credential['timestamps']+$this->config['exp']<time()) {
			return $this->debug('请求超时。', -1);
		}

		if ($credential['id']!=$this->config['id']) {
			return $this->debug('appid不存在。', -2);
		}

		if ($credential['token']!=$this->sign($credential['id'], $this->config['key'], $credential['timestamp'])) {
			return $this->debug('签名错误。', -3);
		}

		return true;

	}

}

