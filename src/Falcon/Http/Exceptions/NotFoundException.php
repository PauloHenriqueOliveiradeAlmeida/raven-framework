<?php

namespace Raven\Falcon\Http\Exceptions;

use Raven\Falcon\Http\StatusCode;

class NotFoundException extends \Exception
{
	public function __construct(string $message = null, $code = StatusCode::NOT_FOUND, \Exception $previous = null)
	{
		parent::__construct($message ?? 'Not Found', $code->value, $previous);
		header('Content-Type: application/json');
	}
}
