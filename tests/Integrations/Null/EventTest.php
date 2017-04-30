<?php

namespace Snscripts\MyCal\Tests\Integrations\Null;

use Snscripts\Result\Result;
use Snscripts\MyCal\Calendar\Event;
use Snscripts\MyCal\Integrations\Null\Event as EventIntegration;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testSaveReturnsFailedObject()
    {
        $EventInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface');

        $eventObj = new Event(
            $EventInterfaceMock,
            new \DateTimeZone('UTC')
        );

        $EventIntegration = new EventIntegration;
        $Result = $EventIntegration->save($eventObj);

        $this->assertTrue(
            $Result->isFail()
        );

        $this->assertSame(
            'error',
            $Result->getCode()
        );

        $this->assertSame(
            'Null integration used, no database interactions available.',
            $Result->getMessage()
        );
    }

    public function testLoadReturnsFailedObject()
    {
        $EventIntegration = new EventIntegration;
        $Result = $EventIntegration->load(1);

        $this->assertTrue(
            $Result->isFail()
        );

        $this->assertSame(
            'not_found',
            $Result->getCode()
        );

        $this->assertSame(
            'Null integration used, no database interactions available.',
            $Result->getMessage()
        );
    }
}
