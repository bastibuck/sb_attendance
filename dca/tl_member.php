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
 * Config
 *
 * Einstellungen des DCA für tl_member (Mitgliederverwaltung)
 */

/*
 * Erweitert das System um einen onsubmit_callback: Ruft beim Absenden des BE-Formulars die 
 * Funktion al_SetInactiveMember (siehe unten) auf.
 *
 */
array_insert($GLOBALS['TL_DCA']['tl_member']['config']['onsubmit_callback'], -1, array
    (
        array('tl_attendanceMember', 'al_SetInactiveMember')
    )
);

/*
 * Erweitert das System um einen ondelete_callback: Ruft beim löschen eines Mitgliedes die 
 * Funktion al_deleteMember (siehe unten) auf.
 * 	 
 */
array_insert($GLOBALS['TL_DCA']['tl_member']['config']['ondelete_callback'], -1, array
    (
        array('tl_attendanceMember', 'al_deleteMember')
    )
);

/**
 * List
 *
 * Erweitert die Funktion toggleIcon, die beim Ändern des disable-Status 
 * eines Mitgliedes (Auge) aufgerufen wird, so dass tl_attendance entsprechend aktualisiert wird
 */
$GLOBALS['TL_DCA']['tl_member']['list']['operations']['toggle']['button_callback'] = array('tl_attendanceMember', 'toggleIcon');


/**
 * Palettes
 *
 * Eingabemaske für die Mitglieder-Verwaltung um drei Felder erweitern, wird als vorletzte Gruppe in DCA eingefügt
 */
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace(
          ';{account_legend},disable,start,stop', 
          ';{attendance_settings_legend},al_inactiveMember;
            {account_legend},disable,start,stop',
          $GLOBALS['TL_DCA']['tl_member']['palettes']['default']
);

/**
 * Fields
 *
 * Zusätzliches Feld für tl_member definieren, Contao erzeugt diese dann automatisch über ein SQL Statement
 * "al_" als Prefix für "Attendance_List", um einzigartige Namen zu erstellen
 */

// Checkbox, um ein Mitglied inaktiv zu setzen
$GLOBALS['TL_DCA']['tl_member']['fields']['al_inactiveMember'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_member']['al_inactiveMember'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'sql'       => "char(1) NOT NULL"
);

/**
 * Class tl_attendanceMember
 *
 * Zusätzliche Methoden, um inaktive Mitglieder aus tl_attendance zu löschen
 *
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */
class tl_attendanceMember extends tl_member {
    /*     * *********************************************************** */

    /**
     * Base Code-Snippet by: Jürgen (https://community.contao.org/de/member.php?332-J%FCrgen) 
     * und Cliffen (https://community.contao.org/de/member.php?4741-cliffen)
     *
     * Thread: https://community.contao.org/de/showthread.php?28127
     *
     * Adapted to this Extension by: Sebastian Buck 2013
     * Funktioniert erst nach Neuladen der Mitgliederübersicht
     */

    /**
     * Overwritten function tl_member.toggleIcon(...)
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes) 
    {
        $this->toggleAttendance($row['disable'], $row['id']);
        return parent::toggleIcon($row, $href, $label, $title, $icon, $attributes);
    }

    private function toggleAttendance($inaktiv, $memberId) {
        if ($inaktiv == 1) {
            $objUser = $this->Database->prepare("DELETE FROM tl_attendance WHERE m_id=?")
                    ->execute($memberId);
        } else {
            // User-ID holen
            $result = Database::getInstance()->prepare('SELECT id FROM tl_member WHERE disable!=? AND al_inactiveMember!=?')->execute(1, 1);
            $members = $result->fetchAllAssoc();

            // Kalender-IDs nur für aktive Kalender holen					
            $objCals = Database::getInstance()->prepare('SELECT al_pickCalendar FROM tl_attendance_lists')->execute();
            $al_cals = $objCals->al_pickCalendar;
            $al_cals = join(',', unserialize($al_cals));

            // Fehler abfangen, wenn kein Anwesenheitsmodul eingerichtet ist
            if ($al_cals) {
                // Termine aus inaktiven Kalendern aus tl_attendance löschen
                $delete = Database::getInstance()
                        ->prepare("
										DELETE FROM tl_attendance 
										WHERE e_id 
										IN(SELECT id FROM tl_calendar_events WHERE pid NOT IN(" . $al_cals . "))")
                        ->execute();

                // Termine aus aktiven Kalendern in tl_attendance holen und übergeben
                $result = Database::getInstance()
                        ->prepare("
										SELECT id 
										FROM tl_calendar_events 
										WHERE pid 
										IN(" . $al_cals . ") 
										ORDER BY id")
                        ->execute();
                $events = $result->fetchAllAssoc();


                // Aktive Mitglieder und Termine in tl_attendance eintragen
                foreach ($members as $member) {
                    $arrNewData['m_id'] = $member['id'];
                    foreach ($events as $event) {
                        $arrNewData['e_id'] = $event['id'];
                        $objData = $this->Database->prepare("INSERT IGNORE INTO tl_attendance %s")->set($arrNewData)->execute();
                    }
                }
            }
        }
    }

    /*     * *********************************************************** */







    /*     * ********************* GEHT ************************************** */

    /**
     * Call the "al_SetInactiveMember" callback
     *
     * Diese Funktion wird beim Absenden des Mitglieder-Formulares im BE aufgerufen.
     * Ist das Mitglied auf inaktiv oder deaktiviert gesetzt, wird es aus tl_attendance gelöscht, sonst hinzugefügt
     * 
     */
    public function al_SetInactiveMember($dc) 
    {
        // Felder al_inactiveMember und disable holen und Werte speichern
        $varInactive = $this->Input->post('al_inactiveMember');
        $varDisable = $this->Input->post('disable');

        // Wenn ein Mitglied inaktiv oder deaktiviert ist, wird es gelöscht
        if ($varInactive || $varDisable) 
        {
            $this->Database->prepare("DELETE FROM tl_attendance WHERE m_id=?")
                    ->execute($dc->id);
        }
        // sonst wird es eingetragen (Änderungen am Aktiv-Status, Neues Mitglied)
        else 
        {
            UpdateAttendance::al_createAttendance("all");
        }
    }

    /**
     * Call the "al_deleteMember" callback
     *
     * Diese Funktion wird beim löschen eines Mitgliedes aufgerufen und löscht dieses Mitglied auch aus tl_attendance
     * 
     */
    public function al_deleteMember($dc) 
    {
        if ($dc instanceof \DataContainer && $dc->activeRecord) 
        {
            \sb_attendanceModel::deleteFromAttendanceTable('m_id', $dc->activeRecord->id);
        }
    }
}