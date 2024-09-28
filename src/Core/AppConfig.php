<?php

namespace Raven\Core;

class AppConfig
{
	public function __construct(
		public array $controllers,
		public string $basePath = "",
		public ?array $staticFiles = [
			"endpoint" => "/public",
			"folder" => __DIR__ . "/public",
		],
		public ?array $methodsAllowed = [
			"POST",
			"GET",
			"PUT",
			"PATCH",
			"DELETE",
			"HEAD",
			"OPTIONS",
		]
	) {
	}
}
