<?php

namespace Tests\Sample;

class ClassC implements SampleInterface
{
    public function hoge(): string
    {
        return 'this is ClassC';
    }
}