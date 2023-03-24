<?php

namespace PHPerKaigi2023;

use PHPerKaigi2023\Exceptions\ContainerException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;

class DIYContainer implements ContainerInterface
{
    /**
     * definitions of DI mappings
     *
     * @var array<Callable|class-string>
     */
    private $definitions = [];

    /**
     * instantiated objects
     *
     * @var array<string,mixed>
     */
    private $instances = [];

    public function __construct()
    {
        $this->definitions = include __DIR__ . '/../config/definitions.php';
    }

    public function get(string $id)
    {
        // インスタンス化されたオブジェクトがなければ作成して返す
        if (!$this->has($id)) {
            $this->instances[$id] = $this->make($id);
        }
        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instances);
    }

    /**
     * 新規にインスタンスを生成する
     *
     * @param string $id
     * @return mixed
     */
    private function make(string $id): mixed
    {
        /**
         * 一旦Callableかクラス名以外は想定しないものとする
         */
        if (array_key_exists($id, $this->definitions) && is_callable($this->definitions[$id])) {
            $callable = $this->definitions[$id];
            return $callable($this);
        }
        try {
            $dependencies = $this->resolveDependencies($id);
            return new $id(...$dependencies);
        } catch (\Throwable $th) {
            throw new ContainerException(
                message: 'エラー：'.$id.'のインスタンス化に失敗しました',
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
        foreach($constructor->getParameters() as $param) {
            /** @see https://www.php.net/manual/ja/reflectionparameter.gettype.php */
            if ($param->getType() instanceof ReflectionNamedType) {
                $dependencies[] = $this->get($param->getType()->getName());
            }
        }
        return $dependencies;
    }
}