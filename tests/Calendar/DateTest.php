<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Calendar\Date;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Date',
            new Date
        );
    }
}
