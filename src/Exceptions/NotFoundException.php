<?php

namespace PHPOdawara2025\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use LogicException;

class NotFoundException extends LogicException implements NotFoundExceptionInterface
{
}