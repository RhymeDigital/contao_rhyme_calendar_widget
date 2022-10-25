<?php

namespace {

    /**
     * Rhyme Calendar Widget Bundle
     *
     * Copyright (c) 2022 Rhyme.Digital
     *
     * @license LGPL-3.0+
     */

    use Contao\Input;
    use Rhyme\CalendarWidget\Model;
    use Rhyme\CalendarWidget\Widget;

    // region BE scripts

    /**
     * BE scripts
     */
    if (TL_MODE === 'BE') {
        $GLOBALS['TL_CSS'][] = 'bundles/rhymecalendarwidget/assets/css/backend.css';
    }

    // endregion

    // region Models

    /**
     * Models
     */
    $GLOBALS['TL_MODELS'][Model\Calendar::getTable()]           = Model\Calendar::class;
    $GLOBALS['TL_MODELS'][Model\Event::getTable()]              = Model\Event::class;

    // endregion

    // region Back end form fields

    /**
     * Back end form fields
     */
    $GLOBALS['BE_FFL']['rhymeCalendarWidget']                   = Widget\CalendarWidget::class;

    // endregion

}
