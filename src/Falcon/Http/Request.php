<?php

namespace Raven\Falcon\Http;
class Request
{
	public function __construct(
		public readonly Headers $headers,
		public readonly object $body
	) {
	}
}
