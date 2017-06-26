<?php
namespace Snscripts\MyCal\Formatters;

use Snscripts\MyCal\Calendar\Date;
use Snscripts\MyCal\Calendar\Event;
use Snscripts\MyCal\Calendar\Calendar;
use Snscripts\MyCal\Interfaces\FormatterInterface;

class BootstrapFormatter implements FormatterInterface
{
    /**
     * Calendar Object
     */
    protected $Calendar;

    /**
     * set the calendar
     *
     * @param Calendar $Calendar
     */
    public function setCalendar(Calendar $Calendar)
    {
        $this->Calendar = $Calendar;
    }

    /**
     * process table
     *
     * @param string $parsedHead
     * @param string $parsedBody
     * @return string
     */
    public function parseTable($parsedHead, $parsedBody)
    {
        $table = '<table class="table mycal" id="MyCal">';
        $table .= '<thead>' . $parsedHead . '</thead>';
        $table .= '<tbody>' . $parsedBody . '</tbody>';
        $table .= '</table>';
        return $table;
    }

    /**
     * process a header row
     *
     * @param string $parsedCells
     * @return string
     */
    public function parseHeaderRow($parsedCells)
    {
        $row = '<tr class="mycal-header-row">';
        $row .= $parsedCells;
        $row .= '</tr>';
        return $row;
    }

    /**
     * process a header cell
     *
     * @param int $day
     * @return string
     */
    public function parseHeaderCell($day)
    {
        $cell = '<th class="mycal-header">';
        $cell .= $this->Calendar->getOptions()->days[$day];
        $cell .= '</th>';
        return $cell;
    }

    /**
     * process a row
     *
     * @param string $parsedCells
     * @return string
     */
    public function parseDateRow($parsedCells)
    {
        $row = '<tr class="mycal-row">';
        $row .= $parsedCells;
        $row .= '</tr>';
        return $row;
    }

    /**
     * process a date cell
     *
     * @param Date $Date
     * @param string $parsedEvents
     * @return string
     */
    public function parseDateCell(Date $Date, $parsedEvents = '')
    {
        $today = ($Date->isToday() ? ' today' : '');

        $cell = '<td class="mycal-date' . $today . '">';
        $cell .= '<div class="date-num"><sup>' . $Date->display('j') . '</sup></div>';
        $cell .= '<div class="events">' . $parsedEvents . '</div>';
        $cell .= '</td>';
        return $cell;
    }

    /**
     * process an empty cell
     *
     * @return string
     */
    public function parseEmptyCell()
    {
        return '<td class="mycal-empty"> &nbsp; </td>';
    }

    /**
     * process an individual event
     *
     * @param Event $Event
     * @return string
     */
    public function parseEvent(Event $Event)
    {
        $display = '<span class="badge badge-primary">';
        $display .= $Event->name . ': ';
        $display .= $Event->displayStart('H:i') . ' - ' . $Event->displayEnd('H:i');
        $display .= '</span>';
        return $display;
    }
}
