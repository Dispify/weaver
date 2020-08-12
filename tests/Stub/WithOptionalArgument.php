<?php

namespace Dispify\Weaver\Tests\Stub;

class WithOptionalArgument
{
    public $arg;
    public $optional;

    public function __construct($arg, $optional = 'optional')
    {
        $this->arg = $arg;
        $this->optional = $optional;
    }
}
