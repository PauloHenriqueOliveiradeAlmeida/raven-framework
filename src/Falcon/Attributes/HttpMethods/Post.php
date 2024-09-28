<?php

namespace Raven\Falcon\Attributes\HttpMethods;

#[\Attribute]
class Post implements IHttpMethod
{
	public function __construct(string $endpoint)
	{
	}
}
