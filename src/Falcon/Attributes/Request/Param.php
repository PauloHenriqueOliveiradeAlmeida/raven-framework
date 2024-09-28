<?php

namespace Raven\Falcon\Attributes\Request;

use Attribute;
use Raven\Falcon\Http\Exceptions\BadRequestException;

#[Attribute]
class Param
{
	public function __construct(
		private readonly string $paramName
	) {}

	public function convertRequestToData($request, string $dataType)
	{
		if (!array_key_exists($this->paramName, $request)) throw new BadRequestException('No param provided, but is required');
		$param = $request[$this->paramName];
		settype($param, $dataType);

		return $param;
	}
}
