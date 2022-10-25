<?php

/**
 * Copyright (c) 2022 Rhyme Digital LLC (https://rhyme.digital)
 *
 * @license LGPL-3.0-or-later
 */

namespace Rhyme\CalendarWidget\Backend\Controller;

use Contao\BackendCustom;
use Contao\BackendTemplate;


class CalendarWidget extends BackendCustom
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Initialize the template in the constructor, so it is available in
        // the getTemplateObject() method
        $this->Template = new BackendTemplate('be_rhyme_calendar_widget');
    }
}

