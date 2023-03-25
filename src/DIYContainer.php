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
        /**
         * 文字列に対応した「何か」を返す
         * 返せるものがなければNotFoundExceptionを投げる
         */
        if (!$this->has($id)) {
            throw new NotFoundException('Entry for ' . $id . ' not found');
        }
        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        /**
         * 渡された文字列に対して返せるものがあるならtrue
         * このメソッドがfalseを還したらget()はNotFountExceptionを返す
         */
        $isResolvedAlready = array_key_exists($id, $this->instances);
        $definitionExists = array_key_exists($id, $this->definitions);
        
        return $isResolvedAlready || $definitionExists;
    }

    private function resolve(string $id)
    {
        
    }
}