<?php

namespace Raven\Core;

use Raven\Core\Exceptions\ExceptionHandler;
use Raven\Core\Route\RouteHandler;

set_exception_handler(ExceptionHandler::throwException(...));

class App
{
	public function __construct(AppConfig $appConfig)
	{
		$envFile = file(__DIR__ . '/../../../../.env');

		if ($envFile) {
			foreach ($envFile as $line) {
				if (trim($line)[0] === '#' || trim($line) === '') continue;

				$line = trim(str_replace('\\n', '', $line));
				putenv($line);
			}
		}

		$routeHandler = new RouteHandler($appConfig);
		$routeHandler->manageRoute();
	}

	public static function bootstrap(AppConfig $appConfig)
	{
		return new static($appConfig);
	}
}
