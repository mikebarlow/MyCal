<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\CalendarFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;

class CalendarFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->DateFactoryMock = $this->createMock('\Snscripts\MyCal\DateFactory');
        $this->OptionsMock = $this->createMock('\Snscripts\MyCal\Calendar\Options');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\CalendarFactory',
            new CalendarFactory(
                $this->CalendarInterfaceMock,
                $this->DateFactoryMock
            )
        );
    }

    public function testNewInstanceReturnsCalendarObject()
    {
        $Factory = new CalendarFactory(
            $this->CalendarInterfaceMock,
            $this->DateFactoryMock
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Calendar',
            $Factory->load()
        );
    }

    public function testLoadReturnsCalendarObjectWhenIdSet()
    {
        // Calendar Mock object
        $Calendar = new \Snscripts\MyCal\Calendar\Calendar(
            new \Snscripts\MyCal\Integrations\Null\Calendar,
            $this->DateFactoryMock,
            $this->OptionsMock
        );
        $Calendar->setAllData([
            'id' => 1,
            'name' => 'My Calendar',
            'user_id' => 12
        ]);

        $CalendarMock = $this->getMockBuilder('\Snscripts\MyCal\Calenar\Calendar')
            ->setMethods(['load'])
            ->getMock();
        $CalendarMock->expects($this->once())
             ->method('load')
             ->willReturn($Calendar);

        $CalendarFactory = $this->getMockBuilder('\Snscripts\MyCal\CalendarFactory')
            ->setMethods(['newInstance'])
            ->setConstructorArgs([
                new \Snscripts\MyCal\Integrations\Null\Calendar,
                $this->DateFactoryMock
            ])
            ->getMock();
        $CalendarFactory->expects($this->once())
             ->method('newInstance')
             ->willReturn($CalendarMock);

        // -----

        $CalTest = $CalendarFactory->load(
            1
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Calendar',
            $CalTest
        );

        $this->assertSame(
            'My Calendar',
            $CalTest->name
        );
    }

    public function testLoadEventReturnsAnEventObject()
    {
        // Event Mock object
        $Event = new \Snscripts\MyCal\Calendar\Event(
            new \Snscripts\MyCal\Integrations\Null\Event,
            new \DateTimeZone('Europe/London')
        );
        $Event->setAllData([
            'id' => 1,
            'title' => 'Super cool event',
            'location' => 'UK'
        ]);

        $EventMock = $this->getMockBuilder('\Snscripts\MyCal\Calenar\Event')
            ->setMethods(['load'])
            ->getMock();
        $EventMock->expects($this->once())
             ->method('load')
             ->willReturn($Event);

        $EventFactory = $this->getMockBuilder('\Snscripts\MyCal\EventFactory')
            ->setMethods(['newInstance'])
            ->setConstructorArgs([
               new \Snscripts\MyCal\Integrations\Null\Event
            ])
            ->getMock();
        $EventFactory->expects($this->once())
             ->method('newInstance')
             ->willReturn($EventMock);

        $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
            new \Snscripts\MyCal\Integrations\Null\Calendar,
            new \Snscripts\MyCal\DateFactory(
                $EventFactory
            )
        );

        $EventTest = $CalendarFactory->loadEvent(1);

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Event',
            $EventTest
        );

        $this->assertSame(
            'Super cool event',
            $EventTest->title
        );
    }

    public function testLoadEventThrowsExceptionWhenNoFactorySet()
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('No Event Factory was loaded.');

        $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
            new \Snscripts\MyCal\Integrations\Null\Calendar,
            new \Snscripts\MyCal\DateFactory
        );

        $CalendarFactory->loadEvent(1);
    }
}
