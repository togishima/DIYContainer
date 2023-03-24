<?php

namespace PHPerKaigi2023\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use LogicException;

class NotFoundException extends LogicException implements NotFoundExceptionInterface
{
}