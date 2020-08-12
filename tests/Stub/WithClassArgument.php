<?php

namespace Dispify\Weaver\Tests\Stub;

class WithClassArgument
{
    public $arg;
    public $dependency;

    public function __construct($arg, ClassImplementation $dependency)
    {
        $this->arg = $arg;
        $this->dependency = $dependency;
    }
}
