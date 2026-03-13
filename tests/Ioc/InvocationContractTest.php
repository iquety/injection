<?php

declare(strict_types=1);

namespace Tests\Ioc;

use ArrayObject;
use DateTime;
use InvalidArgumentException;
use Iquety\Injection\Container;
use Iquety\Injection\InversionOfControl;
use Tests\Ioc\Support\Ioc;
use Tests\Ioc\Support\IocAbstract;
use Tests\Ioc\Support\IocInterface;
use Tests\TestCase;

class InvocationContractTest extends TestCase
{
    /** @return array<string,mixed> */
    public function contractOkProvider(): array
    {
        return [
            'explicity' => [ Ioc::class . '::injectedMethod' ],
            'explicity static' => [ Ioc::class . '::injectedStaticMethod' ],
            'contract array' => [ [Ioc::class, 'injectedMethod'] ],
            'instance array' => [ [new Ioc(new ArrayObject()), 'injectedMethod'] ],
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
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        // $caller <- injeta ArrayObject como argumento

        $value = $control->resolveTo(Ioc::class, $caller);
        $value = $control->resolveTo(IocAbstract::class, $caller);
        $value = $control->resolveTo(IocInterface::class, $caller);

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
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        // $caller <- injeta ArrayObject como argumento

        $value = $control->resolveTo(DateTime::class, $caller);

        $this->assertEquals([ 'x' ], $value);
    }

    /** @test */
    public function contractErrorClosure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Class type .* is not allowed/');

        $container = new Container();
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        // $caller <- injeta ArrayObject como argumento

        $value = $control->resolveTo(
            DateTime::class,
            fn(ArrayObject $object) => $object->getArrayCopy()
        );

        $this->assertEquals([ 'x' ], $value);
    }

    /** @test */
    public function contractErrorFunction(): void
    {
        include_once __DIR__ . '/Support/IocFunction.php';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Type .* do not have contracts/');

        $container = new Container();
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);

        // $caller <- injeta ArrayObject como argumento

        $value = $control->resolveTo(DateTime::class, 'declaredFunction');

        $this->assertEquals([ 'x' ], $value);
    }
}
