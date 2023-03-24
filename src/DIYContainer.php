<?php

namespace PHPerKaigi2023;

use Psr\Container\ContainerInterface;

/**
 * PSR-11実装の簡易自作コンテナ
 */
class DIYContainer implements ContainerInterface
{
    /**
     * @param array<string,Callable|class-string> $definitions 定義（マッピング）
     * @param array<string,mixed> $instances 解決されたものを一時的に保存しておく配列
     */
    public function __construct(
        private array $definitions,
        private array $instances = []
    ){
    }

    public function get(string $id)
    {
    }

    public function has(string $id): bool
    {
    }
}