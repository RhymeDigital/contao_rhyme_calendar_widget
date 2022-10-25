<?php

/**
 * Copyright (c) 2022 Rhyme Digital LLC (https://rhyme.digital)
 *
 * @license LGPL-3.0-or-later
 */

namespace Rhyme\CalendarWidget\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Rhyme\CalendarWidget\Backend\Controller\CalendarWidget as CalendarWidgetController;


/**
 * @Route(defaults={"_scope" = "backend", "_token_check" = true})
 */
class BackendController extends AbstractController
{

    /**
     * Handles custom script responses
     *
     * @return Response
     *
     * @Route("/contao/rhyme_calendar_widget_backend", name="rhyme_calendar_widget_backend_controller")
     */
    public function backendCalendar()
    {
        $this->initializeContaoFramework();

        $controller = new CalendarWidgetController();

        return $controller->run();
    }

}