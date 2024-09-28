<?php

namespace Raven\Falcon\Attributes\HttpMethods;

#[\Attribute]
class Head implements IHttpMethod
{
	public function __construct(string $endpoint)
	{
	}
}
