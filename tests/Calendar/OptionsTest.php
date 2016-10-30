<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Calendar\Options;
use Snscripts\MyCal\Calendar\Date;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Options',
            new Options
        );
    }

    public function testDefaultOptionsReturnsCorrectArray()
    {
        $Options = new \Snscripts\MyCal\Calendar\Options;

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
        $Options = new \Snscripts\MyCal\Calendar\Options;
        $Options->mergeVars(
            $Options->defaultOptions(),
            []
        );

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
        $Options = new \Snscripts\MyCal\Calendar\Options;
        $Options->mergeVars(
            $Options->defaultOptions(),
            [
                'weekStartsOn' => Date::SUNDAY,
                'defaultTimezone' => 'America/New_York'
            ]
        );

        $this->assertSame(
            0,
            $Options->weekStartsOn
        );
        $this->assertSame(
            'America/New_York',
            $Options->defaultTimezone
        );
    }

    public function testStaticSetReturnsObject()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Options',
            Options::set()
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Options',
            Options::set([
                'weekStartsOn' => Date::SUNDAY
            ])
        );
    }
}
