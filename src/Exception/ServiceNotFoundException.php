<?php

namespace Dispify\Weaver\Exception;

use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends \RuntimeException implements NotFoundExceptionInterface, WeaverExceptionInterface
{
}
