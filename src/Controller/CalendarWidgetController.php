<?php

/**
 * Copyright (c) 2022 Rhyme Digital LLC (https://rhyme.digital)
 *
 * @license LGPL-3.0-or-later
 */

namespace Rhyme\CalendarWidget\Controller;

use Contao\Input;
use Contao\System;
use Contao\BackendUser;
use Contao\CoreBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Rhyme\CalendarWidget\Helper\ScopeHelper;
use Rhyme\CalendarWidget\Widget\CalendarWidgetBase;
use Rhyme\CalendarWidget\Model\Calendar as CalendarModel;


/**
 * Handles custom scripts
 * *
 * @Route("/rhyme_calendar_widget", defaults={"_token_check" = true})
 */
class CalendarWidgetController extends AbstractController
{

    /**
     * Handles custom script responses
     *
     * @return Response
     *
     * @Route("/rhyme_addUpdateCalendarEvent", name="rhyme_addUpdateCalendarEvent")
     */
    public function addUpdateCalendarEvent()
    {
        $this->initializeContaoFramework();

        $strClass = Input::post('widgetClassName') ? \urldecode(Input::post('widgetClassName')): (ScopeHelper::isBackend() ? $GLOBALS['BE_FFL']['toastCalendar'] : $GLOBALS['TL_FFL']['toastCalendar']);
        if (!$strClass || !class_exists($strClass)) {
            new JsonResponse([
                'success'   => false,
                'message'   => 'The class "'.$strClass.'" does not exist.',
                'rt'        => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
            ]);
        }

        $event = null;

        try {
            /** @var CalendarWidgetBase $strClass */
            $event = $strClass::handleAddUpdateCalendarEventAjaxRequest();
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'success'   => false,
                'message'   => $e->getMessage(),
                'rt'        => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
            ]);
        }

        return new JsonResponse([
            'success'   => true,
            'event'     => $event->row(),
            'calendar'  => $event->getRelated('pid')->row(),
            'rt'        => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
        ]);
    }



    /**
     * Handles custom script responses
     *
     * @return Response
     *
     * @Route("/rhyme_deleteCalendarEvent", name="rhyme_deleteCalendarEvent")
     */
    public function deleteCalendarEvent()
    {
        $this->initializeContaoFramework();

        $strClass = Input::post('widgetClassName') ? \urldecode(Input::post('widgetClassName')): (ScopeHelper::isBackend() ? $GLOBALS['BE_FFL']['toastCalendar'] : $GLOBALS['TL_FFL']['toastCalendar']);
        if (!$strClass || !class_exists($strClass)) {
            new JsonResponse([
                'success'   => false,
                'message'   => 'The class "'.$strClass.'" does not exist.',
                'rt'        => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
            ]);
        }

        try {
            /** @var CalendarWidgetBase $strClass */
            if (!$strClass::handleDeleteCalendarEventAjaxRequest()) {
                return new JsonResponse([
                    'success'   => false,
                    'message'   => 'The delete method failed.',
                    'rt'        => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
                ]);
            }
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'success'   => false,
                'message'   => $e->getMessage(),
                'rt'        => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
            ]);
        }

        return new JsonResponse([
            'success'   => true,
            'rt'        => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
        ]);
    }

}