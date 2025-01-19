<?php
namespace PHPShots\Common\TestClasses;

class SingletonService
{
    public function greet(): string
    {
        return 'Singleton Service!';
    }
}