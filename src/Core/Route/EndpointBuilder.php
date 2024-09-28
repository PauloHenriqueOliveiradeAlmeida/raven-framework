<?php

namespace Raven\Core\Route;

class EndpointBuilder
{
	private readonly string $url;

	private Endpoint $endpointData;

	public function __construct(string $endpoint, string $url)
	{
		$this->url = $url;
		$this->endpointData = new Endpoint();
		$this->endpointData->endpoint = $this->formatUrl($endpoint);
	}

	public static function set(string $endpoint, string $url)
	{
		return new static($endpoint, $url);
	}

	public function get()
	{
		return $this->endpointData;
	}

	public function withBase(string $base)
	{
		$endpoint = $this->formatUrl($base);
		$endpoint .= $this->endpointData->endpoint;
		$this->endpointData->endpoint = $endpoint;
		return $this;
	}

	public function withParameters()
	{
		$endpoint = $this->endpointData->endpoint;
		$colons = $this->getColonPositions($endpoint);
		$parameters = [];
		$this->endpointData->parameters = $parameters;

		if (!$colons || count($colons) === 0) {
			return $this;
		}

		foreach ($colons as $colon) {
			if ($colon >= strlen($this->url)) {
				return $this;
			}

			$firstSlashAfterColon = strpos($endpoint, "/", $colon);
			$nameLength = abs($firstSlashAfterColon - $colon);
			$name = substr($endpoint, $colon, $nameLength);
			$name = str_replace("/", "", $name);
			$name = str_replace(":", "", $name);

			$firstSlashAfterColon = strpos($this->url, "/", $colon);
			$valueLength = abs($firstSlashAfterColon - $colon);
			$value = substr($this->url, $colon, $valueLength);
			$value = str_replace("/", "", $value);

			$parameters = [...$parameters, $name => $value];
		}

		if (empty($parameters)) {
			return $this;
		}

		$urlWithParameters = str_replace(
			array_keys($parameters),
			array_values($parameters),
			$endpoint
		);
		$urlWithParameters = str_replace(":", "", $urlWithParameters);
		$this->endpointData->endpoint = $this->formatUrl($urlWithParameters);
		$this->endpointData->parameters = $parameters;

		return $this;
	}

	private function formatUrl(string $url)
	{
		if (strlen($url) === 0) {
			return "";
		}
		$urlLastChar = strlen($url) - 1;
		$url = $url[0] === "/" ? $url : "/$url";
		$url =
			$url[$urlLastChar] === "/"
			? substr(trim($url), 0, $url[$urlLastChar])
			: trim($url);
		return $url;
	}

	private function getColonPositions(string $endpoint)
	{
		$colons = [];
		$lastColonPosition = 0;
		while ($lastColonPosition = strpos($endpoint, ":", $lastColonPosition)) {
			array_push($colons, $lastColonPosition);
			$lastColonPosition = $lastColonPosition + strlen(":");
		}

		return $colons;
	}
}
