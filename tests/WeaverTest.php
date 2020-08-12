<?php

namespace Dispify\Weaver\Tests;

use Dispify\Weaver\Exception\AutowiringFailedException;
use Dispify\Weaver\Exception\ClassMismatchException;
use Dispify\Weaver\Exception\ServiceNotFoundException;
use Dispify\Weaver\Exception\UndefinedClassException;
use Dispify\Weaver\Tests\Stub\AliasImplementation;
use Dispify\Weaver\Tests\Stub\ClassImplementation;
use Dispify\Weaver\Tests\Stub\WithClassArgument;
use Dispify\Weaver\Tests\Stub\WithManyArguments;
use Dispify\Weaver\Tests\Stub\WithOptionalArgument;
use Dispify\Weaver\Tests\Stub\WithVariadicArgument;
use Dispify\Weaver\Weaver;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class WeaverTest extends TestCase
{
    public function testWeave()
    {
        $weaver = new Weaver();
        $weaver->weave(\stdClass::class);

        self::assertInstanceOf(\stdClass::class, $weaver->get(\stdClass::class));
        self::assertSame($weaver->get(\stdClass::class), $weaver->get(\stdClass::class));
    }

    public function testWeaveUndefined()
    {
        $weaver = new Weaver();

        self::expectException(UndefinedClassException::class);
        $weaver->weave(\undefinedClass::class);
    }

    public function testUndefinedService()
    {
        $weaver = new Weaver();

        self::expectException(NotFoundExceptionInterface::class);
        self::assertEquals(false, $weaver->has(\stdClass::class));
        $weaver->get(\stdClass::class);
    }

    public function testUndefinedArgument()
    {
        $weaver = new Weaver();
        $weaver->weave(ClassImplementation::class);

        self::expectException(AutowiringFailedException::class);
        $weaver->get(ClassImplementation::class);
    }

    public function testSetMismatchedClass()
    {
        self::expectException(ClassMismatchException::class);
        (new Weaver())->set(self::class, new \stdClass);
    }

    public function testVariadicArgument()
    {
        $weaver = (new Weaver())->set('arg', 'arg');
        $weaver->weave(WithVariadicArgument::class);

        self::assertInstanceOf(WithVariadicArgument::class, $weaver->get(WithVariadicArgument::class));
    }

    public function testWeaveNamedArgument()
    {
        $weaver = new Weaver();
        $weaver->weave(WithManyArguments::class, ['arg1' => 'arg1', 'arg2' => 'arg2', 'arg3' => 'arg3']);

        self::assertInstanceOf(WithManyArguments::class, $weaver->get(WithManyArguments::class));
        self::assertSame('arg1', $weaver->get(WithManyArguments::class)->arg1);
        self::assertSame('arg3', $weaver->get(WithManyArguments::class)->arg3);
    }

    public function testWeaveIndexedArgument()
    {
        $weaver = new Weaver();
        $weaver->weave(WithManyArguments::class, ['arg1', 'arg2', 'arg3']);

        self::assertInstanceOf(WithManyArguments::class, $weaver->get(WithManyArguments::class));
        self::assertSame('arg1', $weaver->get(WithManyArguments::class)->arg1);
        self::assertSame('arg3', $weaver->get(WithManyArguments::class)->arg3);
    }

    public function testWeaveOptionalArgument()
    {
        $weaver = (new Weaver())->set('arg', 'arg');
        $weaver->weave(WithOptionalArgument::class);

        self::assertInstanceOf(WithOptionalArgument::class, $weaver->get(WithOptionalArgument::class));
        self::assertSame('optional', $weaver->get(WithOptionalArgument::class)->optional);
    }

    public function testWeaveAutoconfiguredArgument()
    {
        $weaver = (new Weaver())->set('arg', 'arg');
        $weaver->weave(WithClassArgument::class);

        self::assertInstanceOf(WithClassArgument::class, $weaver->get(WithClassArgument::class));
        self::assertInstanceOf(ClassImplementation::class, $weaver->get(WithClassArgument::class)->dependency);
    }

    public function testWeaveInterfacedArgument()
    {
        $weaver = (new Weaver())->set('arg', 'arg');
        $weaver->weave(WithClassArgument::class);

        self::assertSame(false, $weaver->has(ClassImplementation::class));
        self::assertInstanceOf(WithClassArgument::class, $weaver->get(WithClassArgument::class));
        self::assertInstanceOf(ClassImplementation::class, $weaver->get(WithClassArgument::class)->dependency);
    }

    public function testWeaveAliasedArgument()
    {
        $weaver = (new Weaver())->set('arg', 'arg');
        $weaver->weave(WithClassArgument::class);
        $weaver->weave(AliasImplementation::class);

        self::assertSame($weaver->get(AliasImplementation::class), $weaver->get(ClassImplementation::class));
        self::assertInstanceOf(WithClassArgument::class, $weaver->get(WithClassArgument::class));
        self::assertInstanceOf(ClassImplementation::class, $weaver->get(WithClassArgument::class)->dependency);
    }
}
