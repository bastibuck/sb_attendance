<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   Attendance
 * @author    Sebastian Buck
 * @license   LGPL
 * @copyright Sebastian Buck 2014
 */

/**
 * Table tl_attendance
 */
$GLOBALS['TL_DCA']['tl_attendance'] = array
(
    // Config
    'config' => array
    (
        'sql' => array
        (
            'keys' => array
            (
                // Schlüssel angelegt, id als Primärschlüssel der Tabelle, 
                // Kombination aus e_id, m_id und attendance_id muss einzigartig sein
                'id' => 'primary',
                'e_id,m_id,attendance_id' => 'unique'
            )
        )
    ),
    
    // Fields
    'fields' => array
    (
        // Automatisch hochzählende ID als Primärschlüssel
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        // Zeitstempel zum Festhalten der letzten Änderungen
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        // Fremdschlüssel aus tl_calendar_events
        'e_id' => array
        (
            'sql' => "int(10) unsigned NOT NULL"
        ),
        // Fremdschlüssel aus tl_member
        'm_id' => array
        (
            'sql' => "int(10) unsigned NOT NULL"
        ),
        // Flag-Variable für Anwesenheitsstatus
        'attendance' => array
        (
            'sql' => "char(1) NOT NULL default '0'"
        ),
        // Modul-ID um mehrere Listen zu ermöglichen
        'attendance_id' => array
        (
            'sql' => "int(10) unsigned NOT NULL"
        ),
        // Grund der Abwesenheit
        'reason' => array
        (
            'sql' => "varchar(255) NOT NULL default ''"
        )
    )
);