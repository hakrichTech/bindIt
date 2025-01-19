<?php
namespace PHPShots\Common\TestClasses;

// Class that has a dependency on AbstractClass
class ClassWithDependency
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