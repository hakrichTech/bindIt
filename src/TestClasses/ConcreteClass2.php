<?php
namespace PHPShots\Common\TestClasses;

// Second class for testing multiple concrete bindings with the same abstract type
class ConcreteClass2
{
    protected $dependency;

    public function __construct(AbstractClass $dependency)
    {
        $this->dependency = $dependency;
    }

    public function getDependency(): AbstractClass
    {
        return $this->dependency;
    }
}