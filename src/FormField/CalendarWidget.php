<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\CalendarWidget\FormField;

use Contao\Date;
use Contao\BackendTemplate;
use Contao\Image;
use Contao\Input;
use Contao\Config;
use Contao\Widget;
use Contao\System;
use Contao\Database;
use Contao\StringUtil;
use Contao\Model\Collection;
use Rhyme\CalendarWidget\Widget\CalendarWidgetBase;
use Rhyme\CalendarWidget\Model\Calendar as CalendarModel;
use Rhyme\CalendarWidget\Model\Event as EventModel;

/**
 * Class CalendarWidget
 * @package Rhyme\CalendarWidget\FormField
 */
class CalendarWidget extends CalendarWidgetBase
{

    /**
     * Submit user input
     * @var boolean
     */
    protected $blnSubmitInput = false;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'form_calendar_widget';

    /**
     * Calendar
     * @var CalendarModel|null
     */
    protected ?CalendarModel $objCalendar = null;

    /**
     * Events
     * @var EventModel|Collection
     */
    protected $objEvents = null;


    /**
     * Parses the widget and return it as string
     *
     * @param array|null $arrAttributes
     * @return string
     */
    public function parse($arrAttributes=null) : string
    {
        // Make sure we have a calendar row for this widget
        $this->getCalendarModel();
        $this->getCalendarEvents();

        $arrAttributes['calendar']  = $this->objCalendar;
        $arrAttributes['events']    = $this->objEvents;

        return parent::parse($arrAttributes);
    }


    /**
     * @return array[]
     */
    public static function getAjaxData() : array
    {
        $calendarData = [];

        $eventData = [];

        if ($pid = \intval(Input::post('calendarId'))) {
            $eventData['pid'] = $pid;
        }

        return ['calendarData' => $calendarData, 'eventData' => $eventData];
    }


    /**
     * Make sure we have a calendar row for this widget
     * @return CalendarModel
     */
    protected function getCalendarModel() : CalendarModel
    {
        if ($this->objCalendar !== null) {
            return $this->objCalendar;
        }

        if (($this->objCalendar = CalendarModel::findByNameRowIdAndFromTable(
            $this->strName,
            $this->objDca ? $this->objDca->id : $this->strId,
            $this->strTable)) === null) {

            $this->objCalendar = new CalendarModel();
            $this->objCalendar->tstamp    = time();
            $this->objCalendar->name      = $this->strName;
            $this->objCalendar->rowId     = $this->objDca->id;
            $this->objCalendar->fromTable = $this->strTable;
            $this->objCalendar->save();
        }
        return $this->objCalendar->current();
    }


    /**
     * Get events for the widget's calendar
     * @param array $arrOptions
     * @return EventModel|Collection|null
     */
    protected function getCalendarEvents(array $arrOptions=array())
    {
        if ($this->objCalendar === null) {
            return null;
        }

        return $this->objEvents = EventModel::findByPid($this->objCalendar->id);
    }

    /**
     * Use the parse() method instead.
     *
     * @throw \BadMethodCallException
     */
    public function generate()
    {
        throw new \BadMethodCallException('Please use the parse() method instead!');
    }
}
