<?php

namespace Raven\Falcon\Attributes\Request;

use Attribute;
use Raven\Falcon\Http\Exceptions\BadRequestException;
use ReflectionClass;

#[Attribute]
class Body
{
	public function convertRequestToData($request, string $dataType)
	{
		if (!$request) throw new BadRequestException('No body provided, but is required');

		$reflectedDto = new ReflectionClass($dataType);
		$dto = $reflectedDto->newInstance();
		foreach ($reflectedDto->getProperties() as $dtoProperty) {
			if ($dtoProperty->isReadOnly()) continue;

			if (!property_exists($request, $dtoProperty->getName()) && !$dtoProperty->getType()->allowsNull()) throw new BadRequestException("Property '{$dtoProperty->getName()}' does not exists on request body, but is required");
			if (!$dtoProperty->getType()->allowsNull() && $request->{$dtoProperty->getName()} === null) throw new BadRequestException("Property '{$dtoProperty->getName()}' does not accept null values, but null provided");

			if ($dtoProperty->getType()->allowsNull() && !isset($request->{$dtoProperty->getName()})) {
				unset($dto->{$dtoProperty->getName()});
				continue;
			}

			$dto->{$dtoProperty->getName()} = $request->{$dtoProperty->getName()};
		}

		return $dto;
	}
}
