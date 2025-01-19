<?php
namespace PHPShots\Common\TestClasses;

// Another concrete class for multiple concrete bindings test
class ConcreteClass1
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