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
 * Class ModuleAttendanceList
 *
 * @copyright  Sebastian Buck 2013
 * @author     Sebastian Buck
 * @package    Attendance
 */
class ModuleAttendanceList extends \Module
{
	/**
	 * Template
	 * @var string
	 *
	 * Das zugehörige Template (Ausgabe im Frontend) wird festgelegt
	 *
	 */
	protected $strTemplate = 'mod_attendance_list';
	
	
	// Funktion, um die einzelnen Status Felder zu erzeugen, abhängig von den Nutzerrechten und Ablaufstatus eines Termins
	private function createStatusField($att, $intReiheID, $intTerminID, $flagExpired, $flagEdit, $iconSetPath, $name, $tstamp)
	{		
		$attendanceName = array('unknown', 'yes', 'no', 'later');
		
		// Änderungszeitpunkt zusammensetzen
		if ($tstamp!=0)
		{		
			$timeChanged = " (";
			$timeChanged .= date($GLOBALS['TL_CONFIG']['datimFormat'],$tstamp);			
			$timeChanged .= $GLOBALS['TL_LANG']['al_frontend']['time'];
			$timeChanged .= ")";
		}
		else
		{
			$timeChanged='';
		}
		
		// Alt- und Title-Attribut Wert zusammensetzen
		$strAltTitleTag = $name;
		$strAltTitleTag .= ": ";
		$strAltTitleTag .= $GLOBALS['TL_LANG']['al_frontend'][$attendanceName[$att]];
		$strAltTitleTag.= $timeChanged;
		
		
		// Feld aufbauen
		$strStatusField = "<td";
			// abgelaufene Termine kennzeichnen
			if ($flagExpired)
				{
					$strStatusField .= " class='expired'";
					$strExpired = "_expired";
				}
			
			// Bearbeitbare Felder erzeugen
			if($flagEdit)
			{
				$strStatusField .= "><form action='".Environment::get('requestUri')."' method='POST'>
										<input type='hidden' name='REQUEST_TOKEN' value='{{request_token}}'>
										<input 
											type='image' 
											src='system/modules/sb_attendance/assets/img/".$iconSetPath."/".$attendanceName[$att].$strExpired.".png'
											alt='".$strAltTitleTag."'
											title='".$strAltTitleTag."'
										>
										<input type='hidden' value='".$att."' name='status'>											
										<input type='hidden' value='".$intReiheID."' name='m_id'>
										<input type='hidden' value='".$intTerminID."' name='e_id'>
									</form></td>";			
			}
			// nicht bearbeitbare Felder erzeugen
			else
			{
				$strStatusField .= "><img
											src='system/modules/sb_attendance/assets/img/".$iconSetPath."/".$attendanceName[$att].$strExpired.".png'
											alt='".$strAltTitleTag."'
											title='".$strAltTitleTag."'
										>
									</td>";	
			}
			
		// Statusfeld zurückgeben
		return $strStatusField;
	}

	
	
	/**
	 * Generate the module
	 */
	protected function compile()
	{	
		// Modul-ID laden
		$moduleID = $this->id;
		
		/**
		 * Daten aus Datenbank laden
		 */
		 
			// Namendarstellung laden
			$strNameSetting = \sb_attendanceSettingsModel::findSettings($moduleID, 'al_name');			
			
			// ausgewähltes IconSet laden
			$iconSetPath = \sb_attendanceSettingsModel::findSettings($moduleID, 'al_iconSet');
			$iconSetPath = $iconSetPath .'_icon_set';
			
			// optionales CSS - Daten laden			
			$useCSS = \sb_attendanceSettingsModel::findSettings($moduleID, 'al_useCSS');
			
			
			// dritte Option-Status laden
			$flagDisableThird = \sb_attendanceSettingsModel::findSettings($moduleID, 'al_disableThird');
			
			// Anzahl abgelaufener Termine
			$intExpiredEvents = \sb_attendanceSettingsModel::findSettings($moduleID, 'al_expiredEvents');		
			
			// Sperrzeit laden
			$expireTime = \sb_attendanceSettingsModel::findSettings($moduleID, 'al_expireTime');					
			$expireTime = $expireTime * 3600;
			
			
			
			
			if ($strNameSetting=="first_last")
			{
				// String für DB Abfrage anpassen, wenn Vor- und Nachname ausgegeben werden sollen
				$strNameSetting = "firstname, t1.lastname";
				$strNameSort = "t1.firstname, t1.lastname";
				$intFirst_Last = 1;
			}
			else
			{
				$strNameSort = $strNameSetting;
			}
			
			
			// aktive SpielerIDs aus tl_attendance holen		
			$result = Database::getInstance()
				->prepare('
					SELECT DISTINCT t1.'.$strNameSetting.', t1.id 
					FROM tl_member t1 
					JOIN tl_attendance t2 
					ON (t2.m_id = t1.id) 
					ORDER BY '.$strNameSort.'')
				->execute();	
			$arraySpieler = $result->fetchAllAssoc();

			// aktive TerminIDs aus tl_attendance holen
			$result = Database::getInstance()
				->prepare('
					SELECT DISTINCT t1.id,t1.title,t1.startDate,t1.startTime 
					FROM tl_calendar_events t1 
					JOIN tl_attendance t2 
					ON (t2.e_id = t1.id) 
					ORDER BY t1.startTime')
				->execute();		
			$arrayTermine = $result->fetchAllAssoc();				
			
			
			// Trainer suchen			
			$result = Database::getInstance()
				->prepare('SELECT id FROM tl_member WHERE al_coachRole=?')
				->execute(1);		
			$intCoachID = $result->id;	
			
			// Kapitän suchen
			$result = Database::getInstance()
				->prepare('SELECT id FROM tl_member WHERE al_Captain=?')
				->execute(1);		
			$intCaptainID = $result->id;			
						
			// Admin suchen
			$result = Database::getInstance()
				->prepare('SELECT id FROM tl_member WHERE al_adminRole=?')
				->execute(1);		
			$intAdminID = $result->id;		
			
			
			$test = "<h3>Einstellungen</h3><ul>";
			$test .= "<li>Modul-ID: ".$moduleID."</li>";
			$test .= "<li>Namen: ".$strNameSetting."</li>";
			$test .= "<li>Iconset: ".$iconSetPath."</li>";
			$test .= "<li>CSS: ".$useCSS."</li>";
			$test .= "<li>dritte Option: ".$flagDisableThird."</li>";
			$test .= "<li>Ablaufzeit: ".$expireTime."</li>";
			$test .= "<li>Abgelaufene: ".$intExpiredEvents."</li>";
			$test .= "<li>Standardstatus: ".$this->al_defaultStatus."</li>";
			$test .= "<li>Kalender: ".$this->al_cals."</li>";
			
			//$this->Template->test = $test;
			
			
			
		/**
		 * ENDE: Daten aus Datenbank laden
		 */	
		
		 
		 /**
		 * Statusänderung
		 */
			// Überprüfen, ob POST-Variablen gesetzt sind, also ein Status geändert werden sollte
			if ($this->Input->post('m_id'))
			{
				// Variablen mit POST-Werten belegen
				$int_POST_memberID = $this->Input->post('m_id');
				$int_POST_eventID = $this->Input->post('e_id');
				$int_POST_status = $this->Input->post('status');
				
				$oldStatus = Database::getInstance()
						->prepare('SELECT attendance FROM tl_attendance WHERE m_id=? AND e_id=?')
						->execute($int_POST_memberID,$int_POST_eventID);
						
				if($oldStatus->attendance == $int_POST_status)
				{
					// Statusänderung
					switch ($int_POST_status) 
						{
							case 0:
								$int_POST_status = 1;							
								break;
							case 1:
								$int_POST_status = 2;							
								break;
							case 2:
								// Wenn 'Dritte Option deaktivieren' gesetzt wurde, direkt wieder Status 1 setzen
								if ($flagDisableThird==1)
								{
									$int_POST_status = 1;
								}
								else
								{
									$int_POST_status = 3;
								}
								break;
							case 3:
								$int_POST_status = 1;							
								break;
						}
					
					// akteulle Uhrzeit als Zeitstempel
					$time = time();
					
					// Eintragen in DB
					$changeStatus = Database::getInstance()
							->prepare('UPDATE tl_attendance SET attendance=?,tstamp=? WHERE m_id=? AND e_id=?')
							->execute($int_POST_status,$time,$int_POST_memberID,$int_POST_eventID);
				}						
			}		
		 
		/**
		 * Eingeloggten Nutzer Laden
		 */
			$this->import('FrontendUser');
			$intLoggedUserID = $this->FrontendUser->id;
			
			// Abfangen von Bearbeitungsmöglichkeit bei keinem eingeloggten Nutzer
			if(!$intLoggedUserID)
			{
				$intLoggedUserID = 'kein eingelogter Nutzer';
				$this->Template->noUser = $GLOBALS['TL_LANG']['al_frontend']['noUser'];
			}
		
			
		/**
		 * CSS einbinden
		 */
		 
			// Einbinden der mitgelieferten CSS-Anweisungen, wenn die Option gesetzt wurde
			if($useCSS==1)
			{
				if (TL_MODE == 'FE')
				{
					$GLOBALS['TL_CSS']['al_css'] = 'system/modules/sb_attendance/assets/css/al_style.css';
				}
			}
		
		/**
		 * Daten verarbeiten
		 */
		 
			/**
			 * Zukünftige und abgelaufene Events trennen
			 */
				$future = array();
				$expired = array();
				
				foreach ($arrayTermine as $termin)
				{
					if (($termin['startTime']-$expireTime) < time())
					{
						array_push($expired, $termin);
					}
					else
					{
						array_push($future, $termin);
					}
				}	
			
				// Fehler abfangen, wenn Anzahl anzuzeigener, abgelaufener Events 
				// größer ist, als die Anzahl der abgelaufenen Events
				if($intExpiredEvents>sizeof($expired))
				{
					$intExpiredEvents = sizeof($expired);
				}			
			
				// Anzahl der abgelaufenen Events zu dem Array der kommenden Events hinzufügen
				$i = 1;			
				while ($i<=$intExpiredEvents)
				{	
					$last = array_pop($expired);				
					array_unshift($future, $last);
					
					$i++;
				}			
				$arrayTermine = $future;			
		 
			/**
			 * Spaltenüberschriften bilden
			 */
			 
				// Überschriften-Array definieren
				$headings = array();
				
				$i=1;				
				// Überschriften-Array mit den Titeln der Events befüllen
				foreach ($arrayTermine as $termin)
				{					
					// Abgelaufene Events
					$strTitle = "<td";
					if ($i<=$intExpiredEvents)
					{
						$strTitle .= " class='expired'";
					}
					$strTitle .= "><p class='al_title'>".$termin['title']."</p>";					
					$termin['title'] = $strTitle;
					
									
					// Datumsformate berücksichtigen (Umwandeln in menschenlesbar)
					if ($termin['startTime']!=$termin['startDate'])
					{
						$termin['startTime'] = "<br>".date($GLOBALS['TL_CONFIG']['timeFormat'],$termin['startTime']).$GLOBALS['TL_LANG']['al_frontend']['time']."</p>";
					}
					else
					{
						$termin['startTime'] = '</p>';
					}	
					
					$termin['startDate'] = "<p class='al_date'>".date($GLOBALS['TL_CONFIG']['dateFormat'],$termin['startDate']);
					
					
					// aktive Spieleranzahl holen (abhängig ob ein Trainer definiert ist oder nicht)					
					if ($intCoachID)
					{
						$result = Database::getInstance()
							->prepare('SELECT id FROM tl_attendance WHERE e_id=? AND (attendance=? OR attendance=?) AND m_id!=?')
							->execute($termin['id'],1,3,$intCoachID);
					}
					else
					{
						$result = Database::getInstance()
							->prepare('SELECT id FROM tl_attendance WHERE e_id=? AND (attendance=? OR attendance=?)')
							->execute($termin['id'],1,3);
					}		
							
					$number = "<p>".$GLOBALS['TL_LANG']['al_frontend']['attendants'].":<br>".$result->count()."</p></td>";

					$termin['summe']=$number;
					
					// Überschriften-Array erzeugen
					array_push($headings, $termin);
					
					$i++;			
				}
				
			
			/**
			 * Zeilen erzeugen
			 */

			// Trainer an erste Stelle im Array sortieren
			$i=1;
			foreach ($arraySpieler as $trainer)
			{
				if($trainer['id']==$intCoachID)
				{
					array_unshift($arraySpieler, $trainer);
					unset($arraySpieler[$i]);
				}
				$i++;
			}
			
			// Pro Spieler eine Reihe erzeugen
			foreach ($arraySpieler as $reihe)
			{				
				// Variablen definieren 
				if ($intFirst_Last==1)
				{
					$name = $reihe['firstname'] . " " . $reihe['lastname'];
					$strNameSetting = 'firstname';
				}
				else
				{
					$name = $reihe[''.$strNameSetting.''];
				}
				
				$stati = array ();
				
				$i=1;
				// Termine durchlaufen
				foreach ($arrayTermine as $termin)
				{			
					// Pro Termin den entsprechenden Anwesenheitsstatus laden 
					$result = Database::getInstance()->prepare('SELECT attendance,tstamp FROM tl_attendance WHERE m_id=? AND e_id=?')->execute($reihe['id'],$termin['id']);	
					$attendances = $result->fetchAllAssoc();
					
					foreach ($attendances as $attendance)
						{					
							$att = $attendance['attendance'];
							
							// Flag-Variablen false setzen und nur true setzen, wenn Rechte/Zeit es benötigen
							$flagExpired = false;
							$flagEdit = false;
							
							// Abhängig von Nutzerrolle die Felder editierbar machen oder nur als Bilder ausgeben
							if($intLoggedUserID==$intCoachID || $intLoggedUserID==$intAdminID)
							{									
								$flagEdit = true;
								if ($i<=$intExpiredEvents)
								{
									$flagExpired = true; 
								}										
							}	
							else if($intLoggedUserID==$reihe['id'])
							{
								if ($i<=$intExpiredEvents)
								{
									$flagExpired = true; 									
								}
								else 
								{									
									$flagEdit = true;
								}
							}
							
							else
							{
								if ($i<=$intExpiredEvents)
								{
									$flagExpired = true;
								}								
							}	
							// Funktion aufrufen, die das entsprechende Feld zurückliefert
							$att = $this->createStatusField($att, $reihe['id'], $termin['id'], $flagExpired, $flagEdit, $iconSetPath, $name, $attendance['tstamp']);
								
							array_push($stati, $att);
						}
					$i++;
				}				
				
				
				// Bearbeiten der Daten vor der Übergabe (eingeloggten Nutzer hinzufügen
				// gerade/ungerade Zeilen markieren
				$row += 1;							
				
				// Trainerrolle Hinweis hinzufügen
				if($intCoachID==$reihe['id'])
				{
					$name .= " <i>(".$GLOBALS['TL_LANG']['al_frontend']['coach'].")</i>";
				}
				
				// Kapitänsrolle Hinweis hinzufügen
				if($intCaptainID==$reihe['id'])
				{
					$name .= " <i>(".$GLOBALS['TL_LANG']['al_frontend']['captain'].")</i>";
				}
				
				// Zeilenbeginn abhängig vom eingeloggten Nutzer erstellen
				if($row%2==1)
				{
					$strUserHTMLclass = "odd";
				}
				else
				{
					$strUserHTMLclass = "even";
				}
				
				if($intLoggedUserID==$reihe['id'])
				{
					$strUserHTMLclass .= " logged_user";
				}
				
				if($intCoachID==$reihe['id'])
				{
					$strUserHTMLclass .= " coach";
				}
				
				$name = "<tr class='".$strUserHTMLclass."'><td class='col_member'>".$name."</td>";
					
				
				// Daten-Array aufbauen
				$dataArray[] = array(					
					'mitglied' 	=> $name,
					'stati' 	=> $stati
					);
				
			}
			
		
		/**
		 * ENDE: Daten verarbeiten
		 */		
		
		
		/**
		 * Weitergabe der Arrays an das Frontend-Template zur Ausgabe
		 */
		
		$this->Template->tableBody = $dataArray;
		$this->Template->tableHead = $headings;
		
		
		/* 
			Ausgabe von Variablen-Werten
			
			echo "<pre>"; print_r($dataArray); echo "</pre>"; 
		*/
	}	
}