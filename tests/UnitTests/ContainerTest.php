<?php

namespace Test\UnitTests;

use PHPOdawara2025\DIYContainer;
use PHPOdawara2025\Exceptions\NotFoundException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Sample\ClassA;
use Tests\Sample\ClassB;
use Tests\Sample\SampleInterface;

class ContainerTest extends TestCase
{
    #[Test]
    public function has_登録されていればtrueを返す(): void
    {
        $container = new DIYContainer();
        $container->bind('foo', 'bar');

        $this->assertTrue($container->has('foo'));
    }

    #[Test]
    public function has_登録されていなければfalseを返す(): void
    {
        $container = new DIYContainer();

        $this->assertFalse($container->has('foo'));
    }

    #[Test]
    public function get_解決できないものは例外が投げられる(): void
    {
        $container = new DIYContainer();

        $this->expectException(NotFoundException::class);

        $container->get('No Entry');
    }

    #[Test]
    public function get_クロージャーで値を返すことができる(): void
    {
        $container = new DIYContainer();
        $container->bind('integer', fn() => 100 * 10);

        $concrete = $container->get('integer');

        $this->assertSame(1000, $concrete);
    }

    #[Test]
    public function get_クラス名からインスタンスを返却できる(): void
    {
        $container = new DIYContainer();
        $container->bind('b', ClassB::class);

        $concrete = $container->get('b');

        $this->assertInstanceOf(ClassB::class, $concrete);
    }

    #[Test]
    public function get_入れ子の依存を解決できる()
    {
        $container = new DIYContainer();
        $container->bind(SampleInterface::class, ClassA::class);

        $concrete = $container->get(SampleInterface::class);

        $this->assertInstanceOf(ClassB::class, $concrete->getClassB());
    }

    #[Test]
    public function get_取得されるインスタンスはシングルトン(): void
    {
        $container = new DIYContainer();
        $container->bind('a', ClassA::class);

        $concrete1 = $container->get('a');
        $concrete2 = $container->get('a');

        $this->assertSame($concrete1, $concrete2);
    }

    #[Test]
    #[DataProvider('valueProvider')]
    public function get_格納した値を返すことができる(string $id, mixed $value): void
    {
        $container = new DIYContainer();
        $container->bind($id, $value);

        $concrete = $container->get($id);

        $this->assertSame($value, $concrete);
    }

    public static function valueProvider(): array
    {
        return [
            '文字列' => ['id' => 'hoge', 'value' => 'fuga'],
            '整数' => ['id' => 'integer', 'value' => 100],
            '浮動小数' => ['id' => 'float', 'value' => 0.1],
            '真偽値' => ['id' => 'bool', 'value' => false],
            'null' => ['id' => 'null', 'value' => null],
            '配列' => ['id' => 'array', 'value' => [1, 2, 3]],
        ];
    }
}
