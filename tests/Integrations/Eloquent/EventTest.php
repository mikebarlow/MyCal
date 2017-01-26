<?php

namespace Snscripts\MyCal\Tests\Integrations\Eloquent;

use Snscripts\MyCal\Integrations\Eloquent\Event as EventIntegration;
use Snscripts\MyCal\Calendar\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->EventInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface');

        $this->eventObj = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone(
                'Europe/London'
            )
        );
    }

    public function testGetExtraDataExtractsData()
    {
        $this->eventObj->name = 'Test Event';
        $this->eventObj->calendar_id = 1;
        $this->eventObj->unixStart = 1484851680;
        $this->eventObj->unixEnd = 1484938080;
        $this->eventObj->test = [
            'foo' => 'bar',
            'foobar' => 'barfoo'
        ];

        $EventIntegration = new EventIntegration;

        $this->assertSame(
            [
                'id' => null,
                'name' => 'Test Event',
                'start_date' => 1484851680,
                'end_date' => 1484938080,
                'calendar_id' => 1,
                'extras' => [
                    'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
                ]
            ],
            $EventIntegration->getEventData(
                $this->eventObj
            )
        );

        $this->eventObj->id = 12;
        $this->assertSame(
            [
                'id' => 12,
                'name' => 'Test Event',
                'start_date' => 1484851680,
                'end_date' => 1484938080,
                'calendar_id' => 1,
                'extras' => [
                    'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
                ]
            ],
            $EventIntegration->getEventData(
                $this->eventObj
            )
        );
    }

    public function testSetupModelCreatesBlankEloquentModel()
    {
        $data = [
            'id' => null,
            'name' => 'Test Event',
            'start_date' => 1484851680,
            'end_date' => 1484938080,
            'calendar_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
            ]
        ];

        $EventIntegration = new EventIntegration;
        $Model = $EventIntegration->setupModel(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\Event,
            $data
        );

        $this->assertInstanceOf(
            '\Snscripts\MyCal\Integrations\Eloquent\Models\Event',
            $Model
        );

        $this->assertSame(
            'Test Event',
            $Model->name
        );
    }

    public function testSetupModelReturnsEventModel()
    {
        $data = [
            'id' => 1,
            'name' => 'Test Event',
            'start_date' => 1484851680,
            'end_date' => 1484938080,
            'calendar_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
            ]
        ];

        $Event = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Event')
            ->setMethods(null)
            ->getMock();
        $Event->setRawAttributes($data);

        $EventModel = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Event')
            ->setMethods(['find'])
            ->getMock();

        $EventModel->expects($this->once())
             ->method('find')
             ->willReturn($Event);

         $EventIntegration = new EventIntegration;
         $Model = $EventIntegration->setupModel(
             $EventModel,
             $data
         );

         $this->assertInstanceOf(
             '\Snscripts\MyCal\Integrations\Eloquent\Models\Event',
             $Model
         );

         $this->assertSame(
             'Test Event',
             $Model->name
         );

         $this->assertSame(
             1,
             $Model->id
         );
    }





}
