<?php

namespace Raven\Falcon\Attributes\HttpMethods;

#[\Attribute]
class Delete implements IHttpMethod
{

	public function __construct(string $endpoint)
	{
	}
}
