<?php

namespace PHPerKaigi2023;

use PHPerKaigi2023\Exceptions\ContainerException;
use PHPerKaigi2023\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;

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
        try {
            $this->resolve($id);
            $resolvable = true;
        } catch (\Throwable $th) {
            $resolvable = false;
        }
        
        return $isResolvedAlready || $definitionExists || $resolvable;
    }

    private function resolve(string $id)
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }
        if (array_key_exists($id, $this->definitions) && is_callable($this->definitions[$id])) {
            $callable = $this->definitions[$id];
            return $callable();
        } 
        // has()でバリデートされているのでここに来るのはclass-string
        try {
            $dependencies = $this->resolveDependencies($id);
            $this->instances[$id] = new $id(...$dependencies);
            return $this->instances[$id];
        } catch (\Throwable $th) {
            throw new ContainerException(
                message: 'Failed resolving ' . $id,
                previous: $th
            );
        }
    }

    /**
     * 対象クラスのコンストラクタで定義されているクラスをインスタンス化
     *
     * @param string $className
     * @return array
     */
    private function resolveDependencies(string $className): array
    {
        /** @see https://www.php.net/manual/ja/reflectionclass.getconstructor.php */
        $constructor = (new ReflectionClass($className))->getConstructor();
        $dependencies = [];
        /** @see https://www.php.net/manual/ja/reflectionfunctionabstract.getparameters.php */
        foreach ($constructor->getParameters() as $param) {
            /** @see https://www.php.net/manual/ja/reflectionparameter.gettype.php */
            if ($param->getType() instanceof ReflectionNamedType) {
                $dependencies[] = $this->get($param->getType()->getName());
            }
        }
        return $dependencies;
    }
}
