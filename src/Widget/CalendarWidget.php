<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\CalendarWidget\Widget;

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
use Rhyme\CalendarWidget\Model\Event as EventModel;
use Rhyme\CalendarWidget\Model\Calendar as CalendarModel;

/**
 * Class ToastCalendarWidget
 * @package Rhyme\CalendarWidget\Widget
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
    protected $strTemplate = 'be_widget';

    /**
     * Calendar
     * @var CalendarModel|null
     */
    protected ?CalendarModel $objCalendar = null;

    /**
     * Events
     * @var EventModel|Collection|null
     */
    protected $objEvents = null;


    /**
     * Generate the widget and return it as string
     *
     * @return string
     */
    public function generate() : string
    {
        // Make sure we have a calendar row for this widget
        $this->getCalendarModel();
        $this->getCalendarEvents();

        $objTemplate = new BackendTemplate($this->custom_calendar_widget_tpl ?: 'calendar_widget_backend');
        $objTemplate->setData(\array_merge($this->arrConfiguration, $this->arrAttributes));
        $objTemplate->strTable          = $this->strTable;
        $objTemplate->strField          = $this->strField;
        $objTemplate->strName           = $this->strName;
        $objTemplate->strId             = $this->strId;
        $objTemplate->eventCount        = $this->objEvents !== null ? $this->objEvents->count() : 0;
        $objTemplate->widgetClassName   = \get_called_class();
        $objTemplate->modalUrl          = $this->getModalUrl();
        return $objTemplate->parse();
    }


    /**
     * @return mixed
     */
    public function getModalUrl()
    {
        $container = System::getContainer();
        return $container->get('router')->generate('rhyme_calendar_widget_backend_controller', array
        (
            'popup'             => 1,
            'table'             => $this->strTable,
            'id'                => $this->objDca ? $this->objDca->id : $this->strId,
            'name'              => $this->strName,
            'ref'               => $container->get('request_stack')->getCurrentRequest()->attributes->get('_contao_referer_id'),
            'rt'                => REQUEST_TOKEN,
            'widgetClassName'   => \get_called_class(),
        ));
    }


    /**
     * @return array[]
     */
    public static function getAjaxData() : array
    {
        $calendarData = [
            'id'            => \intval(Input::post('calendarId')),
            'name'          => \strval(Input::post('calendarName')),
            'fromTable'     => \strval(Input::post('calendarFromTable')),
            'rowId'         => \intval(Input::post('calendarRowId')),
            'fe_editable'   => Input::post('calendarFeEditable') ? 1 : '',
        ];

        $eventData = [
            'id'            => \intval(Input::post('eventId')),
            'name'          => \strval(Input::post('eventName')),
            'description'   => \strval(Input::post('eventDescription')),
            'startTime'     => \strval(Input::post('eventStartTime')) ?: null,
            'endTime'       => \strval(Input::post('eventEndTime')) ?: null,
            'isAllDay'      => \strval(Input::post('eventIsAllDay')),
        ];

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
            $this->objCalendar->rowId     = $this->objDca ? $this->objDca->id : $this->strId;
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
}
