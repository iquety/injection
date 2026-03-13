# Inversão de Controle

[◂ Container](01-container.md) | [Índice da documentação](indice.md) | [Evoluindo a biblioteca ▸](99-evoluindo.md)
-- | -- | --

## 1. Introdução

Inversão de controle (IoC) é um princípio de design de programas onde a sequência
(controle) de chamadas dos métodos é invertida em relação à programação tradicional,
ou seja, ela não é determinada diretamente pelo programador. Este controle é delegado
a uma infraestrutura de software muitas vezes chamada de Container ou a qualquer
outro componente que possa tomar controle sobre a execução. Esta é uma característica
muito comum a alguns frameworks.

## 2. Invertendo o controle

### 2.1. Injeção de dependências

O mecanismo de Inversão de Controle precisa receber a instância do Container
para poder identificar os valores existentes e injetá-los automaticamente nos
métodos invocados.

Para invocar um método com os argumentos injetados, pode-se usar os métodos
`resolve` ou `resolveTo`.

```php
class MinhaClasse
{
    public function meuMetodo(MinhaDependencia $dependencia, string $name): string
    {
        // $dependencia será injetada do container

        // $name será injetado pelo segundo argumento do método resolve

        return $name . ' ' . $dependencia->obterValor();
    }
}

class MinhaDependencia
{
    public function obterValor(): string
    {
        return 'skywalker';
    }
}

$container = new Container();
$container->addSingleton(MinhaDependencia::class);

$inversion = new InversionOfControl($container);

// devolve o retorno do método meuMetodo = teste skywalker
$inversion->resolve('MinhaClasse::meuMetodo', ['name' => 'teste']);
```

O método a ser resolvido deve ser chamável e pode ser especificado das seguintes
formas:

```php
// resolvendo uma string
$inversion->resolve(MinhaClasse::meuMetodo);

// resolvendo um array
$inversion->resolve([ MinhaClasse::class, 'meuMetodo']);

// resolvendo um objeto
$inversion->resolve(new MinhaClasse());

// resolvendo qualquer chamável
$inversion->resolve('método, classe ou função');
```

### 2.2. Injeção restrita de dependências

O uso de `resolveTo` é muito parecido com o `resolve`. A diferença é que `resolveTo`
recebe um primeiro argumento adicional, que deve conter o tipo da classe a ser
resolvida. Caso a classe a ser resolvida não seja compatível com o tipo (interface ou classe)
passado no primeiro argumento, a resolução irá falhar lançando uma exceção do tipo
`InvalidArgumentException`.

```php

class MinhaDependencia implements MinhaInterface
{
    public function obterValor(): string
    {
        return 'skywalker';
    }
}

$container = new Container();
$container->addSingleton(MinhaDependencia::class);

$inversion = new InversionOfControl($container);

// devolve o retorno do método meuMetodo = teste skywalker
$inversion->resolveTo(MinhaInterface::class, 'MinhaClasse::meuMetodo');
```

[◂ Container](01-container.md) | [Índice da documentação](indice.md) | [Evoluindo a biblioteca ▸](99-evoluindo.md)
-- | -- | --
