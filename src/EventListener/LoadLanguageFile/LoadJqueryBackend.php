<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\CalendarWidget\EventListener\LoadLanguageFile;

use Contao\ArrayUtil;
use Haste\Util\Debug;
use Rhyme\CalendarWidget\Helper\ScopeHelper;


/**
 * Class LoadJqueryBackend
 * @package Rhyme\AIHA\Hooks\LoadLanguageFile
 */
class LoadJqueryBackend
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
    public static function run(string $strName, string $strLanguage, string $strCacheKey)
    {
        if (!ScopeHelper::isBackend() || static::$blnIsLoaded === true) {
            return;
        }

        ArrayUtil::arrayInsert($GLOBALS['TL_JAVASCRIPT'], 0,  ['bundles/rhymecalendarwidget/assets/js/jquery.js']);
        ArrayUtil::arrayInsert($GLOBALS['TL_JAVASCRIPT'], 1,  ['bundles/rhymecalendarwidget/assets/js/noconflict.js']);
        $GLOBALS['TL_JAVASCRIPT'] = \array_unique($GLOBALS['TL_JAVASCRIPT']);
        static::$blnIsLoaded = true;
    }
}