<?php

namespace Raven\Falcon\Http\Exceptions;

use Exception;
use Raven\Falcon\Http\StatusCode;

class ConflictException extends Exception
{
  public function __construct(string $message = null, $code = StatusCode::CONFLICT, \Exception $previous = null)
  {
    parent::__construct($message ?? 'Conflict', $code->value, $previous);
    header('Content-Type: application/json; charset=utf-8');
  }
}
