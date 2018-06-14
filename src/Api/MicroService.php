<?php
namespace SD\Api;

class MicroService extends Http {

	public function attempt ($auth, $credentials)
	{
		return 'Auth\\'.ucfirst($auth)::attempt($credentials);
	}

}
