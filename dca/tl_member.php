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
 * Erweitert den Toggler der Mitglieder um eine toggleIcon-Funktion (Aktualisiert tl_attendance)
 */
$GLOBALS['TL_DCA']['tl_member']['list']['operations']['toggle']['button_callback'] = array 
    (        
        'tl_attendanceMember', 'toggleIcon'
    );

$GLOBALS['TL_DCA']['tl_member']['list']['operations']['toggle']['attributes'] = array 
    (        
        'onclick="Backend.getScrollOffset();return AjaxRequest.toggleAttendance(this,%s)"'
    );

 /**
 * Fügt den Mitgliedern einen zusätzlichen toggler für inaktive Mitglieder hinzu
 */  

array_insert($GLOBALS['TL_DCA']['tl_member']['list']['operations'],4, array 
    (
        'toggleInactiveMembers' => array
        (
            'label'               => &$GLOBALS['TL_LANG']['tl_member']['toggleInactiveMembers'],
            'icon'                => 'system/modules/sb_attendance/assets/icons/activeMember.png',
            'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleInactiveMember(this,%s)"',
            'button_callback'     => array('tl_attendanceMember', 'toggleInactiveMemberIcon')
        )
    ));

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
class tl_attendanceMember extends tl_member 
{
    /*
     * Funktionen für das Standard-toggler-Feature der Mitglieder 
     * (Erweiterung der Standardfunktionen in tl_member)
     */
    
    /**
    * Base Code-Snippet by: Jürgen (https://community.contao.org/de/member.php?332-J%FCrgen) 
    * and Cliffen (https://community.contao.org/de/member.php?4741-cliffen)
    *
    * Thread: https://community.contao.org/de/showthread.php?28127
    *
    * Adapted to this Extension by: Sebastian Buck 2014 
    */
    
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes) 
    {        
        $this->toggleAttendance($row['disable'], $row['id']);        
        return parent::toggleIcon($row, $href, $label, $title, $icon, $attributes);
    }

    private function toggleAttendance($inaktiv, $memberId) 
    {   
        if ($inaktiv == 1) 
        {
            \sb_attendanceModel::deleteFromAttendanceTable('m_id', $memberId);
        } 
        else 
        {   
            UpdateAttendance::al_createAttendance("all");                   
        }
    }
    
    /*
     * Funktionen für das toggler-Feature für inaktive Mitglieder
     */
    
    // Funktion zum togglen des inactiveMember-Icons und Aufruf der Funktion zur Datenbankaktualisierung
    public function toggleInactiveMemberIcon($row, $href, $label, $title, $icon, $attributes)
    {           
        if (strlen($this->Input->get('taid')))
        {            
            $this->toggleInactiveMember($this->Input->get('taid'), ($this->Input->get('state')));            
            $this->redirect($this->getReferer());
        }
 
        $href .= '&amp;taid='.$row['id'].'&amp;state='.$row['al_inactiveMember'];
 
        if ($row['al_inactiveMember'])
        {
            $icon = 'system/modules/sb_attendance/assets/icons/inactiveMember.png';
        }        
 
        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
    }
    
    // Funktion, um ein Mitglied inaktiv zu setzen und die Anwesenheitsdatenbank zu aktualisieren
    private function toggleInactiveMember($intId, $blnInactive)
    {   
        // Update the database
        $this->Database->prepare("UPDATE tl_member SET tstamp=". time() .", al_inactiveMember='" . ($blnInactive ? '' : '1') . "' WHERE id=?")
            ->execute($intId);
        
        \sb_attendanceModel::deleteFromAttendanceTable('m_id', $intId);        
    }    
  
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
            \sb_attendanceModel::deleteFromAttendanceTable('m_id', $dc->activeRecord->id);
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