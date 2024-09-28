<?php

namespace Raven\Falcon\Http;

class Response
{
	public static function send(?StatusCode $http_status_code = StatusCode::OK)
	{
		http_response_code($http_status_code->value);
		die();
	}

	/**
	 * @param ?StatusCode|?int $httpStatusCode is a response http code status
	 */
	public static function sendBody(array $body, $httpStatusCode = StatusCode::OK)
	{
		header('Content-Type: application/json');
		http_response_code($httpStatusCode instanceof StatusCode ? $httpStatusCode->value : $httpStatusCode);
		echo json_encode($body);
		die();
	}
}
