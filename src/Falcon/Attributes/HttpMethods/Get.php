<?php

namespace Raven\Falcon\Attributes\HttpMethods;

#[\Attribute]
class Get implements IHttpMethod
{
	public function __construct(string $endpoint)
	{
	}
}
