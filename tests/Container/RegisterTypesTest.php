<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;
use Iquety\Injection\Container;
use Tests\TestCase;

class RegisterTypesTest extends TestCase
{
    /** @test */
    public function registerFactory(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('myid'));

        $container->addFactory('myid', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('myid'));
    }

    public function registerSingleton(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('myid'));

        $container->addSingleton('myid', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('myid'));
    }

    /** @test */
    public function registerArbiraryValue(): void
    {
        $container = new Container();

        $container->addFactory('mystring', 'parangarikotirimirruaro');
        $this->assertEquals('parangarikotirimirruaro', $container->get('mystring'));

        $container->addFactory('myint', 11);
        $this->assertEquals(11, $container->get('myint'));

        $container->addFactory('myfloat', 11.22);
        $this->assertEquals(11.22, $container->get('myfloat'));

        $array = ['one', 'two', 'three'];
        $container->addFactory('myarray', $array);
        $this->assertEquals($array, $container->get('myarray'));

        $object = (object) ['one', 'two', 'three'];
        $container->addFactory('myobject', $object);
        $this->assertEquals($object, $container->get('myobject'));
    }

    /** @test */
    public function registerClosure(): void
    {
        $container = new Container();
        $container->addFactory('id', fn() => 'kkk');
        $this->assertEquals('kkk', $container->get('id'));
    }

    /** @test */
    public function registerObjectContract(): void
    {
        $container = new Container();
        $container->addFactory('id', ArrayObject::class);
        $this->assertInstanceOf(ArrayObject::class, $container->get('id'));
    }

    /** @test */
    public function registerFunction(): void
    {
        $container = new Container();
        $container->addFactory('id', 'microtime');

        $this->assertNotFalse(preg_match('#[0-9]{1}.[0-9]* [0-9]*#', $container->get('id')));
    }

    /** @test */
    public function registerOnlyContract(): void
    {
        $container = new Container();

        $container->addFactory(ArrayObject::class);
        $this->assertEquals(new ArrayObject(), $container->get(ArrayObject::class));
    }
}
