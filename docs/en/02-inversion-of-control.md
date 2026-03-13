# Inversion of Control

[◂ Container](01-container.md) | [Documentation index](index.md) | [Evolving the library ▸](99-evolution.md)
-- | -- | --

## 1. Introduction

Inversion of control (IoC) is a program design principle where the sequence
(control) of method calls is inverted in relation to traditional programming,
that is, it is not determined directly by the programmer. This control is
delegated to a software infrastructure often called a Container or a
any other component that can take control over execution. This is a
feature very common to some frameworks.

## 2. Reversing control

### 2.1. Dependency injection

The Inversion of Control mechanism needs to receive the Container instance
to be able to identify existing values ​​and automatically inject them into
methods invoked.

To invoke a method with the injected arguments, you can use the methods
`resolve` or `resolveTo`.

```php
class MyClass
{
    public function myMethod(MyDependency $dependency, string $name): string
    {
        // $dependency will be injected from the container

        // $name will be injected by the second argument of the resolve method

        return $name . ' ' . $dependency->getValue();
    }
}

class MyDependency
{
    public function getValue(): string
    {
        return 'skywalker';
    }
}

$container = new Container();
$container->addSingleton(MyDependency::class);

$inversion = new InversionOfControl($container);

// returns the return of the myMethod method = test skywalker
$inversion->resolve('MyClass::myMethod', ['name' => 'test']);
```

The method to be resolved must be callable and can be specified in the following ways:

```php
// resolving a string
$inversion->resolve(MyClass::myMethod);

// resolving an array
$inversion->resolve([ MyClass::class, 'myMethod']);

// resolving an object
$inversion->resolve(new MyClass());

// resolving any callable
$inversion->resolve('method, class or function');
```

### 2.2. Strict Dependency Injection

Using `resolveTo` is very similar to `resolve`. The difference is that `resolveTo`
takes an additional first argument, which must contain the type of the class to be
resolved. If the class to be resolved is not compatible with the type (interface or class)
passed in the first argument, the resolution will fail by throwing an exception of type
`InvalidArgumentException`.

```php

class MyDependency implements MyInterface
{
    public function getValue(): string
    {
        return 'skywalker';
    }
}

$container = new Container();
$container->addSingleton(MyDependency::class);

$inversion = new InversionOfControl($container);

// returns the return of the myMethod method = test skywalker
$inversion->resolveTo(MyInterface::class, 'MyClass::myMethod');
```

[◂ Container](01-container.md) | [Documentation index](index.md) | [Evolving the library ▸](99-evolution.md)
-- | -- | --
