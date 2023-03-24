<?php

namespace PHPerKaigi2023;

use Psr\Container\ContainerInterface;

class DIYContainer implements ContainerInterface
{
    /**
     * 解決されたものを一時的に保存しておく配列
     * 
     * @var array<string,mixed>
     */
    private $instances = [];

    /**
     * @param array<string,Callable|class-string> $definitions DIのマッピング
     */
    public function __construct(
        private array $definitions
    ){
    }

    public function get(string $id)
    {

    }

    public function has(string $id): bool
    {

    }
}