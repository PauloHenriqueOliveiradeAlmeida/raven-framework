<?php

namespace Raven\Core\Exceptions;

use Raven\Falcon\Http\Response;

final class ExceptionHandler
{
	public static function throwException(\Throwable $exception)
	{
		if ($exception instanceof \Exception) {
			return Response::sendBody([
				"message" => $exception->getMessage()
			], $exception->getCode());
		}
		throw $exception;
	}
}
