<?php

namespace Raven\Falcon\Http\Exceptions;

use Exception;
use Raven\Falcon\Http\StatusCode;

class ServiceUnavailableException extends Exception
{
	public function __construct(string $message = null, $code = StatusCode::SERVICE_UNAVAILABLE, \Exception $previous = null)
	{
		parent::__construct($message ?? 'Service Unavailable', $code->value, $previous);
		header('Content-Type: application/json');
	}
}
