<?php
namespace Peak\MicroService;

include '../../vendor/autoload.php';

$api = new \Peak\MicroService\Integration\Test(
	new Auth\Token([
		'id' => 'sd-product',
		'key' => 'U3VzYW4gZHJlc3MgbGlrZSBhIGNoaWxkLg',
		'exp' => 6,
	])
);

if ($api->test(1)) {
	echo 666,'<br>';
}

print_r($api->result);
