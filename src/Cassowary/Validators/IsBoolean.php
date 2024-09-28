<?php

namespace Raven\Cassowary\Validators;

use Attribute;
use Raven\Cassowary\Validators\IValidator;
use Raven\Falcon\Http\Exceptions\BadRequestException;

#[Attribute]
class IsBoolean implements IValidator
{
	public function __construct(private readonly ?string $message = null) {}

	public function validate(string $propertyName, $value): bool
	{
		if (gettype($value) !== 'boolean') {
			throw new BadRequestException(
				$this->message ?? "$propertyName is not a valid type, expected boolean"
			);
		}

		return true;
	}
}
