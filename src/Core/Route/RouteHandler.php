<?php

namespace Raven\Core\Route;

use Raven\Cassowary\Validators\IValidator;
use Raven\Core\AppConfig;
use Raven\Core\Route\Dtos\ControllerDto;
use Raven\Core\Route\Dtos\HttpMethodDto;
use Raven\Falcon\Attributes\Controller;
use Raven\Falcon\Attributes\HttpMethods\IHttpMethod;
use Raven\Falcon\Attributes\Middlewares\Guard\UseGuard;
use Raven\Falcon\Attributes\Request\Body;
use Raven\Falcon\Attributes\Request\Param;
use Raven\Falcon\Http\Exceptions\MethodNotAllowedException;
use Raven\Falcon\Http\Exceptions\NotFoundException;
use Raven\Falcon\Http\Exceptions\UnauthorizedException;
use Raven\Falcon\Http\Headers;
use Raven\Falcon\Http\Request;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

final class RouteHandler
{
	/**
	 * @var ControllerDto[]
	 */
	private array $controllers = [];

	public function __construct(private readonly AppConfig $appConfig) {}

	public function serveStaticFiles(string $folder, string $endpoint)
	{
		$requestedUrl = $this->sanitizeUrl($_SERVER["REQUEST_URI"]);
		$requestedMethod = $_SERVER["REQUEST_METHOD"];

		if (
			$requestedMethod !== "GET" ||
			!in_array("GET", $this->appConfig->methodsAllowed)
		) {
			return;
		}

		$staticFiles = scandir($folder);

		if (!$staticFiles) {
			throw new \Error("Invalid Folder");
		}

		foreach ($staticFiles as $entry) {
			if (is_dir($entry)) {
				continue;
			}
			$endpointBuilder = EndpointBuilder::set($entry, $requestedUrl)
				->withBase($endpoint)
				->get();

			if ($endpointBuilder->endpoint !== $requestedUrl) {
				continue;
			}

			header("Content-Type: " . mime_content_type("$folder/$entry"));
			readfile("$folder/$entry");
			exit();
		}
	}

	public function manageRoute()
	{
		foreach ($this->appConfig->controllers as $route) {
			array_push($this->controllers, $this->extractRouteData($route));
		}

		$requestedUrl = $this->sanitizeUrl($_SERVER["REQUEST_URI"]);
		$requestedMethod = $_SERVER["REQUEST_METHOD"];

		foreach ($this->controllers as $controllerData) {

			foreach ($controllerData->methods as $method) {
				$endpoint = EndpointBuilder::set($method->endpoint, $requestedUrl)
					->withBase($controllerData->endpoint)
					->withBase($this->appConfig->basePath)
					->withParameters()
					->get();

				if ($endpoint->endpoint === $requestedUrl) {
					if (
						!in_array($method->httpMethodName, $this->appConfig->methodsAllowed)
					) {
						throw new MethodNotAllowedException();
					}
					if ($controllerData->guards) {
						$headers = new Headers;
						foreach (getallheaders() as $key => $value) {
							$separatedKey = explode('-', $key);
							$serializedKey = '';
							foreach ($separatedKey as $keyIndex => $keyPart) {
								if ($keyIndex === 0) {
									$serializedKey .= strtolower($keyPart[0]) . substr($keyPart, 1);
									continue;
								}
								$serializedKey .= strtoupper($keyPart[0]) . substr($keyPart, 1);
							}
							$headers->{$serializedKey} = $value;
						}
						$request = new Request($headers, (object) file_get_contents('php://input'));

						foreach ($controllerData->guards as $guard) {
							$verified = ($guard->newInstance())->verify($request);
							if (!$verified)
								throw new UnauthorizedException('Permission Denied');
						}
					}

					if ($method->httpMethodName === $requestedMethod) {
						$controllerInstance = new $controllerData->controller();

						$controllerMethod = new ReflectionMethod($controllerInstance, $method->controllerMethod);
						$parameters = [];
						if ($method->guards) {
							$headers = new Headers;
							foreach (getallheaders() as $key => $value) {
								$separatedKey = explode('-', $key);
								$serializedKey = '';
								foreach ($separatedKey as $keyIndex => $keyPart) {
									if ($keyIndex === 0) {
										$serializedKey .= strtolower($keyPart[0]) . substr($keyPart, 1);
										continue;
									}
									$serializedKey .= strtoupper($keyPart[0]) . substr($keyPart, 1);
								}
								$headers->{$serializedKey} = $value;
							}
							$request = new Request($headers, (object) file_get_contents('php://input'));

							foreach ($method->guards as $guard) {
								$verified = ($guard->newInstance())->verify($request);
								if (!$verified)
									throw new UnauthorizedException('Permission Denied');
							}
						}
						foreach ($controllerMethod->getParameters() as $methodParameter) {
							$bodyAttributes = $methodParameter->getAttributes(Body::class, ReflectionAttribute::IS_INSTANCEOF);

							if (count($bodyAttributes) > 0) {
								$request = json_decode(file_get_contents('php://input'), false);

								$attributeInstance = $bodyAttributes[0]->newInstance();
								$parameter = $attributeInstance->convertRequestToData($request, $methodParameter->getType());

								$reflectedClassParameter = new ReflectionClass($parameter::class);
								$parameterProperties = $reflectedClassParameter->getProperties();

								foreach ($parameterProperties as $parameterProperty) {
									foreach ($parameterProperty->getAttributes(IValidator::class, ReflectionAttribute::IS_INSTANCEOF) as $parameterValidatorAttribute) {
										if (isset($parameter->{$parameterProperty->getName()})) {
											$validator = $parameterValidatorAttribute->newInstance();
											$validator->validate($parameterProperty->getName(), $parameter->{$parameterProperty->getName()});
										}
									}
								}

								array_push($parameters, $parameter);
							}
							$paramAttributes = $methodParameter->getAttributes(Param::class, ReflectionAttribute::IS_INSTANCEOF);
							if (count($paramAttributes) === 0)
								continue;

							$reflectedParamAttribute = $paramAttributes[0]->newInstance();
							$param = $reflectedParamAttribute->convertRequestToData($endpoint->parameters, $methodParameter->getType()->getName());

							array_push($parameters, $param);
						}

						return $controllerInstance->{$method->controllerMethod}(...$parameters);
					}
				}
			}
		}
		throw new NotFoundException();
	}

	private function extractRouteData(string $route)
	{
		$reflectedRoute = new ReflectionClass("\\$route");
		$routeEndpoint = $reflectedRoute->getAttributes(Controller::class);
		$routeGuards = $reflectedRoute->getAttributes(UseGuard::class);

		if (count($routeEndpoint) === 0) {
			throw new \Error("nao é uma rota válida");
		}
		$routeEndpoint = $routeEndpoint[0]->getArguments()["endpoint"];

		$httpMethods = [];

		foreach ($reflectedRoute->getMethods() as $routeMethod) {
			$httpMethodAttributes = $routeMethod->getAttributes(
				IHttpMethod::class,
				ReflectionAttribute::IS_INSTANCEOF
			);
			if (count($httpMethodAttributes) === 0) {
				continue;
			}
			$routeHttpMethod = $httpMethodAttributes[0];
			$httpMethodName = strtoupper(
				substr(
					$routeHttpMethod->getName(),
					strrpos($routeHttpMethod->getName(), "\\") + 1
				)
			);
			$methodGuards = $routeMethod->getAttributes(UseGuard::class);
			$httpMethodDto = new HttpMethodDto(
				$httpMethodName,
				$routeMethod->getName(),
				$routeHttpMethod->getArguments()["endpoint"] ?? "",
				$methodGuards
			);

			array_push($httpMethods, $httpMethodDto);
		}
		$controllerDto = new ControllerDto(
			$routeEndpoint,
			$reflectedRoute->getName(),
			$httpMethods,
			$routeGuards
		);
		return $controllerDto;
	}

	/**
	 * Method to sanitize url
	 * @param string $url is a url to sanitize
	 */
	private static function sanitizeUrl(string $url)
	{
		$sanitized = substr($url, strpos($url, "/"));
		$sanitized =
			$sanitized[strlen($sanitized) - 1] === "/"
			? substr($sanitized, 0, strlen($sanitized) - 1)
			: $sanitized;

		return $sanitized;
	}
}
