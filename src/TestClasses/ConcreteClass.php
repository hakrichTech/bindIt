<?php
namespace PHPShots\Common\TestClasses;


// Concrete class to test singleton and contextual bindings
class ConcreteClass
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