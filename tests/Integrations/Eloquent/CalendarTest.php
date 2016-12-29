<?php

namespace Snscripts\MyCal\Tests\Integrations\Eloquent;

use Snscripts\MyCal\Integrations\Eloquent\Calendar as CalendarIntegration;
use Snscripts\MyCal\Calendar\Calendar;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->DateFactoryMock = $this->getMock('\Snscripts\MyCal\DateFactory');
        $this->OptionsMock = $this->getMock('\Snscripts\MyCal\Calendar\Options');
        $this->OptionsMock->method('toArray')
            ->willReturn([]);

        $this->calendarObj = new Calendar(
            $this->CalendarInterfaceMock,
            $this->DateFactoryMock,
            $this->OptionsMock
        );
    }

    public function testGetCalendarDataExtractsCalendarData()
    {
        $this->calendarObj->name = 'Test Calendar';
        $this->calendarObj->user_id = 1;
        $this->calendarObj->test = [
            'foo' => 'bar',
            'foobar' => 'barfoo'
        ];

        $CalendarIntegration = new CalendarIntegration;

        $this->assertSame(
            [
                'id' => null,
                'name' => 'Test Calendar',
                'user_id' => 1,
                'extras' => [
                    'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
                ],
                'options' => []
            ],
            $CalendarIntegration->getCalendarData(
                $this->calendarObj
            )
        );

        $this->calendarObj->id = 12;
        $this->assertSame(
            [
                'id' => 12,
                'name' => 'Test Calendar',
                'user_id' => 1,
                'extras' => [
                    'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
                ],
                'options' => []
            ],
            $CalendarIntegration->getCalendarData(
                $this->calendarObj
            )
        );
    }

    public function testSetupModelCreatesBlankEloquentModel()
    {
        $data = [
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
            ],
            'options' => []
        ];

        $CalendarIntegration = new CalendarIntegration;
        $Model = $CalendarIntegration->setupModel(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\Calendar,
            $data
        );

        $this->assertInstanceOf(
            '\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar',
            $Model
        );

        $this->assertSame(
            'Test Calendar',
            $Model->name
        );
    }

    public function testSetupExtrasReturnsArrayOfExtras()
    {
        $data = [
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}',
                'foo' => 'bar',
            ],
            'options' => []
        ];

        $CalendarIntegration = new CalendarIntegration;

        $calendarExtras = $CalendarIntegration->setupExtras(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra,
            $data
        );

        $this->assertSame(
            2,
            count($calendarExtras)
        );
    }

    public function testSetupExtrasReturnBlankArrayWhenNoExtrasSet()
    {
        $data = [
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [],
            'options' => []
        ];

        $CalendarIntegration = new CalendarIntegration;

        $calendarExtras = $CalendarIntegration->setupExtras(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra,
            $data
        );

        $this->assertSame(
            0,
            count($calendarExtras)
        );
    }

    public function testSetupOptionsReturnsArrayOfOptions()
    {
        $data = [
            'id' => 1,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
            ],
            'options' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}',
                'foo' => 'bar',
            ]
        ];

        $CalendarIntegration = new CalendarIntegration;

        $calendarOptions = $CalendarIntegration->setupOptions(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\Option,
            $data
        );

        $this->assertSame(
            2,
            count($calendarOptions)
        );
    }

    public function testSetupOptionsReturnBlankArrayWhenNoOptionsSet()
    {
        $data = [
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [],
            'options' => []
        ];

        $CalendarIntegration = new CalendarIntegration;

        $calendarOptions = $CalendarIntegration->setupOptions(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\Option,
            $data
        );
        $this->assertSame(
            0,
            count($calendarOptions)
        );
    }

}
