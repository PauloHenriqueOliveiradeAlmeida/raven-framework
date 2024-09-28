<?php

namespace Raven\Cassowary\Validators;

use Attribute;
use Raven\Cassowary\Validators\IValidator;
use Raven\Falcon\Http\Exceptions\BadRequestException;

#[Attribute]
class Length implements IValidator
{
	public function __construct(
		private readonly ?int $max = 255,
		private readonly ?int $min = 0,
		private readonly ?string $message = null
	) {}

	public function validate(string $propertyName, $value): bool
	{
		$valueLength = strlen((string)$value);
		if ($valueLength < $this->min || $valueLength > $this->max) {
			throw new BadRequestException(
				$this->message ?? "$propertyName is out of range, must be between {$this->min} and {$this->max}"
			);
		}

		return true;
	}
}
