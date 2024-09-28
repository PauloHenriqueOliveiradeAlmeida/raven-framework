<?php

namespace Raven\Falcon\Http\Exceptions;

use Raven\Falcon\Http\StatusCode;

class MethodNotAllowedException extends \Exception
{
	public function __construct(string $message = null, $code = StatusCode::METHOD_NOT_ALLOWED, \Exception $previous = null)
	{
		parent::__construct($message ?? 'Method not allowed', $code->value, $previous);
		header('Content-Type: application/json');
	}
}
