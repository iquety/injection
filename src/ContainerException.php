<?php

declare(strict_types=1);

namespace Iquety\Injection;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ContainerException extends RuntimeException implements ContainerExceptionInterface {}
