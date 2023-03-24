<?php

namespace Test\UnitTests;

use PHPerKaigi2023\DIYContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\Sample\ClassA;
use Tests\Sample\SampleInterface;

class ContainerTest extends TestCase
{
    private ContainerInterface $container;

    public function setUp(): void
    {
        $this->container = new DIYContainer();
    }

    /**
     * @test
     */
    public function 具象クラス名は定義をしなくとも自動で解決できる()
    {
        // FQCNを渡せば解決してくれる
        $sample = $this->container->get(ClassA::class);
        $this->assertTrue($sample instanceof ClassA);
    }

    /**
     * @test
     */
    // public function 一度インスタンス化したオブジェクトはキャッシュされる()
    // {
    //     // 最初はキャッシュがない状態
    //     $this->assertFalse($this->container->has(SampleSampleInterface::class));
    //     // 一回取り出す
    //     $sample = $this->container->get(SampleInterface::class);
    //     // オブジェクトが生成されたのでキャッシュされているはず...
    //     $this->assertTrue($this->container->has(SampleInterface::class));
    // }

    /**
     * @test
     */
    // public function 定義をすればインターフェースから具象クラスを解決できる()
    // {
    //     $sample = $this->container->get(SampleInterface::class);
    //     // SampleInterfaceとClassAがマッピングされる
    //     $this->assertTrue($sample instanceof ClassA);
    //     // ClassAの内部でClassBのfuga()が呼ばれる
    //     $this->assertSame('piyo', $sample->hoge());
    // }
}