<?php

namespace Raven\Falcon\Attributes\Middlewares\Guard;

use Raven\Falcon\Http\Request;

interface IGuard
{
	public function verify(Request $request): bool;
}
