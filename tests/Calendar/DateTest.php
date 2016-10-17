<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Calendar\Date;
use DateTimeZone;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Date',
            new Date(
                time(),
                new DateTimeZone('Europe/London'),
                Date::MONDAY
            )
        );
    }
}
