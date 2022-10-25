<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace {

    use Contao\BackendUser;

    $GLOBALS['TL_DCA']['tl_rhyme_calendar'] = array
    (

        // Config
        'config' => array
        (
            'dataContainer'               => 'Table',
            'enableVersioning'            => true,
            'closed'                      => true,
            'ctable'                      => ['tl_rhyme_calendar_event'],
            'sql' => array
            (
                'keys' => array
                (
                    'id' => 'primary',
                    'name,fromTable,rowId' => 'index',
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
            'tstamp' => array
            (
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            ),
            'name' => array
            (
                'sql'                     => "varchar(255) NOT NULL default ''"
            ),
            'fromTable' => array
            (
                'sql'                     => "varchar(128) NOT NULL default ''"
            ),
            'rowId' => array
            (
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            ),
            'fe_editable' => array
            (
                'sql'                     => "char(1) NOT NULL default ''"
            ),
        )
    );
}