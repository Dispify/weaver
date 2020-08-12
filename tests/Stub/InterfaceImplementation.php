<?php

namespace Dispify\Weaver\Tests\Stub;

class InterfaceImplementation implements DependencyInterface
{
    public $arg;

    public function __construct($arg)
    {
        $this->arg = $arg;
    }
}
