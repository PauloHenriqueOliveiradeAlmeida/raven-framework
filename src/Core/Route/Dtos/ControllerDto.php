<?php

namespace Raven\Core\Route\Dtos;

class ControllerDto
{
	/**
	 * @param HttpMethodDto[] $methods
	 */
	public function __construct(
		public readonly string $endpoint,
		public readonly string $controller,
		public readonly array $methods,
		public readonly array $guards
	) {}
}
