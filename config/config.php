<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   Attendance
 * @author    Sebastian Buck
 * @license   LGPL
 * @copyright Sebastian Buck 2013
 */

/**
 * FRONT END MODULES
 *
 *	Dem Array der Frontend-Module wird ein neuer Gruppen Eintrag 'attendance' und ein neues Modul 'attendance_list' hinzugefügt.
 *	Die Gruppe wird an vorletzter Stelle in das Array eingefügt und somit in der Dropdown-Liste dort angezeigt.
 *
 */
 
array_insert($GLOBALS['FE_MOD'], -1, array
(
	'attendance_group' => array
	(
		'attendance_list'    => 'ModuleAttendanceList'
	)
));

 


	
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
 *
 * Rufen bei bestimmten Aktionen gezielt kleine Methoden auf
 */		
$GLOBALS['TL_HOOKS']['activateAccount'][] = array('AttendanceHooks', 'addUserToAttendance');
