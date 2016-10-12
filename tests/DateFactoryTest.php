<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\DateFactory;

class DateFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\DateFactory',
            new DateFactory
        );
    }

    public function testNewInstanceReturnsDateObject()
    {
        $Factory = new DateFactory;

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Date',
            $Factory->newInstance()
        );
    }
}
