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
	'AttendanceHooks'      => 'system/modules/sb_attendance/AttendanceHooks.php',
	// Models
	'sb_attendanceModel'   => 'system/modules/sb_attendance/models/sb_attendanceModel.php',

	// Modules
	'AttendanceListViewer' => 'system/modules/sb_attendance/modules/AttendanceListViewer.php',
	'UpdateAttendance'     => 'system/modules/sb_attendance/modules/UpdateAttendance.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_attendance_list_viewer' => 'system/modules/sb_attendance/templates',
));
