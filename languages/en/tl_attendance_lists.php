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
 * Übersetzungen für die Eingabemaske der Anwesenheitsliste 
 */ 
 
// Legends
$GLOBALS['TL_LANG']['tl_attendance_lists']['title_legend'] = 'Title and Headline';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_calender_legend'] = 'Calendar options';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_member_legend'] = 'Member groups';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_statusOptions_legend'] = 'Status options';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_memberRoles_legend'] = 'Member roles';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_descriptions'] = 'optional descriptions';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_style_legend'] = 'Display options';

// Operationen
$GLOBALS['TL_LANG']['tl_attendance_lists']['new'][0] = 'New attendance list';
$GLOBALS['TL_LANG']['tl_attendance_lists']['new'][1] = 'Create new attendance list';
 
$GLOBALS['TL_LANG']['tl_attendance_lists']['edit'][0] = 'Edit attendance list';
$GLOBALS['TL_LANG']['tl_attendance_lists']['edit'][1] = 'Edit attendance list ID %s';
 
$GLOBALS['TL_LANG']['tl_attendance_lists']['delete'][0] = 'Delete attendance list';
$GLOBALS['TL_LANG']['tl_attendance_lists']['delete'][1] = 'Delete attendance list ID %s';
 
$GLOBALS['TL_LANG']['tl_attendance_lists']['show'][0] = 'Show attendance list\'s details';
$GLOBALS['TL_LANG']['tl_attendance_lists']['show'][1] = 'Show attendance list\'s ID %s details';

// Felder 
$GLOBALS['TL_LANG']['tl_attendance_lists']['title'][0] = 'Name';
$GLOBALS['TL_LANG']['tl_attendance_lists']['title'][1] = 'Enter a name for the attendance list.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickCalendar'][0] = 'Select calendars';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickCalendar'][1] = 'Select one or more calendars to be managed in the attendance list.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickMembers'][0] = 'Member groups';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickMembers'][1] = 'Select one or more member groups to be managed in the attendance list.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expiredEvents'][0] = 'Expired events';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expiredEvents'][1] = 'How many expired events to you want to be displayed?';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expireTime'][0] = 'Lock time for events';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expireTime'][1] = 'Set a locking time (in hours) for events. No more status changes are allowed afterwards.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_eventsPerPage'][0] = 'Events per page';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_eventsPerPage'][1] = 'Define the events per page (pagination). Setting this to "0" will disable pagination.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_disableThird'][0] = 'Disable third option';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_disableThird'][1] = 'Third status option "later" will be disabled.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_askReason'][0] = 'Reason for absence';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_askReason'][1] = 'Should members be asked for the reason after changing a status to "absent"?';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_defaultStatus'][0] = 'Default status';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_defaultStatus'][1] = 'This status will be used for new fields.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCoach'][0] = 'Assign coach role';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCoach'][1] = 'This member will be granted more rights and will be excluded from the total number of attendants.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Coach'][0] = 'Coach';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Coach'][1] = '';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCaptain'][0] = 'Team captain';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCaptain'][1] = 'This member is the team\'s captain.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Captain'][0] = 'Captain';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Captain'][1] = '';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkAdmin'][0] = 'Assign administrator role';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkAdmin'][1] = 'This member will be granted more rights.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Admin'][0] = 'Administrator';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Admin'][1] = '';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_roleAdvice'][0] = 'Tip';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_roleAdvice'][1] = "Members can be set as inactive in the <a href='".Environment::get('path')."/contao/main.php?do=member'>member settings</a>.<br>";

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CoachDescription'][0] = 'Description "<i>'.$GLOBALS['TL_LANG']['al_frontend']['coach'].'</i>"';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CoachDescription'][1] = 'Enter an optional description to be used instead of the standard "<i>'.$GLOBALS['TL_LANG']['al_frontend']['coach'].'</i>".';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CaptainDescription'][0] = 'Description "<i>'.$GLOBALS['TL_LANG']['al_frontend']['captain'].'</i>"';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CaptainDescription'][1] = 'Enter an optional description to be used instead of the standard "<i>'.$GLOBALS['TL_LANG']['al_frontend']['captain'].'</i>".';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_AttendantsDescription'][0] = 'Description "<i>'.$GLOBALS['TL_LANG']['al_frontend']['attendants'].'</i>"';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_AttendantsDescription'][1] = 'Enter an optional description to be used instead of the standard "<i>'.$GLOBALS['TL_LANG']['al_frontend']['attendants'].'</i>" (number of attendants).';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name'][0] = 'Name settings';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name'][1] = 'How do you want a member\'s name displayed in the list?';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_iconSet'][0] = 'Icon-Set';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_iconSet'][1] = 'Choose an icon set.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_useCSS'][0] = 'Use provided CSS';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_useCSS'][1] = 'Do you want to use the provided CSS to style the attendance list?';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_showInfos'][0] = 'Hoverbox for more information';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_showInfos'][1] = 'Do you want additional information for events to be displayed in a hover-box?';

// Select options
$GLOBALS['TL_LANG']['tl_attendance_lists']['flat_thick'] = 'Flat-Design, thick';
$GLOBALS['TL_LANG']['tl_attendance_lists']['flat_thick_alternative'] = 'Flat-Design, thick alternative';
$GLOBALS['TL_LANG']['tl_attendance_lists']['flat_thin'] = 'Flat-Design, thin';

// options for radio buttons (default status)
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio']['0'] = 'unknown';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio']['1'] = 'attending';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio']['2'] = 'absent';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio']['3'] = 'later';

// options for name settings
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name']['username'] = 'Username';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name']['firstname'] = 'Firstname';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name']['lastname'] = 'Lastname';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name']['first_last'] = 'First and lastname';