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
        try {
            return $this->make($id);
        } catch (Error|ReflectionException $e) {
            throw new NotFoundException(
                message: 'Entry Not Found',
                previous: $e
            );
        } catch(Throwable $th) {
            throw new ContainerException(
                message: 'Failed resolving ' . $id,
                previous: $th
            );
        }
    }

    private function make(string $id)
    {
        if (!array_key_exists($id, $this->instances)) {
            $dependencies = $this->resolveDependencies($id);
            $this->instances[$id] = new $id(...$dependencies);
        }
        return $this->instances[$id];
    }

    /**
     * 
     *
     * @param class-string $id
     * @return array<mixed>
     */
    private function resolveDependencies(string $id)
    {
        /** @see https://www.php.net/manual/ja/reflectionclass.getconstructor.php */
        $constructor = (new ReflectionClass($id))->getConstructor();
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

    public function has(string $id): bool
    {
        if (array_key_exists($id, $this->definitions) || array_key_exists($id, $this->instances)) {
            return true;
        }
        return false;
    }
}