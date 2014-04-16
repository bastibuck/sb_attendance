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
 * Table tl_attendance_lists
 */
$GLOBALS['TL_DCA']['tl_attendance_lists'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'     => 'Table',        
        'onsubmit_callback' => array
        (
            array('UpdateAttendance', 'al_createAttendance'),
            array('RemoveMemberRoles', 'removeRole')
        ),
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        ),
    ),
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'      => 1,
            'fields'    => array('title'),
            'flag'      => 1
        ),
        'label' => array
        (
            'fields'         => array('title'),            
            'label_callback' => array('tl_attendance_label', 'attendanceLabel')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_attendance_lists']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_attendance_lists']['delete'],
                'href'  => 'act=delete',
                'icon'  => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_attendance_lists']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
                'attributes' => 'style="margin-right:3px"'
            ),
        )
    ),
    // Palettes
    'palettes' => array
    (
        '__selector__'  => array('al_checkCoach','al_checkCaptain','al_checkAdmin'),
        'default' => '  {title_legend},title;
                        {attendance_calender_legend},al_pickCalendar;
                        {attendance_member_legend},al_pickMembers;                        
                        {attendance_statusOptions_legend},al_defaultStatus,al_disableThird,al_askReason,al_expireTime;
                        {attendance_memberRoles_legend},al_checkCoach,al_checkCaptain,al_checkAdmin,al_roleAdvice;
                        {attendance_descriptions:hide},al_CoachDescription,al_CaptainDescription,al_AttendantsDescription;
                        {attendance_style_legend},al_expiredEvents,al_eventsPerPage,al_name,al_iconSet,al_useCSS,al_showInfos;
                     '
    ),
    	// Subpalettes
    'subpalettes' => array
    (
        'al_checkCoach'     => 'al_Coach',
        'al_checkCaptain'   => 'al_Captain',
        'al_checkAdmin'     => 'al_Admin'
    ),
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['title'],
            'inputType' => 'text',
            'exclude'   => true,
            'sorting'   => true,
            'flag'      => 1,
            'sql'       => "varchar(255) NOT NULL default ''",
            'eval'      => array
            (
                'mandatory' => true,
                'unique'    => true,
                'maxlength' => 255,
                'tl_class'  => 'w50'
            )
        ),
        'al_pickCalendar' => array
        (
            'label'      => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickCalendar'],
            'inputType'  => 'checkbox',
            'foreignKey' => 'tl_calendar.title',
            'sql'        => "blob NULL",
            'eval'       => array
            (
                'mandatory' => true,
                'multiple'  => true
            )            
        ),
        'al_pickMembers' => array
        (
            'label'      => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickMembers'],
            'inputType'  => 'checkbox',
            'foreignKey' => 'tl_member_group.name',            
            'sql'        => "blob NULL",
            'eval'       => array
            (
                'mandatory'      => true,
                'submitOnChange' => true,
                'multiple'       => true
            )
        ),
        'al_expiredEvents' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expiredEvents'],
            'exclude'   => true,
            'inputType' => 'text',
            'sql'       => "int(2) NOT NULL default '0'",
            'eval'      => array
            (
                'mandatory' => true,
                'rgxp'      => 'digit',
                'nospace'   => true,
                'tl_class'  => 'm12 w50'
            )
        ),
        'al_expireTime' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_expireTime'],
            'exclude'   => true,
            'inputType' => 'text',
            'sql'       => "int(2) NOT NULL default '0'",
            'eval'      => array
            (
                'mandatory' => true,
                'rgxp'      => 'digit',
                'nospace'   => true,
                'tl_class'  => 'w50'
            )
        ),
        'al_eventsPerPage' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_eventsPerPage'],
            'exclude'   => true,
            'inputType' => 'text',
            'sql'       => "int(2) NOT NULL default '5'",
            'eval'      => array
            (
                'mandatory' => true,
                'rgxp'      => 'digit',
                'nospace'   => true,
                'tl_class'  => 'm12 w50'
            )
        ),
        'al_disableThird' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_disableThird'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "varchar(1) NOT NULL default ''",
            'eval'      => array
            (
                'tl_class' => 'w50 m12'
            )
        ),
        'al_askReason' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_askReason'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "varchar(1) NOT NULL default ''",
            'eval'      => array
            (
                'tl_class' => 'w50 m12'
            )
        ),
        'al_defaultStatus' => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_defaultStatus'],
            'inputType'     => 'radio',
            'sql'           => "varchar(1) NOT NULL default '0'",
            'options'       => array('0', '1', '2', '3'),
            'reference'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_radio'],
            'explanation'   => 'al_defaultStatus',
            'eval'          => array
            (
                'helpwizard' => true,
                'tl_class'   => 'm12 w50 clr'
            )
        ),
        'al_checkCoach' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCoach'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "varchar(1) NOT NULL default ''",
            'eval'      => array
            (
                'submitOnChange' => true,
                'tl_class' => 'm12 clr'
            )
        ),
        'al_Coach' => array
        (
            'label'             => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Coach'],
            'exclude'           => true,
            'inputType'         => 'radio',
            'options_callback'  => array ('tl_attendance_label', 'groupMembers'),                    
            'sql'               => "varchar(10) NOT NULL default ''",
            'eval'              => array
            (
                'tl_class' => 'm12 clr',
                'mandatory' => true
            )
        ),
        'al_checkCaptain' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkCaptain'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "varchar(1) NOT NULL default ''",
            'eval'      => array
            (
                'submitOnChange' => true,
                'tl_class' => 'm12 clr'
            )
        ),
        'al_Captain' => array
        (
            'label'             => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Captain'],
            'exclude'           => true,
            'inputType'         => 'radio',
            'options_callback'  => array ('tl_attendance_label', 'groupMembers'),                    
            'sql'               => "varchar(10) NOT NULL default ''",
            'eval'              => array
            (
                'tl_class' => 'm12 clr',
                'mandatory' => true
            )
        ),
        'al_checkAdmin' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_checkAdmin'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "varchar(1) NOT NULL default ''",
            'eval'      => array
            (
                'submitOnChange' => true,
                'tl_class' => 'm12 clr'
            )
        ),
        'al_Admin' => array
        (
            'label'             => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_Admin'],
            'exclude'           => true,
            'inputType'         => 'radio',
            'options_callback'  => array ('tl_attendance_label', 'groupMembers'),                    
            'sql'               => "varchar(10) NOT NULL default ''",
            'eval'              => array
            (
                'tl_class' => 'm12 clr',
                'mandatory' => true
            )
        ),
        'al_roleAdvice' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_roleAdvice'],
            'exclude'   => true,
            'inputType' => 'text',
            'sql'       => "varchar(1) NULL",
            'eval'      => array
            (
                'disabled' => true,
                'tl_class' => 'tl_info tl_info_fix clr'
            )
        ),
        'al_CoachDescription' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CoachDescription'],
            'inputType' => 'text',
            'exclude'   => true,                        
            'sql'       => "varchar(255) NOT NULL default ''",
            'eval'      => array
            (                
                'maxlength' => 255
            )
        ),
        'al_CaptainDescription' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_CaptainDescription'],
            'inputType' => 'text',
            'exclude'   => true,                        
            'sql'       => "varchar(255) NOT NULL default ''",
            'eval'      => array
            (                
                'maxlength' => 255
            )
        ),
        'al_AttendantsDescription' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_AttendantsDescription'],
            'inputType' => 'text',
            'exclude'   => true,                        
            'sql'       => "varchar(255) NOT NULL default ''",
            'eval'      => array
            (                
                'maxlength' => 255
            )
        ),
        'al_name' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name'],
            'inputType' => 'radio',
            'sql'       => "varchar(10) NOT NULL default 'username'",
            'options'   => array('username', 'firstname', 'lastname', 'first_last'),
            'reference' => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_name'],
            'eval'      => array
            (                
                'tl_class' => 'm12 clr'
            )
        ),
        'al_iconSet' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_iconSet'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('flat_thick', 'flat_thick_alternative', 'flat_thin'),
            'reference' => &$GLOBALS['TL_LANG']['tl_attendance_lists'],            
            'sql'       => "varchar(32) NOT NULL default ''",
            'eval'      => array
            (
                'tl_class' => 'al_optionSet'
            )
        ),
        'al_useCSS' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_useCSS'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "varchar(1) NOT NULL default ''",
            'eval'      => array
            (
                'tl_class' => 'm12'
            )
        ),
        'al_showInfos' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_showInfos'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "varchar(1) NOT NULL default ''",
            'eval'      => array
            (
                'tl_class' => 'm12'                
            )
        )
    )
);

/**
 * Class tl_attendance_label
 *
 * Zusätzliche Methoden, um den Anwesenheitslisten individuelle Label zu geben
 *
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */
class tl_attendance_label extends Backend 
{
    // Funktion liefert Beschriftung der Anwesenheitslisten in der Übersicht zurück
    public function attendanceLabel($arrRow) 
    {
        // Kalender holen
        $cals = unserialize($arrRow['al_pickCalendar']);

        $calendars = array();
        foreach ($cals as $cal) 
        {
            $objParent = $this->Database
                            ->prepare("SELECT title FROM tl_calendar WHERE id=?")
                            ->execute($cal);
            array_push($calendars, $objParent->title);
        }
        $calendars = join(", ", $calendars);

        // Mitglieder-Gruppen holen
        $members = unserialize($arrRow['al_pickMembers']);

        $memberGroups = array();
        foreach ($members as $member) 
        {
            $objParent = $this->Database
                            ->prepare("SELECT name FROM tl_member_group WHERE id=?")
                            ->execute($member);
            array_push($memberGroups, $objParent->name);
        }
        $memberGroups = join(", ", $memberGroups);

        $strCalendars = &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickCalendar'][0];
        $strMemberGroups = &$GLOBALS['TL_LANG']['tl_attendance_lists']['al_pickMembers'][0];

        $label = "<h1>" . $arrRow['title'] . "</h1>";
        $label .= "<span style='color:#b3b3b3;padding-left:5px'>[" . $strCalendars . ": " . $calendars . "]</span><br>";
        $label .= "<span style='color:#b3b3b3;padding-left:5px'>[" . $strMemberGroups . ": " . $memberGroups . "]<br><br></span>";

        return $label;
    }
    
    // Funktion liefert Namen für Trainer/Kapitän zurück
    public function groupMembers($arrRow) 
    {       
        //Ausgewählte Gruppen laden
        $al_mems = unserialize(\sb_attendanceModel::findSettings($arrRow->id, 'al_pickMembers'));            
        // Mitglieder laden
        $objMembers = $this->Database->prepare("SELECT id,firstname,lastname,groups FROM tl_member")
                ->execute();

        $arrMembers = array();
        // Mitglieder in den ausgewählten Gruppen ins Array schreiben
        while ($objMembers->next())
        {
            $groups = deserialize($objMembers->groups);
            foreach ($groups as $group)
            {
                if (in_array($group, $al_mems))
                {
                    $arrMembers[$objMembers->id] = $objMembers->firstname . ' ' . $objMembers->lastname;
                }
            }
        }
        
        return $arrMembers;         
    }
}

/**
 * Class RemoveMemberRoles
 *
 * Zusätzliche Methode, um gesetzte Mitgliederrollen zu entfernen, 
 * wenn das entsprechende Häkchen entfernt wurde
 *
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */
class RemoveMemberRoles extends Backend 
{    
    public function removeRole($arrRow) 
    {
        $id = $arrRow->id;
        
        if (!$this->Input->post('al_checkCoach'))
        {
            \sb_attendanceModel::removeRole('al_Coach', $id);
        }  
        
        if (!$this->Input->post('al_checkCaptain'))
        {
            \sb_attendanceModel::removeRole('al_Captain', $id);
        } 
        
        if (!$this->Input->post('al_checkAdmin'))
        {
            \sb_attendanceModel::removeRole('al_Admin', $id);
        } 
    }
}
