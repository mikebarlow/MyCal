<?php
namespace Snscripts\MyCal\Tests\Integrations;

use Snscripts\MyCal\CalendarFactory;
use Snscripts\MyCal\Integrations\BaseIntegration;

class BaseIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractIdReturnsId()
    {
        $Factory = new CalendarFactory(
            $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->createMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();
        $Calendar->id = 22;

        $BaseIntegration = new BaseIntegration;

        $this->assertSame(
            22,
            $BaseIntegration->extractVar($Calendar, 'id')
        );
    }

    public function testExtractIdReturnsNullWhenNoIdSet()
    {
        $Factory = new CalendarFactory(
            $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->createMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();

        $BaseIntegration = new BaseIntegration;

        $this->assertNull(
            $BaseIntegration->extractVar($Calendar, 'id')
        );
    }

    public function testExtractNameReturnsName()
    {
        $Factory = new CalendarFactory(
            $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->createMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();
        $Calendar->name = 'MyCal Tests';

        $BaseIntegration = new BaseIntegration;

        $this->assertSame(
            'MyCal Tests',
            $BaseIntegration->extractVar(
                $Calendar,
                'name',
                function ($Object) {
                    throw new \DomainException('No name set on ' . get_class($Object));
                }
            )
        );

        $Calendar->name = 'New Name';
        $this->assertSame(
            'New Name',
            $BaseIntegration->extractVar(
                $Calendar,
                'name',
                function ($Object) {
                    throw new \DomainException('No name set on ' . get_class($Object));
                }
            )
        );
    }

    public function testExtractNameThrowsExceptionWhenNoNameSet()
    {
        $this->expectException(\DomainException::class);

        $Factory = new CalendarFactory(
            $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->createMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();

        $BaseIntegration = new BaseIntegration;
        $BaseIntegration->extractVar(
            $Calendar,
            'name',
            function ($Object) {
                throw new \DomainException('No name set on ' . get_class($Object));
            }
        );
    }

    public function testExtractDataReturnsArrayOfData()
    {
        $Factory = new CalendarFactory(
            $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->createMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();
        $Calendar->name = 'MyCal Tests';
        $Calendar->author = 'Mike Barlow';
        $Calendar->array_test = [
            'foo', 'bar', 'foobar'
        ];

        $BaseIntegration = new BaseIntegration;

        $this->assertSame(
            [
                'author' => 'Mike Barlow',
                'array_test' => 'a:3:{i:0;s:3:"foo";i:1;s:3:"bar";i:2;s:6:"foobar";}'
            ],
            $BaseIntegration->extractData(
                $Calendar,
                ['id', 'name', 'user_id']
            )
        );
    }

    public function testExtractOptionsReturnArrayOfCalendarOptions()
    {
        $Factory = new CalendarFactory(
            $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->createMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();

        $BaseIntegration = new BaseIntegration;

        $this->assertSame(
            [
                'weekStartsOn' => 1,
                'defaultTimezone' => 'Europe/London',
                'days' => 'a:7:{i:0;s:3:"Sun";i:1;s:3:"Mon";i:2;s:3:"Tue";i:3;s:3:"Wed";i:4;s:3:"Thu";i:5;s:3:"Fri";i:6;s:3:"Sat";}'
            ],
            $BaseIntegration->extractOptions($Calendar)
        );
    }

    public function testUnserializeDataReturnsArrayOfData()
    {
        $BaseIntegration = new BaseIntegration;

        $this->assertSame(
            [
                'author' => 'Mike Barlow',
                'array_test' => [
                    'foo',
                    'bar',
                    'foobar'
                ]
            ],
            $BaseIntegration->unserializeData([
                'author' => 'Mike Barlow',
                'array_test' => 'a:3:{i:0;s:3:"foo";i:1;s:3:"bar";i:2;s:6:"foobar";}'
            ])
        );
    }
}
