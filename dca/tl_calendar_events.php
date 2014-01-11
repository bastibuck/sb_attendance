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
 * Einstellungen des DCA für tl_calendar_events (Terminverwaltung)
 */ 
 
	/* 
	 * Erweitert das System um einen ondelete_callback: Ruft beim Löschen eines Termins die 
	 * Funktion al_deleteEvent (siehe unten) auf.
	 *
	 */ 
	array_insert($GLOBALS['TL_DCA']['tl_calendar_events']['config']['ondelete_callback'], -1, array
		(
			array('tl_attendanceEvents', 'al_deleteEvent')
		)
	);
	
	/* 
	 * Erweitert das System um einen onsubmit_callback: Ruft beim Absenden des BE-Formulars die 
	 * Funktion al_SetEvent (siehe unten) auf.
	 *
	 */ 
	array_insert($GLOBALS['TL_DCA']['tl_calendar_events']['config']['onsubmit_callback'], -1, array
		(
			array('tl_attendanceEvents', 'al_SetEvent')
		)
	);
 
 

/**
 * Class tl_attendanceEvents
 *
 * Zusätzliche Methode, um beim Erstellen des Moduls, die Tabelle zu befüllen
 *
 * @copyright  Sebastian Buck 2013
 * @author     Sebastian Buck
 * @package    Attendance
 */
class tl_attendanceEvents extends tl_calendar_events
{
	/**
	 * Call the "al_SetEvent" callback
	 *
	 * Diese Funktion wird gerufen, wenn das BE-Formular gesendet wird, und aktualisiert tl_attendance
	 */ 
	public function al_SetEvent($dc)
	{	
		// Veröffentlichungsstatus holen und speichern
		$eventPub = $this->Input->post('published');
		
		// Wenn das Event veröffentlich ist
		if($eventPub==1)
		{
			// aktive User-IDs holen
			$result = Database::getInstance()->prepare('SELECT id FROM tl_member WHERE disable!=? AND al_inactiveMember!=?')->execute(1,1);
			$members = $result->fetchAllAssoc();
			
			// Kalender-IDs nur für aktive Kalender holen					
			$objCals = Database::getInstance()->prepare('SELECT al_cals FROM tl_module WHERE type=?')->execute(attendance_list);
			$al_cals = $objCals->al_cals;
			
			// Fehler abfangen, wenn kein Anwesenheitsmodul eingerichtet ist
			if ($al_cals)
			{		
				// Termine aus aktiven Kalendern in tl_attendance holen und übergeben
				$result = Database::getInstance()
						->prepare("
								SELECT id 
								FROM tl_calendar_events 
								WHERE id=? AND pid IN (".$al_cals.")
								")
						->execute($dc->id);		
					$events = $result->fetchAllAssoc();			
				
				
				// Aktive Mitglieder und Termine in tl_attendance eintragen
				foreach ($members as $member)
				{
					$arrNewData['m_id'] = $member['id'];
					foreach ($events as $event)
					{
						$arrNewData['e_id'] = $event['id'];
						$objData = $this->Database->prepare("INSERT IGNORE INTO tl_attendance %s")->set($arrNewData)->execute();
					}			
				}	
			}
		}
		// sonst (wenn das Event also nicht veröffentlicht ist)
		else
		{
			// Event wird aus tl_attendance gelöscht
			$delete = Database::getInstance()
					->prepare("
							DELETE FROM tl_attendance 
							WHERE e_id=?
							")
					->execute($dc->id);	
		}
	}

	/**
	* Call the "al_deleteEvent" callback
	*
	* Diese Funktion wird gerufen, wenn ein Event gelöscht wird, und löscht das entsprechende Event aus tl_attendance
	*/ 
	public function al_deleteEvent($dc)
	{	
		if ($dc instanceof \DataContainer && $dc->activeRecord)
		{
			$this->Database->prepare("DELETE FROM tl_attendance WHERE e_id=?")
						   ->execute($dc->activeRecord->id);
		}					
	}	
}




