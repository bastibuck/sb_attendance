<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Sb_attendance
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'AttendanceHooks'            => 'system/modules/sb_attendance/AttendanceHooks.php',
	// Models
	'sb_attendanceSettingsModel' => 'system/modules/sb_attendance/models/sb_attendanceSettingsModel.php',

	// Modules
	'ModuleAttendanceList'       => 'system/modules/sb_attendance/modules/ModuleAttendanceList.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_attendance_list' => 'system/modules/sb_attendance/templates',
));
