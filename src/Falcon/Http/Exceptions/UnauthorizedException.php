<?php

namespace Raven\Falcon\Http\Exceptions;

use Exception;
use Raven\Falcon\Http\StatusCode;

class UnauthorizedException extends Exception
{
	public function __construct(string $message = null, $code = StatusCode::UNAUTHORIZED, \Exception $previous = null)
	{
		parent::__construct($message ?? 'Unauthorized', $code->value, $previous);
		header('Content-Type: application/json');
	}
}
