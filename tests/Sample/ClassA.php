<?php

namespace Tests\Sample;

class ClassA implements SampleInterface
{
    public function __construct(
        private ClassB $classB
    ){
    }

    public function hoge(): string
    {
        return $this->classB->fuga();
    }

    public function getClassB(): ClassB
    {
        return $this->classB;
    }
}