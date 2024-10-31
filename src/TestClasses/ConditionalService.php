<?php
namespace PHPShots\Common\TestClasses;

use PHPShots\Common\Bind;


class ConditionalService
{
    public function message(): string
    {
        return 'Should be bound!';
    }

    public function show($message = "Default content!")
    {
        print_r(func_get_args());
        echo "Showing: " . $message;
    }
}