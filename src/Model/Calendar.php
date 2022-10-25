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
 * Class Calendar
 * @package Rhyme\CalendarWidget\Model
 */
class Calendar extends Model
{
    /**
     * @var string
     */
    protected static $strTable = 'tl_rhyme_calendar';


    /**
     * Find all rows by name, rowId and fromTable
     *
     * @param string  $name
     * @param integer $intRowId         The row ID
     * @param string  $strFromTable     The parent table name
     * @param array   $arrOptions       An optional options array
     *
     * @return Calendar[]|Calendar|null A collection of models or null if there are none
     */
    public static function findByNameRowIdAndFromTable(string $name, $intRowId, string $strFromTable, array $arrOptions=array())
    {
        $t = static::$strTable;

        $arrColumns = array("$t.name=? AND $t.rowId=? AND $t.fromTable=?");

        return static::findOneBy($arrColumns, array($name, $intRowId, $strFromTable), $arrOptions);
    }
}