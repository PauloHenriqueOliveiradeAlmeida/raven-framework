<?php

namespace Raven\Falcon\Attributes\HttpMethods;

#[\Attribute]
class Put implements IHttpMethod
{
	public function __construct(string $endpoint)
	{
	}
}
