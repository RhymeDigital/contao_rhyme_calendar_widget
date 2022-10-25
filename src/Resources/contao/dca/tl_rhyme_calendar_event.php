<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace {

    use Contao\BackendUser;
    use Rhyme\CalendarWidget\Model\Calendar as CalendarModel;

    $GLOBALS['TL_DCA']['tl_rhyme_calendar_event'] = array
    (

        // Config
        'config' => array
        (
            'dataContainer'               => 'Table',
            'enableVersioning'            => true,
            'closed'                      => true,
            'ptable'                      => 'tl_rhyme_calendar',
            'sql' => array
            (
                'keys' => array
                (
                    'id' => 'primary',
                    'pid' => 'index',
                )
            )
        ),

        // List
        'list' => array
        (
            'sorting' => array
            (
                'mode'                    => 1,
                'fields'                  => array('name'),
                'flag'                    => 6,
                'panelLayout'             => 'filter;search,limit',
                //'filter'                  => array(array('status=?', '')),
            ),
            'label' => array
            (
                'fields'                  => array('name'),
                'format'                  => '%s',
            ),
            'global_operations' => array
            (
                'all' => array
                (
                    'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                    'href'                => 'act=select',
                    'class'               => 'header_edit_all',
                    'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
                )
            ),
            'operations' => array
            (
            )
        ),

        // Palettes
        'palettes' => array
        (
            '__selector__'                => array(),
            'default'                     => '{general_legend},name;'
        ),

        // Subpalettes
        'subpalettes' => array
        (

        ),

        // Fields
        'fields' => array
        (
            'id' => array
            (
                'sql'                     => "int(10) unsigned NOT NULL auto_increment"
            ),
            'pid' => array
            (
                'foreignKey'              => CalendarModel::getTable().".name",
                'relation'                => array('type'=>'hasOne', 'load'=>'lazy'),
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            ),
            'tstamp' => array
            (
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            ),
            'name' => array
            (
                'sql'                     => "varchar(255) NOT NULL default ''"
            ),
            'description' => array
            (
                'sql'                     => "TEXT NOT NULL default ''"
            ),
            'startTime' => [
                'sql'                     => "datetime NULL",
            ],
            'endTime' => [
                'sql'                     => "datetime NULL",
            ],
            'isAllDay' => array
            (
                'sql'                     => "char(1) NOT NULL default ''"
            ),
        )
    );
}