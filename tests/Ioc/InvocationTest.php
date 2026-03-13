<?php

declare(strict_types=1);

namespace Tests\Ioc;

use ArrayObject;
use Iquety\Injection\Container;
use Iquety\Injection\InversionOfControl;
use Tests\Ioc\Support\Ioc;
use Tests\Ioc\Support\IocNoConstructor;
use Tests\TestCase;

class InvocationTest extends TestCase
{
    /** @return array<string,mixed> */
    public function methodsInvocationProvider(): array
    {
        return [
            'explicity' => [ Ioc::class . '::injectedMethod' ],
            'explicity no constructor' => [ IocNoConstructor::class . '::injectedMethod' ],
            'explicity static' => [ Ioc::class . '::injectedStaticMethod' ],
            'contract array' => [ [Ioc::class, 'injectedMethod'] ],
            'instance array' => [ [new Ioc(new ArrayObject()), 'injectedMethod'] ],
            'instance object' => [ new Ioc(new ArrayObject()) ], // __invoke(ArrayObject $o)
            'closure' => [ fn(ArrayObject $object) => $object->getArrayCopy() ],
            'function' => [ 'declaredFunction' ],
        ];
    }

    /**
     * @test
     * @dataProvider methodsInvocationProvider
     * @param mixed $caller
    */
    public function runMethod($caller): void
    {
        include_once __DIR__ . '/Support/IocFunction.php';

        $container = new Container();
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);
        $value = $control->resolve($caller); // <- injeta ArrayObject como argumento
        $this->assertEquals([ 'x' ], $value);
    }
}
