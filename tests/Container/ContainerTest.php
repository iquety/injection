<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;
use Exception;
use Iquety\Injection\Container;
use Iquety\Injection\ContainerException;
use Iquety\Injection\NotFoundException;
use Tests\TestCase;

/** @codeCoverageIgnore */
class ContainerTest extends TestCase
{
    /** @test */
    public function hasId(): void
    {
        $container = new Container();
        $this->assertFalse($container->has('id'));
        $container->addFactory('id', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('id'));

        $container = new Container();
        $this->assertFalse($container->has('id'));
        $container->addSingleton('id', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('id'));
    }

    /** @test */
    public function hasIdPrecedenceSharedBefore(): void
    {
        $container = new Container();

        // registra uma dependencia compartilhada
        $container->addSingleton('id', 'parangarikotirimirruaro');

        // obtém sa listas de dependencias registradas
        $sharedValue = $this->getPropertyValue($container, 'singleton');
        $factoryValue = $this->getPropertyValue($container, 'factory');
        $this->assertTrue(in_array('parangarikotirimirruaro', $sharedValue));
        $this->assertCount(0, $factoryValue);

        // tenta registrar uma dependencia não-compartilhada com mesmo $id
        $container->addFactory('id', 'sobrescreve');

        // valor foi sobrescrito na dependencia compartilhada
        // não é possível "descompartilhar"
        $sharedValue = $this->getPropertyValue($container, 'singleton');
        $factoryValue = $this->getPropertyValue($container, 'factory');
        $this->assertTrue(in_array('sobrescreve', $sharedValue));
        $this->assertCount(0, $factoryValue);
    }

    /** @test */
    public function hasIdPrecedenceSharedAfter(): void
    {
        $container = new Container();

        // registra uma dependencia compartilhada
        $container->addFactory('id', 'parangarikotirimirruaro');

        // obtém sa listas de dependencias registradas
        $sharedValue = $this->getPropertyValue($container, 'singleton');
        $factoryValue = $this->getPropertyValue($container, 'factory');
        $this->assertCount(0, $sharedValue);
        $this->assertTrue(in_array('parangarikotirimirruaro', $factoryValue));

        // tenta registrar uma dependencia não-compartilhada com mesmo $id
        $container->addSingleton('id', 'sobrescreve');

        // dependência é removida da fabricação e alocada como compartilhada
        $sharedValue = $this->getPropertyValue($container, 'singleton');
        $factoryValue = $this->getPropertyValue($container, 'factory');
        $this->assertTrue(in_array('sobrescreve', $sharedValue));
        $this->assertCount(0, $factoryValue);
    }

    /** @test */
    public function setIdAndValue(): void
    {
        $container = new Container();
        $container->addFactory('id', 'parangarikotirimirruaro');
        $this->assertEquals('parangarikotirimirruaro', $container->get('id'));

        $container = new Container();
        $container->addFactory('id', fn() => 'kkk');
        $this->assertEquals('kkk', $container->get('id'));

        $container = new Container();
        $container->addFactory('id', 'ArrayObject');
        $this->assertInstanceOf(ArrayObject::class, $container->get('id'));
    }

    /** @test */
    public function setIdOnly(): void
    {
        $container = new Container();
        $container->addFactory('id');
        $this->assertEquals('id', $container->get('id'));

        $container = new Container();
        $container->addFactory(ArrayObject::class);
        $this->assertEquals(new ArrayObject(), $container->get(ArrayObject::class));
    }

    /** @test */
    public function getShared(): void
    {
        $container = new Container();
        $container->addSingleton('id', fn() => microtime());

        // a mesma instância é chamada
        $this->assertEquals($container->get('id'), $container->get('id'));
    }

    /** @test */
    public function getFactory(): void
    {
        $container = new Container();
        $container->addFactory('id', fn() => microtime());
        $container->addFactory('args', fn($one, $two) => $one . $two);
        $container->addFactory(ArrayObject::class, fn(array $set) => new ArrayObject($set));

        // a instância é fabricada a cada chamada
        $this->assertNotEquals($container->get('id'), $container->get('id'));

        $this->assertEquals(
            'ricardo',
            $container->getWithArguments('args', ['ric', 'ardo'])
        );

        $this->assertEquals(
            new ArrayObject(['ric', 'ardo']),
            $container->getWithArguments(ArrayObject::class, [['ric', 'ardo']])
        );
    }

    /** @test */
    public function singleResolution(): void
    {
        $container = new Container();
        $container->addSingleton(ArrayObject::class);

        /** @var ArrayObject<int, string> */
        $retrieveOne = $container->get(ArrayObject::class);
        $this->assertEquals([], $retrieveOne->getArrayCopy());

        // muda o estado da dependencia
        $retrieveOne->append('abc');

        /** @var ArrayObject<int, string> */
        $retrieveTwo = $container->get(ArrayObject::class);
        $this->assertEquals([ 'abc' ], $retrieveTwo->getArrayCopy());
    }

    /** @test */
    public function getNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find dependency definition for not-exists');

        $container = new Container();
        $container->get('not-exists');
    }

    /** @test */
    public function resolveId(): void
    {
        $container = new Container();
        $container->addFactory('closure', fn() => microtime());

        $this->assertNotSame(
            $container->get('closure'),
            $container->get('closure')
        );

        $container = new Container();
        $container->addSingleton('closure', fn() => microtime());
    }

    /** @test */
    public function resolveWithErrorInFactory(): void
    {
        $this->expectException(ContainerException::class);

        $container = new Container();
        $container->addFactory('closure', fn() => throw new Exception());
        $container->get('closure');
    }
}
