<?php

namespace Tests\Sample;

class ClassB
{
    private $property;

    public function __construct()
    {
        $this->property = 'piyo';
    }

    public function fuga(): string
    {
        return $this->property;
    }
}