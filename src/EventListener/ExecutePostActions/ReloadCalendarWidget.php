<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\CalendarWidget\EventListener\ExecutePostActions;

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Database;
use Contao\Input;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\System;
use Rhyme\CalendarWidget\Helper\ScopeHelper;
use Rhyme\CalendarWidget\Widget\CalendarWidgetBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


/**
 * Class ReloadCalendarWidget
 */
class ReloadCalendarWidget extends Controller
{
    /**
     * @Hook("executePostActions", priority=100)
     */
    public function __invoke($strAction, $dc): void
    {
        if($strAction === 'reloadRhymeCalendarWidget')
        {
            $intId = Input::get('id');
            $strField = $dc->inputName = Input::post('name');

            // Handle the keys in "edit multiple" mode
            if (Input::get('act') == 'editAll')
            {
                $intId = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', $strField);
                $strField = preg_replace('/(.*)_[0-9a-zA-Z]+$/', '$1', $strField);
            }

            $dc->field = $strField;

            // The field does not exist
//            if (!isset($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]))
//            {
//                System::log('Field "' . $strField . '" does not exist in DCA "' . $dc->table . '"', __METHOD__, TL_ERROR);
//                throw new BadRequestHttpException('Bad request');
//            }

            $objRow = null;
            $varValue = null;

            // Load the value
            if (Input::get('act') != 'overrideAll')
            {
                if ($GLOBALS['TL_DCA'][$dc->table]['config']['dataContainer'] == 'File')
                {
                    $varValue = Config::get($strField);
                }
                elseif ($intId > 0 && Database::getInstance()->tableExists($dc->table))
                {
                    $objRow = Database::getInstance()->prepare("SELECT * FROM " . $dc->table . " WHERE id=?")
                        ->execute($intId);

                    // The record does not exist
                    if ($objRow->numRows < 1)
                    {
                        System::log('A record with the ID "' . $intId . '" does not exist in table "' . $dc->table . '"', __METHOD__, TL_ERROR);
                        throw new BadRequestHttpException('Bad request');
                    }

                    $varValue = $objRow->$strField;
                    $dc->activeRecord = $objRow;
                }
            }

            // Call the load_callback
            if (\is_array($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback']))
            {
                foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback'] as $callback)
                {
                    if (\is_array($callback))
                    {
                        $objCallback = System::importStatic($callback[0]);
                        $varValue = $objCallback->{$callback[1]}($varValue, $dc);
                    }
                    elseif (\is_callable($callback))
                    {
                        $varValue = $callback($varValue, $dc);
                    }
                }
            }


            $strClass = Input::post('widgetClassName', true) ?: (ScopeHelper::isBackend() ? $GLOBALS['BE_FFL']['rhymeCalendarWidget'] : $GLOBALS['TL_FFL']['rhymeCalendarWidget']);
            if (!$strClass || !\class_exists($strClass)) {
                throw new BadRequestHttpException('Widget class not found');
            }

            /** @var CalendarWidgetBase $strClass */
            $objWidget = new $strClass($strClass::getAttributesFromDca($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField], $dc->inputName, $varValue, $strField, $dc->table, $dc));

            throw new ResponseException($this->convertToResponse($objWidget->generate()));
        }
    }

    /**
     * Convert a string to a response object
     *
     * @param string $str
     *
     * @return Response
     */
    protected function convertToResponse(string $str): Response
    {
        return new Response(static::replaceOldBePaths($str));
    }

}