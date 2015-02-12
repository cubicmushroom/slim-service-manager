<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use AspectMock\Test as test;
use Codeception\Module;
use Codeception\TestCase;

class UnitHelper extends Module
{
    function _after(TestCase $test)
    {
        test::clean();
    }
}
