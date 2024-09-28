<?php

namespace Raven\Falcon\Http\Exceptions;

use Raven\Falcon\Http\StatusCode;

class BadRequestException extends \Exception
{
	public function __construct(string $message = null, $code = StatusCode::BAD_REQUEST, \Exception $previous = null)
	{
		parent::__construct($message ?? 'Bad Request', $code->value, $previous);
		header('Content-Type: application/json; charset=utf-8');
	}
}
