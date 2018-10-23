<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\EventFactory;
use Snscripts\MyCal\Interfaces\EventInterface;

class EventFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->EventInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface');
        $this->OptionsMock = $this->createMock('\Snscripts\MyCal\Calendar\Options');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\EventFactory',
            new EventFactory(
                $this->EventInterfaceMock
            )
        );
    }

    public function testLoadReturnsEventObject()
    {
        $Factory = new EventFactory(
            $this->EventInterfaceMock
        );

        $Factory->setOptions($this->OptionsMock);

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Event',
            $Factory->load(
                new \DateTimeZone('Europe/London')
            )
        );
    }

    public function testLoadReturnsEventObjectWhenIdSet()
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

        // -----

        $EventTest = $EventFactory->load(
            new \DateTimeZone('Europe/London'),
            1
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Event',
            $EventTest
        );

        $this->assertSame(
            'Super cool event',
            $EventTest->title
        );
    }
}
