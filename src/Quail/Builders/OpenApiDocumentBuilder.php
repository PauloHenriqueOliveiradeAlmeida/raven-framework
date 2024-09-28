<?php

namespace Raven\Quail\Builders;

use Raven\Quail\Builders\OpenApiDocument;
use Raven\Quail\Builders\OpenApiDocumentInfo;
use Raven\Quail\Builders\OpenApiDocumentMethod;
use Raven\Quail\Builders\OpenApiDocumentPath;
use Raven\Quail\Builders\OpenApiDocumentResponse;

class OpenApiDocumentBuilder
{
	private OpenApiDocument $openApiDocument;

	public function __construct(
		string $title,
		string $description,
		string $version
	) {
		$openApiDocumentInfo = new OpenApiDocumentInfo();
		$openApiDocumentInfo->title = $title;
		$openApiDocumentInfo->description = $description;
		$openApiDocumentInfo->version = $version;

		$this->openApiDocument = new OpenApiDocument();
		$this->openApiDocument->info = $openApiDocumentInfo;
		$this->openApiDocument->paths = [];
	}

	public static function set(
		string $title,
		string $description,
		string $version
	) {
		return new static($title, $description, $version);
	}

	public function addPath(string $path)
	{
		$openApiDocumentPath = new OpenApiDocumentPath();
		$openApiDocumentPath->path = $path;
		$openApiDocumentPath->methods = [];
		array_push($this->openApiDocument->paths, $openApiDocumentPath);
		return $this;
	}

	public function addMethodPath(string $path, OpenApiDocumentMethod $method)
	{
		$method->methodName = strtolower($method->methodName);

		foreach ($this->openApiDocument->paths as $openApiDocumentPath) {
			if ($openApiDocumentPath->path === $path) {
				array_push($openApiDocumentPath->methods, $method);
			}
		}

		return $this;
	}

	public function addResponseMethodPath(
		string $path,
		string $method,
		OpenApiDocumentResponse $response
	) {
		foreach ($this->openApiDocument->paths as $openApiDocumentPath) {
			if ($openApiDocumentPath->path !== $path) {
				continue;
			}

			foreach ($openApiDocumentPath->methods as $openApiDocumentMethod) {
				if ($openApiDocumentMethod->methodName === strtolower($method)) {
					array_push($openApiDocumentMethod->responses, $response);
				}
			}
		}

		return $this;
	}

	public function build()
	{
		return $this->openApiDocument;
	}
}
