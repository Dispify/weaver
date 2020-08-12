<?php

namespace Dispify\Weaver\Tests\Stub;

class WithVariadicArgument
{
    public $arg;

    public function __construct($arg, ...$variadic)
    {
        $this->arg = $arg;
    }
}
