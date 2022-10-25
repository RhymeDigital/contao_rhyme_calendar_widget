<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\CalendarWidget\Helper;

use Contao\System;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\RequestStack;


class ScopeHelper
{
    public static function isBackend() {
        return System::getContainer()->get('contao.security.token_checker')->hasBackendUser();
    }

    public static function isFrontend() {
        return !static::isBackend();
    }
}