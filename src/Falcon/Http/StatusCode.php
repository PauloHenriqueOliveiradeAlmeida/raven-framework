<?php

namespace Raven\Falcon\Http;

enum StatusCode: int
{
	case CREATED = 201;
	case OK = 200;
	case NO_CONTENT = 204;
	case BAD_REQUEST = 400;
	case UNAUTHORIZED = 401;
	case NOT_FOUND = 404;
	case METHOD_NOT_ALLOWED = 405;
	case CONFLICT = 409;
	case SERVER_ERROR = 500;
	case SERVICE_UNAVAILABLE = 503;
}
