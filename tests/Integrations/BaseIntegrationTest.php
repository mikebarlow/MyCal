<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\CalendarFactory;
use Snscripts\MyCal\Integrations\BaseIntegration;

class BaseIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractNameReturnsName()
    {
        $Factory = new CalendarFactory(
            $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->getMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();
        $Calendar->name = 'MyCal Tests';

        $BaseIntegration = new BaseIntegration;

        $this->assertSame(
            'MyCal Tests',
            $BaseIntegration->extractName($Calendar)
        );

        $Calendar->name = 'New Name';
        $this->assertSame(
            'New Name',
            $BaseIntegration->extractName($Calendar)
        );
    }

    public function testExtractNameThrowsExceptionWhenNoNameSet()
    {
        $this->setExpectedException('DomainException');

        $Factory = new CalendarFactory(
            $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->getMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();

        $BaseIntegration = new BaseIntegration;
        $BaseIntegration->extractName($Calendar);
    }

    public function testExtractDataReturnsArrayOfData()
    {
        $Factory = new CalendarFactory(
            $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->getMock('\Snscripts\MyCal\DateFactory')
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
            $BaseIntegration->extractData($Calendar)
        );
    }

    public function testExtractOptionsReturnArrayOfCalendarOptions()
    {
        $Factory = new CalendarFactory(
            $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface'),
            $this->getMock('\Snscripts\MyCal\DateFactory')
        );
        $Calendar = $Factory->load();

        $BaseIntegration = new BaseIntegration;

        $this->assertSame(
            [
                'weekStartsOn' => 1,
                'defaultTimezone' => 'Europe/London',
                'displayTable' => 'a:7:{s:10:"tableClass";s:11:"table mycal";s:7:"tableId";s:5:"MyCal";s:14:"headerRowClass";s:16:"mycal-header-row";s:11:"headerClass";s:12:"mycal-header";s:8:"rowClass";s:9:"mycal-row";s:9:"dateClass";s:10:"mycal-date";s:10:"emptyClass";s:11:"mycal-empty";}',
                'days' => 'a:7:{i:0;s:3:"Sun";i:1;s:3:"Mon";i:2;s:3:"Tue";i:3;s:3:"Wed";i:4;s:3:"Thu";i:5;s:3:"Fri";i:6;s:3:"Sat";}'
            ],
            $BaseIntegration->extractOptions($Calendar)
        );
    }
}
