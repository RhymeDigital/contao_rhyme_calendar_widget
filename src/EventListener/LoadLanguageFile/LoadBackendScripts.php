<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\CalendarWidget\EventListener\LoadLanguageFile;

use Contao\Frontend;


/**
 * Class LoadBackendScripts
 * @package Rhyme\CalendarWidget\EventListener\LoadLanguageFile
 */
class LoadBackendScripts
{

    /**
     * @var bool
     */
    protected static bool $blnIsLoaded = false;

    /**
     * @param string $strName
     * @param string $strLanguage
     * @param string $strCacheKey
     * @return void
     */
    public function __invoke(string $strName, string $strLanguage, string $strCacheKey)
    {
        if (TL_MODE !== 'BE' || static::$blnIsLoaded === true) {
            return;
        }

        $GLOBALS['TL_JAVASCRIPT'] = \array_unique(\array_merge($GLOBALS['TL_JAVASCRIPT'] ?? [], [
            'https//cdn3.devexpress.com/jslib/20.1.4/js/dx.web.js',
        ]));

        $GLOBALS['TL_CSS'] = \array_unique(\array_merge($GLOBALS['TL_CSS'] ?? [], [
            'https://cdn3.devexpress.com/jslib/20.1.4/css/dx.common.css',
            'https://cdn3.devexpress.com/jslib/20.1.4/css/dx.light.css',
        ]));

        static::$blnIsLoaded = true;
    }
}