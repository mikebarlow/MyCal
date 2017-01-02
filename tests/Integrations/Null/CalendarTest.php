<?php

namespace Snscripts\MyCal\Tests\Integrations\Null;

use Snscripts\Result\Result;
use Snscripts\MyCal\Calendar\Calendar;
use Snscripts\MyCal\Integrations\Null\Calendar as CalendarIntegration;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function testSaveReturnsFailedObject()
    {
        $CalendarInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $DateFactoryMock = $this->createMock('\Snscripts\MyCal\DateFactory');
        $OptionsMock = $this->createMock('\Snscripts\MyCal\Calendar\Options');
        $OptionsMock->method('toArray')
            ->willReturn([]);

        $calendarObj = new Calendar(
            $CalendarInterfaceMock,
            $DateFactoryMock,
            $OptionsMock
        );

        $CalendarIntegration = new CalendarIntegration;
        $Result = $CalendarIntegration->save($calendarObj);

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
        $CalendarIntegration = new CalendarIntegration;
        $Result = $CalendarIntegration->load(1);

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
