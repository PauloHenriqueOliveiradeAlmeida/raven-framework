<?php

namespace Raven\Core\Route\Dtos;

class HttpMethodDto
{

	public function __construct(
		public readonly string $httpMethodName,
		public readonly string $controllerMethod,
		public readonly string $endpoint,
		public readonly array $guards
	) {}
}
