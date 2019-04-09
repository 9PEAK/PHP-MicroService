<?php
namespace Peak\MicroService;

trait IoC
{

	protected function peakMicroService ($cls, Auth $auth, $type='Laravel')
	{
		if (is_subclass_of($cls, Core::class)) {
			switch ($type) {
				case 'Laravel':
					$this->app->singleton(
						$cls,
						function () use (&$cls, &$auth) {
							return new $cls($auth);
						});
					break;
			}
		}
	}

}
