<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;
use Exception;
use Iquety\Injection\Container;
use Iquety\Injection\ContainerException;
use Iquety\Injection\NotFoundException;
use Tests\TestCase;

class RegisterGetTest extends TestCase
{
    /** @test */
    public function getSingleton(): void
    {
        $container = new Container();
        $container->addSingleton('id', fn() => microtime());

        // a mesma instância é chamada todas as vezes
        $this->assertEquals($container->get('id'), $container->get('id'));
    }

    /** @test */
    public function getFactory(): void
    {
        $container = new Container();
        $container->addFactory('id', fn() => microtime());

        // a instância é fabricada a cada chamada
        $this->assertNotEquals($container->get('id'), $container->get('id'));
    }

    /** @test */
    public function sigletonReference(): void
    {
        $container = new Container();
        $container->addSingleton(ArrayObject::class);

        /** @var ArrayObject<int, string> */
        $retrieveOne = $container->get(ArrayObject::class);
        $this->assertEquals([], $retrieveOne->getArrayCopy());

        // muda o estado do singleton obtido
        $retrieveOne->append('abc');

        // obtém novamente o singleton via container
        /** @var ArrayObject<int, string> */
        $retrieveTwo = $container->get(ArrayObject::class);

        // o estado foi alterado
        $this->assertEquals([ 'abc' ], $retrieveTwo->getArrayCopy());
    }

    /** @test */
    public function notFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find dependency definition for not-exists');

        $container = new Container();
        $container->get('not-exists');
    }

    /** @test */
    public function withErrorInFactory(): void
    {
        $this->expectException(ContainerException::class);

        $container = new Container();
        $container->addFactory('closure', fn() => throw new Exception());
        $container->get('closure');
    }
}
