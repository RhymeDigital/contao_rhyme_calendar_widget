<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\CalendarWidget\Widget;

use Contao\FilesModel;
use Contao\Image;
use Contao\Input;
use Contao\Config;
use Contao\Widget;
use Contao\System;
use Contao\Database;
use Contao\BackendUser;
use Contao\FrontendUser;
use Contao\StringUtil;
use Rhyme\CalendarWidget\Helper\ScopeHelper;
use Rhyme\CalendarWidget\Model\Event as EventModel;
use Rhyme\CalendarWidget\Model\Calendar as CalendarModel;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CalendarWidgetBase
 * @package Rhyme\CalendarWidget\Widget
 */
abstract class CalendarWidgetBase extends Widget
{

    /**
     * @param array $calendarData
     * @param array $eventData
     * @return EventModel
     */
    public static function addUpdateCalendarEvent(array $calendarData, array $eventData)
    {
        if (!\intval($calendarData['id']) || ($objCalendar = CalendarModel::findByPk($calendarData['id'])) === null) {
            $objCalendar = static::addUpdateCalendar($calendarData);
        }

        if (!\intval($eventData['id']) || ($objEvent = EventModel::findByPk($eventData['id'])) === null) {
            $objEvent = new EventModel();
        }

        $objEvent->tstamp = time();
        $objEvent->pid = $objCalendar->id;

        foreach ($eventData as $key=>$data) {
            if ($key === 'id') continue;
            $objEvent->$key = $data;
        }

        return $objEvent->save();
    }


    /**
     * @param array $calendarData
     * @return CalendarModel
     */
    public static function addUpdateCalendar(array $calendarData) : CalendarModel
    {
        if (\intval($calendarData['id'])) {
            $objCalendar = CalendarModel::findByPk($calendarData['id']);
        }
        else {
            $objCalendar = new CalendarModel();
        }

        $objCalendar->tstamp = time();

        foreach ($calendarData as $key=>$data) {
            if ($key === 'id') continue;
            $objCalendar->$key = $data;
        }

        return $objCalendar->save();
    }


    /**
     * @param int $id
     * @return bool
     */
    public static function deleteEvent(int $id) : bool
    {
        $e = EventModel::getTable();
        Database::getInstance()->prepare("DELETE FROM $e WHERE id=?")->execute($id);
        return true;
    }


    /**
     * @return EventModel
     * @throws \Exception
     */
    public static function handleAddUpdateCalendarEventAjaxRequest() : EventModel
    {
        if (static::isBackendUserLoggedOut()) {
            throw new \Exception('LOGGED OUT');
        }
        if (static::isFrontendUserWithoutPermissions()) {
            throw new \Exception('Front end user does not have permission to edit.');
        }
        if (empty($data = static::getAjaxData()) ||
            empty($data['calendarData']) ||
            empty($data['eventData'])
        ) {
            throw new \Exception('Could not retrieve data from Input.');
        }

        // Create event (and calendar if needed)
        if (($event = static::addUpdateCalendarEvent($data['calendarData'], $data['eventData'])) === null) {
            throw new \Exception('Event creation failed.');
        }

        return $event;
    }


    /**
     * @return bool
     * @throws \Exception
     */
    public static function handleDeleteCalendarEventAjaxRequest() : bool
    {
        if (static::isBackendUserLoggedOut()) {
            throw new \Exception('LOGGED OUT');
        }
        if (static::isFrontendUserWithoutPermissions()) {
            throw new \Exception('Front end user does not have permission to edit.');
        }
        if (empty($data = static::getAjaxData()) ||
            empty($data['calendarData']) ||
            empty($data['eventData'])
        ) {
            throw new \Exception('Could not retrieve data from Input.');
        }

        return static::deleteEvent($data['eventData']['id']);
    }


    /**
     * @return bool
     */
    protected static function isBackendUserLoggedOut() : bool
    {
        return !BackendUser::getInstance()->id;
    }


    /**
     * Don't allow front end users to create calendars and events?
     * Todo: check fe_editable on calendar
     * @return bool
     */
    protected static function isFrontendUserWithoutPermissions() : bool
    {
        return ScopeHelper::isFrontend() && !FrontendUser::getInstance()->id;
    }


    abstract public static function getAjaxData();


    abstract public function getModalUrl();
}
