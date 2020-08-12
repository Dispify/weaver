<?php

namespace Dispify\Weaver\Exception;

use Psr\Container\ContainerExceptionInterface;

class AutowiringFailedException extends \RuntimeException implements ContainerExceptionInterface, WeaverExceptionInterface
{
}
