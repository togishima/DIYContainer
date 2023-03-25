<?php

namespace PHPerKaigi2023;

use Psr\Container\ContainerInterface;

/**
 * PSR-11実装の簡易自作コンテナ
 */
class DIYContainer implements ContainerInterface
{
    /**
     * @var array<string,mixed> $instances 解決されたものを一時的に保存しておく配列
     */
    private array $instances = [];

    /**
     * @param array<string,Callable|class-string> $definitions 定義（マッピング）
     */
    public function __construct(
        private array $definitions
    ){
    }

    public function get(string $id)
    {
        /**
         * 文字列に対応した「何か」を返す
         * 返せるものがなければNotFoundExceptionを投げる
         */
    }

    public function has(string $id): bool
    {
        /**
         * 渡された文字列に対して返せるものがあるならtrue
         * このメソッドがfalseを還したらget()はNotFountExceptionを返す
         */
    }
}