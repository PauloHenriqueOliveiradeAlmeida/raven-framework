<?php

namespace Raven\Falcon\Attributes\HttpMethods;

#[\Attribute]
class Patch implements IHttpMethod
{
	public function __construct(string $endpoint)
	{
	}
}
