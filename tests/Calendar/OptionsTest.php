<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Calendar\Options;
use Snscripts\MyCal\Calendar\Date;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultOptionsReturnsCorrectArray()
    {
        $Options = new \Snscripts\MyCal\Calendar\Options([]);

        $this->assertSame(
            [
                'weekStartsOn' => 1,
                'defaultTimezone' => 'Europe/London'
            ],
            $Options->defaultOptions()
        );
    }

    public function testConstructorSetsDataAndGets()
    {
        $Options = new \Snscripts\MyCal\Calendar\Options([]);

        $this->assertSame(
            1,
            $Options->weekStartsOn
        );
        $this->assertSame(
            'Europe/London',
            $Options->defaultTimezone
        );
    }

    public function testConstructorMergesDataAndGets()
    {
        $Options = new \Snscripts\MyCal\Calendar\Options([
            'weekStartsOn' => Date::SUNDAY,
            'defaultTimezone' => 'America/New_York'
        ]);

        $this->assertSame(
            0,
            $Options->weekStartsOn
        );
        $this->assertSame(
            'America/New_York',
            $Options->defaultTimezone
        );
    }
}
