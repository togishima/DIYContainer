<?php

namespace PHPerKaigi2023;

use Error;
use PHPerKaigi2023\Exceptions\ContainerException;
use PHPerKaigi2023\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use Throwable;

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
        // コールバックの場合は実行
        if (array_key_exists($id, $this->definitions) && is_callable($this->definitions[$id])) {
            $callable = $this->definitions[$id];
            return $callable();
        }
        // クラス名なら生成を試みる
        return $this->make($id);
    }

    private function make(string $id)
    {
        if (!array_key_exists($id, $this->instances)) {
            $this->instances[$id] = new $id();
        }
        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
    }
}