<?php

namespace Raven\Falcon\Http;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class Headers
{
  public ?string $authorization = null;
}
