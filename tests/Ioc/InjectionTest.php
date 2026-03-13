<?php

declare(strict_types=1);

namespace Tests\Ioc;

use ArrayObject;
use Iquety\Injection\Container;
use Iquety\Injection\ContainerException;
use Iquety\Injection\InversionOfControl;
use Iquety\Injection\NotFoundException;
use stdClass;
use Tests\Ioc\Support\Ioc;
use Tests\Ioc\Support\IocNoConstructor;
use Tests\TestCase;

class InjectionTest extends TestCase
{
    /** @test */
    public function injectDependencyInConstructor(): void
    {
        $container = new Container();
        $container->addFactory(ArrayObject::class, fn() => new ArrayObject());
        $container->addFactory(stdClass::class, fn() => new stdClass());

        $control = new InversionOfControl($container);

        // o método ContainerIoc->values devolve um array com os valores setados
        // no construtor __construct(ArrayObject $object, stdClass $class = null)
        $results = $control->resolve(Ioc::class . '::values');
        $this->assertInstanceOf(ArrayObject::class, $results[0]);
        $this->assertInstanceOf(stdClass::class, $results[1]);
    }

    /** @test */
    public function injectNotFoundDependencyInConstructor(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'It was not possible to resolve the value for parameter ($object) in method (__construct)'
        );

        $control = new InversionOfControl(new Container());
        $control->resolve(Ioc::class . '::values');
    }

    /** @test */
    public function containerException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Impossible to inject string dependency');

        $control = new InversionOfControl(new Container());
        $control->resolve('values');
    }

    /** @test */
    public function injectNotFoundDependencyInMethod(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'It was not possible to resolve the value for parameter ($object) in method (injectedMethod)'
        );

        $control = new InversionOfControl(new Container());
        $control->resolve(IocNoConstructor::class . '::injectedMethod');
    }
}
