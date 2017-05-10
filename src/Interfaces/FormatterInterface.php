<?php
namespace Snscripts\MyCal\Interfaces;

use Snscripts\MyCal\Calendar\Date;
use Snscripts\MyCal\Calendar\Event;
use Snscripts\MyCal\Calendar\Calendar;

interface FormatterInterface
{
    /**
     * set the calendar object
     * should set to protected $Calendar on the formatter
     *
     * @param Calendar $Calendar
     */
    public function setCalendar(Calendar $Calendar);

    /**
     * process table
     *
     * @param string $parsedHead
     * @param string $parsedBody
     * @return string
     */
    public function parseTable($parsedHead, $parsedBody);

    /**
     * process a header row
     *
     * @param string $parsedCells
     * @return string
     */
    public function parseHeaderRow($parsedCells);

    /**
     * process a header cell
     *
     * @param int z$day
     * @return string
     */
    public function parseHeaderCell($day);

    /**
     * process a row
     *
     * @param string $parsedCells
     * @return string
     */
    public function parseDateRow($parsedCells);

    /**
     * process a date cell
     *
     * @param Date $Date
     * @param string $parsedEvents
     * @return string
     */
    public function parseDateCell(Date $Date, $parsedEvents);

    /**
     * process an empty cell
     *
     * @return string
     */
    public function parseEmptyCell();

    /**
     * process an individual event
     *
     * @param Event $Event
     * @return string
     */
    public function parseEvent(Event $Event);
}
