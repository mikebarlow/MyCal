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
}
