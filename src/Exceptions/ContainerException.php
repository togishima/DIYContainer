<?php

namespace PHPOdawara2025\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use LogicException;

class ContainerException extends LogicException implements NotFoundExceptionInterface
{
}
