<?php

/**
 * Copyright (c) 2022 Rhyme Digital LLC (https://rhyme.digital)
 *
 * @license LGPL-3.0-or-later
 */

namespace Rhyme\CalendarWidget\Model;

use Contao\Model;
use Contao\Model\Collection;

/**
 * Class Event
 * @package Rhyme\CalendarWidget\Model
 */
class Event extends Model
{
    /**
     * @var string
     */
    protected static $strTable = 'tl_rhyme_calendar_event';
}