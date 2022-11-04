<?php

declare(strict_types=1);

namespace Tests\Container\Ioc;

use ArrayObject;
use DateTime;
use InvalidArgumentException;
use Iquety\Injection\Container;
use Iquety\Injection\InversionOfControl;
use Tests\Ioc\Support\Ioc;
use Tests\Ioc\Support\IocAbstract;
use Tests\Ioc\Support\IocExtended;
use Tests\Ioc\Support\IocInterface;
use Tests\Ioc\Support\IocNoConstructor;
use Tests\TestCase;

include_once __DIR__ . '/Support/IocFunction.php';

class InvocationContractTest extends TestCase
{
    /** @return array<int,mixed> */
    public function contractOkProvider(): array
    {
        return [
            'explicity' =>[ Ioc::class . "::injectedMethod" ],
            'explicity static' => [ Ioc::class . "::injectedStaticMethod" ],
            'contract array' => [ array(Ioc::class, "injectedMethod") ],
            'instance array' => [ array(new Ioc(new ArrayObject()), "injectedMethod") ],
            'instance object' => [ new Ioc(new ArrayObject()) ], // __invoke(ArrayObject $o)
        ];
    }

    /**
     * @test
     * @dataProvider contractOkProvider
     * @param mixed $caller
    */
    public function contractOk($caller): void
    {
        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        // $caller <- injeta ArrayObject como argumento

        $value = $control->resolveOnly(Ioc::class, $caller);
        $value = $control->resolveOnly(IocAbstract::class, $caller);
        $value = $control->resolveOnly(IocInterface::class, $caller);
        
        $this->assertEquals([ 'x' ], $value);
    }

    /**
     * @test
     * @dataProvider contractOkProvider
     * @param mixed $caller
    */
    public function contractError($caller): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Class type .* is not allowed/');

        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        // $caller <- injeta ArrayObject como argumento

        $value = $control->resolveOnly(DateTime::class, $caller);

        $this->assertEquals([ 'x' ], $value);
    }

    /** @test */
    public function contractErrorClosure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Class type .* is not allowed/');

        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        // $caller <- injeta ArrayObject como argumento

        $value = $control->resolveOnly(
            DateTime::class,
            fn(ArrayObject $object) => $object->getArrayCopy()
        );

        $this->assertEquals([ 'x' ], $value);
    }

    /** @test */
    public function contractErrorFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Type .* do not have contracts/');

        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        // $caller <- injeta ArrayObject como argumento

        $value = $control->resolveOnly(DateTime::class, "declaredFunction");

        $this->assertEquals([ 'x' ], $value);
    }
}
