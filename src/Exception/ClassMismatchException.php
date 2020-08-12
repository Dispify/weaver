<?php

namespace Dispify\Weaver\Exception;

class ClassMismatchException extends \InvalidArgumentException implements WeaverExceptionInterface
{
    private $expectedClass;
    private $actualClass;

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function __construct($expected, $actual)
    {
        $this->expectedClass = \is_object($expected) ? \get_class($expected) : (string) $expected;
        $this->actualClass = \is_object($actual) ? \get_class($actual) : (string) $actual;

        parent::__construct('Expected object of class "' . $this->expectedClass . '", given "' . $this->actualClass . '"');
    }

    public function getExpectedClass(): string
    {
        return $this->expectedClass;
    }

    public function getActualClass(): string
    {
        return $this->actualClass;
    }
}
