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
 * Config
 *
 * Einstellungen des DCA für tl_module (Moduleinstellungen)
 */ 
 
	/* 
	 * Erweitert das System um einen onsubmit_callback: Ruft beim Absenden des BE-Formulars die 
	 * Funktion al_createAttendance (siehe unten) auf.
	 *
	 */ 
	array_insert($GLOBALS['TL_DCA']['tl_module']['config']['onsubmit_callback'], -1, array
		(
			array('tl_attendanceModule', 'al_createAttendance')
		)
	);
 
 
/**
 * Palettes
 *
 * Eingabemaske für das Frontend-Modul attendance_list erstellen
 */  
$GLOBALS['TL_DCA']['tl_module']['palettes']['attendance_list'] = 
		'
		{title_legend},name,headline,type;
		{attendance_calender_legend},al_pickCalendar;
		{attendance_show_legend},al_expiredEvents,al_expireTime;
		{attendance_statusOptions_legend},al_defaultStatus,al_disableThird;
		{attendance_memberRoles_legend},al_roleAdvice;
		{attendance_style_legend},al_name,al_iconSet,al_useCSS;
		';		
	
/**
 * Fields
 *
 * Zusätzliche Felder für tl_module definieren; Contao erzeugt diese dann automatisch über ein SQL Statement
 * "al_" als Prefix für "Attendance_List", um einzigartige Namen zu erstellen
 */	
 
// Kalenderauswahl
$GLOBALS['TL_DCA']['tl_module']['fields']['al_pickCalendar'] = array 
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_pickCalendar'],
	'inputType'         => 'checkbox',
	'options_callback'  => array('tl_module_calendar', 'getCalendars'),
	'sql'           	=> "blob NULL",
	'eval'				=> array 
		(	
			'mandatory'		=> true,
			'multiple'		=> true
		)	
);



// Ausgewählte Kalender, Feld wird nicht ausgegeben, nur aus al_pickCalendar befüllt
$GLOBALS['TL_DCA']['tl_module']['fields']['al_cals'] = array
(	
	'sql'           	=> "varchar(64) NOT NULL"	
);
 
// Feld zur Einstellung, wie viele abgelaufenen Events angezeigt werden sollen
$GLOBALS['TL_DCA']['tl_module']['fields']['al_expiredEvents'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_expiredEvents'],
	'exclude'    	    => true,
	'inputType'  	    => 'text',	
	'sql'         	    => "int(2) NOT NULL default '0'",
	'eval'				=> array 
		(
			'mandatory' 	=> true,
			'rgxp'			=> 'digit',
			'nospace'		=> true,
			'tl_class'		=> 'w50'
		)
);

// Feld zum Festlegen der Sperrzeit vor einem Termin
$GLOBALS['TL_DCA']['tl_module']['fields']['al_expireTime'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_expireTime'],
	'exclude'       	=> true,
	'inputType'     	=> 'text',	
	'sql'           	=> "int(2) NOT NULL default '0'",
	'eval'				=> array 
		(
			'mandatory' 	=> true,
			'rgxp'			=> 'digit',
			'nospace'		=> true,
			'tl_class'		=> 'w50'
		)
);


// Dritte Option de/aktivieren
$GLOBALS['TL_DCA']['tl_module']['fields']['al_disableThird'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_disableThird'],
	'exclude'       	=> true,
	'inputType'     	=> 'checkbox',	
	'sql'           	=> "varchar(1) NOT NULL default ''",
	'eval'				=> array 
		(
			'tl_class'		=> 'w50 m12'
		)	
);


 
// Standard-Status
$GLOBALS['TL_DCA']['tl_module']['fields']['al_defaultStatus'] = array 
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_defaultStatus'],
	'inputType'         => 'radio',	
	'sql'           	=> "varchar(1) NOT NULL default '0'",
	'options'           => array('0', '1', '2', '3'),
	'reference'         => &$GLOBALS['TL_LANG']['tl_module']['al_radio'],
	'explanation'		=> 'al_defaultStatus',
	'eval'				=> array 
		(				
			'helpwizard'		=> true,
			'tl_class'		=> 'm12 w50 clr'
		),
	
);


// Hinweisfeld auf Mitgliedereinstellungen (Admin, Trainer, inaktiv)
$GLOBALS['TL_DCA']['tl_module']['fields']['al_roleAdvice'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_roleAdvice'],
	'exclude'       	=> true,
	'inputType'         => 'text',
	'sql'           	=> "varchar(200) NOT NULL default 'Hinweis: Die Vergabe von Mitgliederrollen kann in der Mitgliederverwaltung vorgenommen werden.'",
	'eval'				=> array 
		(							
				'disabled'	=> true,
				'tl_class'	=> 'tl_info tl_info_fix'			
		)
);
 
// Darstellung des Namen
$GLOBALS['TL_DCA']['tl_module']['fields']['al_name'] = array 
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_name'],
	'inputType'         => 'radio',	
	'sql'           	=> "varchar(10) NOT NULL default 'username'",
	'options'           => array('username', 'firstname', 'lastname', 'first_last'),
	'reference'         => &$GLOBALS['TL_LANG']['tl_module']['al_name']	
);

 
// Icon-Set-Auswahl
$GLOBALS['TL_DCA']['tl_module']['fields']['al_iconSet'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_iconSet'],
	'exclude'       	=> true,
	'inputType'     	=> 'select',
	'options'           => array('flat_thick', 'flat_thick_alternative', 'flat_thin'),
	'reference'         => &$GLOBALS['TL_LANG']['tl_module'],	
	'eval'              => array('tl_class'=>'w50 al_optionSet'),
	'sql'               => "varchar(32) NOT NULL default ''"
);

// Auswahl, ob CSS verwendet werden soll
$GLOBALS['TL_DCA']['tl_module']['fields']['al_useCSS'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_module']['al_useCSS'],
	'exclude'       	=> true,
	'inputType'     	=> 'checkbox',	
	'sql'           	=> "varchar(1) NOT NULL default ''",
	'eval'				=> array 
		(
			'tl_class'		=> 'w50 m12'
		)	
);
 



/**
 * Class tl_attendanceModule
 *
 * Zusätzliche Methoden, um beim Erstellen/Ändern des Moduls, die Tabelle zu aktualisieren
 *
 * @copyright  Sebastian Buck 2013
 * @author     Sebastian Buck
 * @package    Attendance
 */
class tl_attendanceModule extends tl_module
{
	 /**
	 * Call the "al_createAttendance" callback
	 *
	 * Diese Funktion wird beim Speichern des Anwesenheitsliste-Moduls gerufen und befüllt die Tabelle mit den aktiven Mitgliedern
	 * Inaktive und deaktivierte Mitglieder werden zuerst aus tl_attendance gelöscht
	 */ 

	public function al_createAttendance($dc)
	{	
		if ($this->Input->post('al_pickCalendar'))  // nur Ausführen, wenn al_pickCalendars gesetzt wurde. 
													// nur der Fall wenn das Anwesenheitsmodul gespeichert wird
		{				
			/*			 
			 * Inaktive Mitglieder aus tl_attendance löschen
			 */
			$del = Database::getInstance()->query('SELECT id FROM tl_member WHERE disable=1 OR al_inactiveMember=1');
			$dels = $del->fetchAllAssoc();
			
			foreach ($dels as $delete)
			{			
				$objData = $this->Database->prepare("DELETE FROM tl_attendance WHERE m_id=?")->execute($delete['id']);
			}
			
			/*			 
			 * ausgewählte Kalender-IDs in die Datenbank speichern
			 */		
			$saveCals = Database::getInstance()
					->prepare('UPDATE tl_module SET al_cals = ? WHERE id=?')
					->execute(implode(',',($this->Input->post('al_pickCalendar'))),$dc->id);
						
			/*			 
			 * Nur Events aus ausgewählten Kalendern eintragen, andere löschen
			 */
			$objCals = Database::getInstance()->prepare('SELECT al_cals FROM tl_module WHERE id=?')->execute($dc->id);
			$al_cals = $objCals->al_cals;
			
			if($al_cals)
			{	
				// Events aus Kalendern, die nicht ausgewählt sind, aus tl_attendance löschen
				$delete = Database::getInstance()
					->prepare("
							DELETE FROM tl_attendance 
							WHERE e_id 
							IN(SELECT id FROM tl_calendar_events WHERE published<1 OR pid NOT IN(".$al_cals."))")					
					->execute();
			
				// Event-IDs für veröffentlichte Termine aus ausgesuchten Kalendern suchen und speichern
				$result = Database::getInstance()
					->prepare("
							SELECT id 
							FROM tl_calendar_events 
							WHERE pid 
							IN(".$al_cals.") AND published=1 
							ORDER BY id")					
					->execute();
				$events = $result->fetchAllAssoc();				
			}
			
			// User-IDs von aktiven Mitgliedern holen und speichern
			$result = Database::getInstance()->query('SELECT id FROM tl_member WHERE disable!=1 AND al_inactiveMember!=1');
			$members = $result->fetchAllAssoc();
			
			// tl_attendance aus User-IDs und Kalender-IDs erstellen/aktualisieren
			foreach ($members as $member)
			{
				$arrNewData['m_id'] = $member['id'];
				foreach ($events as $event)
				{
					$arrNewData['e_id'] = $event['id'];
					$objData = $this->Database->prepare("INSERT IGNORE INTO tl_attendance %s")->set($arrNewData)->execute();
				}			
			}
			
			// standard Status setzen für alle Felder, die noch nicht geändert wurden (tstamp=0)			
			$defaultStatus = $this->Input->post('al_defaultStatus');
			
			$changeStatus = Database::getInstance()
					->prepare('UPDATE tl_attendance SET attendance=? WHERE tstamp=? ')
					->execute($defaultStatus,0);	
			
			
		}					
	}	
}




