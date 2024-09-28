<?php

namespace Raven\Falcon\Attributes\Middlewares\Guard;

use Attribute;
use Raven\Falcon\Http\Request;

#[Attribute]
class UseGuard
{
	public function __construct(private readonly IGuard $iGuard) {}

	public function verify(Request $request)
	{
		return $this->iGuard->verify($request);
	}
}
