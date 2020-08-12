<?php

namespace Dispify\Weaver\Exception;

use Psr\Container\ContainerExceptionInterface;

class UndefinedClassException extends \InvalidArgumentException implements ContainerExceptionInterface, WeaverExceptionInterface
{
}
