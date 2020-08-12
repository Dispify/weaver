<?php

namespace Dispify\Weaver\Tests\Stub;

class WithInterfaceArgument
{
    public $arg;
    public $dependency;

    public function __construct($arg, DependencyInterface $dependency)
    {
        $this->arg = $arg;
        $this->dependency = $dependency;
    }
}
