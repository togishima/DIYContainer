<?php

namespace Test\UnitTests;

use PHPerKaigi2023\DIYContainer;
use PHPerKaigi2023\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\Sample\ClassA;
use Tests\Sample\ClassB;
use Tests\Sample\SampleInterface;

class ContainerTest extends TestCase
{
    private ContainerInterface $container;

    public function setUp(): void
    {
        $this->container = new DIYContainer([
            'foo' => fn() => 'bar',
            ClassB::class => fn() => new ClassB(),
            SampleInterface::class => fn() => new ClassA(new ClassB()),
        ]);
    }

    /**
     * @test 
     */
    public function 与えられた文字列に対して定義があればその通りに返す()
    {
        $bar = $this->container->get('foo');
        $this->assertSame('bar', $bar);
        $classB = $this->container->get(ClassB::class);
        $this->assertTrue($classB instanceof ClassB);
    }

    /**
     * @test
     */
    public function 解決できない文字列には例外が投げられる()
    {
        $this->expectException(NotFoundException::class);
        $this->container->get('None');
    }

    /**
     * @test
     */
    public function インターフェースとのマッピングができる()
    {
        $sample = $this->container->get(SampleInterface::class);
        $this->assertTrue($sample instanceof ClassA);
    }

    // autowireing

    /**
     * @test
     */
    public function 与えられた文字列がクラス名だった場合オートロードできれば解決する()
    {
        $sample = $this->container->get(ClassB::class);
        $this->assertTrue($sample instanceof ClassB);
    }

    /**
     * @test
     */
    public function 入れ子になっているクラスを解決できる()
    {
        $sample = $this->container->get(ClassA::class);
        // ClassAの内部でClassBのfuga()が呼ばれるはず
        $this->assertSame('piyo', $sample->hoge());
    }
}