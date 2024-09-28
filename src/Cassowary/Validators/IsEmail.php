<?php

namespace Raven\Cassowary\Validators;

use Attribute;
use Raven\Cassowary\Validators\IValidator;
use Raven\Falcon\Http\Exceptions\BadRequestException;

#[Attribute]
class IsEmail implements IValidator
{
	public function __construct(private readonly ?string $message = null) {}

	public function validate(string $propertyName, $value): bool
	{
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			throw new BadRequestException("$value should be a email");
		}
		return true;
	}
}
