<?php

namespace Raven\Cassowary\Validators;

use Attribute;
use Raven\Cassowary\Validators\IValidator;
use Raven\Falcon\Http\Exceptions\BadRequestException;

#[Attribute]
class IsString implements IValidator
{
	public function __construct(private readonly ?string $message = null) {}

	public function validate(string $propertyName, $value): bool
	{
		if (isset($value) && gettype($value) !== 'string') {
			throw new BadRequestException(
				$this->message ?? "$propertyName is not a valid type, expected string"
			);
		}

		return true;
	}
}
