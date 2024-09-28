<?php

namespace Raven\Cassowary\Validators;

use Attribute;
use Raven\Cassowary\Validators\IValidator;
use Raven\Falcon\Http\Exceptions\BadRequestException;

#[Attribute]
class IsInteger implements IValidator
{
	public function __construct(private readonly ?string $message = null) {}

	public function validate(string $propertyName, $value): bool
	{
		if (isset($value) && gettype($value) !== 'integer') {
			throw new BadRequestException(
				$this->message ?? "$propertyName is not a valid type, expected integer"
			);
		}

		return true;
	}
}
