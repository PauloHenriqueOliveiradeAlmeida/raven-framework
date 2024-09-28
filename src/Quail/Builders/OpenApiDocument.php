<?php

namespace Raven\Quail\Builders;
use stdClass;

class OpenApiDocumentResponse
{
	public function __construct(
		public int $statusCode,
		public ?string $description = "",
		public ?object $content = null
	) {
	}
}
class OpenApiDocumentMethodParameters
{
	public function __construct(public string $in, public string $name)
	{
	}
}

class OpenApiDocumentMethod
{
	/**
	 * @param ?OpenApiDocumentMethodParameters[] $parameters
	 * @param ?OpenApiDocumentResponse[] $responses
	 * @param ?string[] $tags
	 */
	public function __construct(
		public string $methodName,
		public ?array $parameters = null,
		public ?string $operationId = null,
		public ?string $summary = null,
		public ?array $tags = [],
		public ?array $responses = []
	) {
	}
}

class OpenApiDocumentPath
{
	public string $path;
	/**
	 * @var OpenApiDocumentMethod[] $methods
	 */
	public array $methods;
}

class OpenApiDocumentInfo
{
	public string $title;
	public string $description;
	public string $version;
}

class OpenApiDocument
{
	public string $openApiVersion = "3.0.0";
	public OpenApiDocumentInfo $info;

	/**
	 * @var OpenApiDocumentPath[] $paths
	 */
	public array $paths;

	public function toJson()
	{
		$openApiDocumentArray = [
			"openapi" => $this->openApiVersion,
			"info" => $this->info,
			"paths" => new stdClass(),
		];

		foreach ($this->paths as $openApiPath) {
			$path = $openApiPath->path;
			$openApiDocumentArray["paths"]->$path = new stdClass();

			foreach ($openApiPath->methods as $openApiMethod) {
				$method = $openApiMethod->methodName;
				$openApiDocumentArray["paths"]->$path->$method = [
					"operationId" => $openApiMethod->operationId,
					"summary" => $openApiMethod->summary,
					"tags" => $openApiMethod->tags,
					"parameters" => [],
					"responses" => new stdClass(),
				];
				if (!isset($openApiMethod->responses)) {
					continue;
				}
				foreach ($openApiMethod->responses as $openApiResponse) {
					$response = $openApiResponse->statusCode;
					$openApiDocumentArray["paths"]->$path->$method[
						"responses"
					]->$response = [
						"description" => $openApiResponse->description,
						"content" => $openApiResponse->content,
					];
				}

				if (!isset($openApiMethod->parameters)) {
					continue;
				}
				foreach ($openApiMethod->parameters as $openApiParameters) {
					array_push(
						$openApiDocumentArray["paths"]->$path->$method["parameters"],
						[
							"name" => $openApiParameters->name,
							"in" => $openApiParameters->in,
							"required" => true,
						]
					);
				}
			}
		}

		return json_encode($openApiDocumentArray);
	}

	private function removeNullValues(array|object $data)
	{
		$data = (array) $data;

		return array_filter(
			array_map(
				fn($value) => is_array($value) || is_object($value)
					? $this->removeNullValues($value)
					: $value,
				$data
			)
		);
	}
}
