<?php
namespace Peak\MicroService\Auth;

use \Peak\MicroService\Auth as Raw;

class Token extends Raw
{

	protected $config = [
		'id' => null,
		'key' => null,
		'exp' => null,
	];

    use \Peak\Plugin\Debuger\Base;

	/**
	 * sign the credential config param
	 * @param string id
	 * @param string key
	 * @param int $time random string to make sign token
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
	    $time = time();
		return [
			'id' => $this->config['id'],
			'token' => self::sign($this->config['id'], $this->config['key'], $time),
			'timestamp' => $time
		];
	}



	public function check ($credential):bool
	{
		if ($credential['timestamp']+(int)$this->config['exp']<time()) {
			return (bool)self::debug('请求超时。');
		}

		if ($credential['id']!=$this->config['id']) {
			return (bool)self::debug('appid不存在。');
		}

		if ($credential['token']!=$this->sign($credential['id'], $this->config['key'], $credential['timestamp'])) {
			return (bool)self::debug('签名错误。');
		}

		return true;

	}

}
