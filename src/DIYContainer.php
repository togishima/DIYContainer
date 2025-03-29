<?php

namespace PHPOdawara2025;

use PHPOdawara2025\Exceptions\ContainerException;
use PHPOdawara2025\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;
use Throwable;

class DIYContainer implements ContainerInterface
{
    /** @var array<string,mixed> $resolved 生成済のインスタンス */
    private array $resolved = [];

    /** @var array<string|class-string,mixed> $definitions 解決方法の定義 */
    private array $definitions = [];

    public function bind(string $id, mixed $definition): void
    {
        $this->definitions[$id] = $definition;
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException('Entry for ' . $id . ' not found');
        }
        return array_key_exists($id, $this->resolved)
            ? $this->resolved[$id]
            : $this->resolve($id);
    }

    public function has(string $id): bool
    {
        if (array_key_exists($id, $this->definitions)) {
            return true;
        }
        if (array_key_exists($id, $this->resolved)) {
            return true;
        }
        try {
            $this->resolve($id);
            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    private function resolve(string $id): mixed
    {
        if (!array_key_exists($id, $this->definitions)) {
            return class_exists($id) 
                ? $this->build($id, $id)
                : throw new ContainerException('Entry ' . $id . ' Not Found');
        }
        if (is_callable($this->definitions[$id])) {
            return $this->definitions[$id]();
        }
        if (is_string($this->definitions[$id]) && class_exists($this->definitions[$id])) {
            return $this->build($this->definitions[$id], $id);
        }
        return $this->definitions[$id];
    }

    /** @param class-string $className */
    private function build(string $className, string $id): mixed
    {
        $this->resolved[$id] = new $className(...$this->resolveDependencies($className));
        return $this->resolved[$id];
    }

    /**
     * @param class-string $className
     * @return array<mixed>
     */
    private function resolveDependencies(string $className): array
    {
        /** @see https://www.php.net/manual/ja/reflectionclass.getconstructor.php */
        $constructor = (new ReflectionClass($className))->getConstructor();
        if ($constructor === null) {
            return throw new ContainerException('Class ' . $className . ' has no constructor');
        }
        $dependencies = [];
        /** @see https://www.php.net/manual/ja/reflectionfunctionabstract.getparameters.php */
        foreach ($constructor->getParameters() as $param) {
            /** @see https://www.php.net/manual/ja/reflectionparameter.gettype.php */
            if ($param->getType() instanceof ReflectionNamedType) {
                /** @var class-string $className */
                $className = $param->getType()->getName();
                $dependencies[] = $this->get($className);
            }
        }
        return $dependencies;
    }
}
