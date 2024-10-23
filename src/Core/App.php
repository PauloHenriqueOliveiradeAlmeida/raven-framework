<?php

namespace Raven\Core;

use Raven\Core\Exceptions\ExceptionHandler;
use Raven\Core\Route\RouteHandler;

set_exception_handler(ExceptionHandler::throwException(...));

class App
{
  public function __construct(AppConfig $appConfig)
  {
    $envFileExists = file_exists(__DIR__ . '/../../../../../.env');

    if ($envFileExists && getenv('ENVIROMENT') !== 'production') {
      $envFile = file(__DIR__ . '/../../../../../.env');
      foreach ($envFile as $line) {
        if (trim($line) === '' || trim($line)[0] === '#') continue;

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
