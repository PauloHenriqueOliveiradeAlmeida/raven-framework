<?php

namespace Raven\Cassowary\Validators;

use Attribute;
use Raven\Cassowary\Validators\IValidator;
use Raven\Falcon\Http\Exceptions\BadRequestException;

#[Attribute]
class IsFloat implements IValidator
{
	public function __construct(private readonly ?string $message = null) {}

	public function validate(string $propertyName, $value): bool
	{
		if (gettype($value) !== 'double') {
			throw new BadRequestException(
				$this->message ?? "$propertyName is not a valid type, expected float"
			);
		}

		return true;
	}
}
