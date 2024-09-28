<?php

namespace Raven\Quail;

use Raven\Core\AppConfig;
use Raven\Core\Route\EndpointBuilder;
use Raven\Core\Route\RouteHandler;
use Raven\Falcon\Attributes\Controller;
use Raven\Falcon\Attributes\HttpMethods\IHttpMethod;
use Raven\Quail\Builders\OpenApiDocumentMethod;
use Raven\Quail\Builders\OpenApiDocumentBuilder;
use Raven\Quail\Builders\OpenApiDocumentMethodParameters;
use ReflectionAttribute;
use ReflectionClass;

class Documentation
{
	public function __construct(private readonly AppConfig $appConfig)
	{
	}

	public function setup(
		string $endpoint,
		OpenApiDocumentBuilder $documentBuilder
	) {
		$this->buildDocument($documentBuilder);
		$routeHandler = new RouteHandler($this->appConfig);
		$routeHandler->serveStaticFiles(__DIR__ . "/public", $endpoint);
	}

	private function buildDocument(OpenApiDocumentBuilder $documentBuilder)
	{
		foreach ($this->appConfig->controllers as $controller) {
			$reflectedController = new ReflectionClass("\\$controller");
			$controllerEndpoint = $reflectedController->getAttributes(
				Controller::class
			)[0];

			$path = EndpointBuilder::set(
				$controllerEndpoint->getArguments()["endpoint"],
				""
			)
				->withBase($this->appConfig->basePath)
				->get()->endpoint;
			$documentBuilder->addPath($path);

			foreach ($reflectedController->getMethods() as $controllerMethod) {
				$httpMethodAttributes = $controllerMethod->getAttributes(
					IHttpMethod::class,
					ReflectionAttribute::IS_INSTANCEOF
				);
				if (count($httpMethodAttributes) === 0) continue;
				$controllerHttpMethod = $httpMethodAttributes[0];
				$httpMethodName = strtoupper(
					substr(
						$controllerHttpMethod->getName(),
						strrpos($controllerHttpMethod->getName(), "\\") + 1
					)
				);
				$childrenPath = $path;
				$endpointBuilder = null;
				if (
					array_key_exists("endpoint", $controllerHttpMethod->getArguments())
				) {
					$endpointBuilder = EndpointBuilder::set(
						$controllerHttpMethod->getArguments()["endpoint"],
						$_SERVER["REQUEST_URI"]
					)->withBase($path);
					$childrenPath = preg_replace(
						"/:(\w+)/",
						'{$1}',
						$endpointBuilder->get()->endpoint
					);
					if ($childrenPath !== $endpointBuilder->get()->endpoint) {
					}
					$documentBuilder->addPath($childrenPath);
				}
				preg_match_all("/\{(.*?)\}/", $childrenPath, $parameters);
				$parameters = array_map(
					fn($param) => new OpenApiDocumentMethodParameters("path", $param),
					$parameters[1]
				);

				$OAMethod = new OpenApiDocumentMethod(
					$httpMethodName,
					$parameters,
					tags: [$path]
				);
				$documentBuilder->addMethodPath($childrenPath, $OAMethod);
			}
		}
		$OADocument = $documentBuilder->build();
		file_put_contents(__DIR__ . "/public/swagger.json", $OADocument->toJson());
	}
}
