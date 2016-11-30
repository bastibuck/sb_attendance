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
 * BACK END MODULES
 *
 */
array_insert($GLOBALS['BE_MOD'], 0, array
(
    'attendance' => array
    (
        'attendance_lists' => array
        (
            'icon'      => 'system/modules/sb_attendance/assets/icons/attendance_list_icon.png',
            'tables'    => array('tl_attendance_lists')
        )/*,
        'attendance_statistic' => array
        (
            'icon' => 'system/modules/sb_attendance/assets/icons/attendance_statistic_icon.png'
        )*/
    )
));

/**
 * Content Element
 *
 */
$GLOBALS['TL_CTE']['attendance'] = array
(
    'attendance_list_viewer' => 'AttendanceListViewer'
);


/**
 * CSS-Adjustment
 *
 * Zusätzliche CSS-Angabe für ein Backend-Feld (Hinweis in Anwesenheitsliste)
 */
if (TL_MODE == 'BE') 
{
    $GLOBALS['TL_CSS']['BACKEND'] = 'system/modules/sb_attendance/assets/css/backend.css';
}

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['activateAccount'][] = array('AttendanceHooks', 'addUserToAttendance');