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
$GLOBALS['TL_LANG']['tl_attendance_lists']['title_legend'] = 'Titel und Überschrift';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_calender_legend'] = 'Kalender-Auswahl';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_member_legend'] = 'Mitglieder-Gruppen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_statusOptions_legend'] = 'Statusoptionen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_memberRoles_legend'] = 'Mitglieder markieren';
$GLOBALS['TL_LANG']['tl_attendance_lists']['attendance_style_legend'] = 'Darstellungsoptionen';

// Operationen
$GLOBALS['TL_LANG']['tl_attendance_lists']['new'][0] = 'Neue Anwesenheitsliste';
$GLOBALS['TL_LANG']['tl_attendance_lists']['new'][1] = 'Eine neue Anwesenheitsliste anlegen';
 
$GLOBALS['TL_LANG']['tl_attendance_lists']['edit'][0] = 'Anwesenheitsliste bearbeiten';
$GLOBALS['TL_LANG']['tl_attendance_lists']['edit'][1] = 'Anwesenheitsliste ID %s bearbeiten';
 
$GLOBALS['TL_LANG']['tl_attendance_lists']['delete'][0] = 'Anwesenheitsliste löschen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['delete'][1] = 'Anwesenheitsliste ID %s löschen';
 
$GLOBALS['TL_LANG']['tl_attendance_lists']['show'][0] = 'Anwesenheitslistendetails';
$GLOBALS['TL_LANG']['tl_attendance_lists']['show'][1] = 'Details des Anwesenheitsliste ID %s anzeigen';

// Felder 
$GLOBALS['TL_LANG']['tl_attendance_lists']['title'][0] = 'Name';
$GLOBALS['TL_LANG']['tl_attendance_lists']['title'][1] = 'Geben Sie hier einen Namen für die Anwesenheitsliste ein.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickCalendar'][0] = 'Kalenderauswahl';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickCalendar'][1] = 'Wählen Sie einen oder mehrere Kalender aus.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickMembers'][0] = 'Mitgliedergruppen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickMembers'][1] = 'Wählen Sie eine oder mehrere Mitglieder-Gruppen aus.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expiredEvents'][0] = 'Abgelaufene Termine';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expiredEvents'][1] = 'Wie viele abgelaufene Termine sollen angezeigt werden?';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expireTime'][0] = 'Sperrzeit von Terminen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expireTime'][1] = 'Legen Sie die Sperrzeit (in Stunden) fest, ab wann die Statusänderungen vor einem Termin gesperrt werden sollen.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_eventsPerPage'][0] = 'Termine pro Seite';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_eventsPerPage'][1] = 'Legen Sie fest, wie viele Termine pro Seite angezeigt werden sollen (Pagination). Bei "0" wird Pagination deaktiviert.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_disableThird'][0] = 'Dritte Option deaktivieren';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_disableThird'][1] = 'Die Dritte Statusoption "komme später" wird hierüber de/aktiviert.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_askReason'][0] = 'Grund der Abwesenheit';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_askReason'][1] = 'Sollen Mitglieder beim Ändern des Status auf "abwesend" nach dem Grund gefragt werden?';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_defaultStatus'][0] = 'Standard Status';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_defaultStatus'][1] = 'Dieser Status wird für neue Felder verwendet.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCoach'][0] = 'Trainerrolle zuweisen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCoach'][1] = 'Definieren Sie einen Trainer für diese Liste. Dieses Mitglied erhält mehr Rechte und wird an erster Stelle gelistet.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Coach'][0] = 'Trainer';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Coach'][1] = '';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CoachDescription'][0] = 'optionale Bezeichnung';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CoachDescription'][1] = 'Sie können hier eine eigene Bezeichnung eingeben, die anstatt des Standards "<i>Trainer</i>" verwendet wird.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCaptain'][0] = 'Kapitän festlegen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCaptain'][1] = 'Definieren Sie einen Mannschaftskapitän für diese Liste.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Captain'][0] = 'Kapitän';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Captains'][1] = 'Definieren Sie einen Mannschaftskapitän für diese Liste.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CaptainDescription'][0] = 'optionale Bezeichnung';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CaptainDescription'][1] = 'Sie können hier eine eigene Bezeichnung eingeben, die anstatt des Standards "<i>Kapitän</i>" verwendet wird.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkAdmin'][0] = 'Administrator festlegen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkAdmin'][1] = 'Definieren Sie einen Administrator für diese Liste. Dieses Mitglied erhält erweiterte Rechte.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Admin'][0] = 'Administrator';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Admin'][1] = '';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_roleAdvice'][0] = 'Hinweis';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_roleAdvice'][1] = "Mitglieder können in den <a href='".Environment::get('path')."/contao/main.php?do=member'>Mitgliedereinstellungen</a> inaktiv gesetzt werden und tauchen dann nicht mehr in der Liste auf.<br>";

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name'][0] = 'Darstellung des Namen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name'][1] = 'Wie soll der Name eines Mitgliedes angezeigt werden?';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_iconSet'][0] = 'Icon-Set';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_iconSet'][1] = 'Hier können Sie das verwendete Icon-Set auswählen.';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_useCSS'][0] = 'Mitgeliefertes CSS verwenden';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_useCSS'][1] = 'Soll das Aussehen der Anwesenheitsliste durch das mitgelieferte CSS beeinflusst werden?';

$GLOBALS['TL_LANG']['tl_attendance_lists']['al_showInfos'][0] = 'Hoverbox für Informationen';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_showInfos'][1] = 'Sollen zusätzliche Informationen zu einem Termin in einer Hoverbox angezeigt werden?';

// Optionen im Select-Feld (Icon-Set)
$GLOBALS['TL_LANG']['tl_attendance_lists']['flat_thick'] = 'Flat-Design, breite Symbole';
$GLOBALS['TL_LANG']['tl_attendance_lists']['flat_thick_alternative'] = 'Flat-Design, breite, alternative Symbole ';
$GLOBALS['TL_LANG']['tl_attendance_lists']['flat_thin'] = 'Flat-Design dünne Symbole';

// Optionen im Radio-Feld (Standard-Status)
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio']['0'] = 'Unbekannt';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio']['1'] = 'Anwesend';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio']['2'] = 'Abwesend';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio']['3'] = 'Komme später';

// Optionen der Darstellung des Namen
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name']['username'] = 'Nutzername';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name']['firstname'] = 'Vorname';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name']['lastname'] = 'Nachname';
$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name']['first_last'] = 'Vor- und Nachname';