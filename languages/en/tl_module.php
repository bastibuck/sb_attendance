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
 
// Legends
$GLOBALS['TL_LANG']['tl_module']['attendance_calender_legend'] = 'Calendar options';
$GLOBALS['TL_LANG']['tl_module']['attendance_show_legend'] = 'Expired events and locking time';
$GLOBALS['TL_LANG']['tl_module']['attendance_statusOptions_legend'] = 'Status options';
$GLOBALS['TL_LANG']['tl_module']['attendance_memberRoles_legend'] = 'Member roles';
$GLOBALS['TL_LANG']['tl_module']['attendance_style_legend'] = 'Display options';

// Fields
$GLOBALS['TL_LANG']['tl_module']['al_pickCalendar'][0] = 'Select calendars';
$GLOBALS['TL_LANG']['tl_module']['al_pickCalendar'][1] = 'Select one or more calendars to be managed in the attendance list.';

$GLOBALS['TL_LANG']['tl_module']['al_expiredEvents'][0] = 'Expired events';
$GLOBALS['TL_LANG']['tl_module']['al_expiredEvents'][1] = 'How many expired events to you want to be displayed?';

$GLOBALS['TL_LANG']['tl_module']['al_expireTime'][0] = 'Locking time for events';
$GLOBALS['TL_LANG']['tl_module']['al_expireTime'][1] = 'Set a locking time (in hours) for events. No more status changes are allowed afterwards.';

$GLOBALS['TL_LANG']['tl_module']['al_disableThird'][0] = 'Disable third option';
$GLOBALS['TL_LANG']['tl_module']['al_disableThird'][1] = 'Third status option "later" will be disabled/enabled.';

$GLOBALS['TL_LANG']['tl_module']['al_defaultStatus'][0] = 'Default status';
$GLOBALS['TL_LANG']['tl_module']['al_defaultStatus'][1] = 'This status will be used for new fields.';

$GLOBALS['TL_LANG']['tl_module']['al_roleAdvice'][0] = ' ';
$GLOBALS['TL_LANG']['tl_module']['al_roleAdvice'][1] = 'Tip: Member roles can be managed in the member settings.';

$GLOBALS['TL_LANG']['tl_module']['al_iconSet'][0] = 'Icon-set';
$GLOBALS['TL_LANG']['tl_module']['al_iconSet'][1] = 'Choose an icon set.';

$GLOBALS['TL_LANG']['tl_module']['al_useCSS'][0] = 'Use provided CSS';
$GLOBALS['TL_LANG']['tl_module']['al_useCSS'][1] = 'Do you want to use the provided CSS to style the attendance list?';

// Select options
$GLOBALS['TL_LANG']['tl_module']['flat_thick'] = 'Flat-Design, thick';
$GLOBALS['TL_LANG']['tl_module']['flat_thick_alternative'] = 'Flat-Design, thick alternative';
$GLOBALS['TL_LANG']['tl_module']['flat_thin'] = 'Flat-Design thin';


// options for radio buttons (default status)
$GLOBALS['TL_LANG']['tl_module']['al_radio']['0'] = 'unknown';
$GLOBALS['TL_LANG']['tl_module']['al_radio']['1'] = 'attending';
$GLOBALS['TL_LANG']['tl_module']['al_radio']['2'] = 'absent';
$GLOBALS['TL_LANG']['tl_module']['al_radio']['3'] = 'later';
