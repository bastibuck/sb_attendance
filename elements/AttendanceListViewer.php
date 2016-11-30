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
 * Class AttendanceListViewer
 *
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */
class AttendanceListViewer extends \ContentElement
{
    /**
     * Template
     * @var strTemplate
     *
     * Das zugehörige Template (Ausgabe im Frontend) wird festgelegt
     */
    protected $strTemplate = 'ce_attendance_list_viewer';

    // Funktion, um die einzelnen Status Felder zu erzeugen
    private function createStatusField(
        $att,
        $intReiheID,
        $intTerminID,
        $flagExpired,
        $flagEdit,
        $iconSetPath,
        $strName,
        $tstamp,
        $attendanceID,
        $flagAskReason)
    {
        $attendanceName = array('unknown', 'yes', 'no', 'later');

        // Änderungszeitpunkt zusammensetzen
        if ($tstamp != 0)
        {
            $timeChanged = '(';
            $timeChanged .= date($GLOBALS['TL_CONFIG']['datimFormat'], $tstamp);
            $timeChanged .= $GLOBALS['TL_LANG']['al_frontend']['time'];
            $timeChanged .= ')';
        }
        else
        {
            $timeChanged = '';
        }

        // Alt- und Title-Attribut Wert zusammensetzen
        $strAltTitleTag = $strName;
        $strAltTitleTag .= ': ';
        $strAltTitleTag .= $GLOBALS['TL_LANG']['al_frontend'][$attendanceName[$att]];
        $strAltTitleTag.= $timeChanged;

        // Feld aufbauen
        $strStatusField = '<td';
        // abgelaufene Termine kennzeichnen
        if ($flagExpired)
        {
            $strStatusField .= " class='expired'";
            $strExpired = '_expired';
        }

        // Grund der Abwesenheit abfragen
        $flagDisableThird = \sb_attendanceModel::findSettings($attendanceID, 'al_disableThird');
        if ($att == 3)
        {
            $giveReason = "onSubmit='return saveReason(this.name);'";
        }
        else if ($att == 1 && $flagDisableThird == 1)
        {
            $giveReason = "onSubmit='return saveReason(this.name);'";
        }

        // Grund der Abwesenheit auslesen und auf Variable speichern
        if ($att == 2 && $flagAskReason==1)
        {
            $resultReason = \sb_attendanceModel::findAttendance($intReiheID, $intTerminID, $attendanceID);
            if ($resultReason->reason!="0" && $resultReason->reason!="")
            {
                $strReason = $GLOBALS['TL_LANG']['al_frontend']['reason'];
                $strReason .= $resultReason->reason;
            }

        }

        // Bearbeitbare Felder erzeugen
        if ($flagEdit)
        {
            $strStatusField .= ">
                <form ".$giveReason." name='".$attendanceID."_".$intReiheID."_".$intTerminID."' action='" . Environment::get('requestUri') . "' method='POST'>
                    <input type='hidden' name='REQUEST_TOKEN' value='{{request_token}}'>
                    <input type='image'
                           src='system/modules/sb_attendance/assets/img/" . $iconSetPath . "/" . $attendanceName[$att] . $strExpired . ".png'
                           alt='" . $strAltTitleTag . $strReason."'
                           title='" . $strAltTitleTag . $strReason."'>
                    <input type='hidden' value='" . $att . "' name='status'>
                    <input type='hidden' value='" . $attendanceID . "' name='attendanceID'>
                    <input type='hidden' value='" . $intReiheID . "' name='m_id'>
                    <input type='hidden' value='" . $intTerminID . "' name='e_id'>
                    <input type='hidden' value='--JS deaktiviert--' name='reason'>
                    <input type='hidden' value='".$GLOBALS['TL_LANG']['al_frontend']['reasonText']."' name='reasonText'>
                </form></td>";
        }
        // nicht bearbeitbare Felder erzeugen
        else {
            $strStatusField .= ">
                <img
                    src='system/modules/sb_attendance/assets/img/" . $iconSetPath . "/" . $attendanceName[$att] . $strExpired . ".png'
                    alt='" . $strAltTitleTag . "'
                    title='" . $strAltTitleTag . "'>
                </td>";
        }

        // Statusfeld zurückgeben
        return $strStatusField;
    }

    // Funktion, um eine Statusänderung vorzunehmen
    private function changeStatus(
        $intMemberID,
        $intEventID,
        $intStatus,
        $intOldStatus,
        $attendance_ID,
        $reason)
    {
        $flagDisableThird = \sb_attendanceModel::findSettings($attendance_ID, 'al_disableThird');

        // Vergleiche alten Status mit übermitteltem
        if ($intOldStatus->attendance == $intStatus)
        {
            // Statusänderung
            switch ($intStatus)
            {
                case 0:
                    $intNewStatus = 1;
                    break;
                case 1:
                    // Wenn 'Dritte Option deaktivieren' gesetzt wurde,
                    // direkt Status 2 setzen
                    if ($flagDisableThird == 1)
                    {
                        $intNewStatus = 2;
                    }
                    else
                    {
                        $intNewStatus = 3;
                    }
                    break;
                case 2:
                    $intNewStatus = 1;
                    break;
                case 3:
                    $intNewStatus = 2;
                    break;
            }

            // aktuelle Uhrzeit als Zeitstempel
            $time = time();

            // Eintragen in DB
            \sb_attendanceModel::setAttendance($intNewStatus, $time, $reason, $intMemberID, $intEventID, $attendance_ID);
        }
    }

		public function generate()
	  {
	    if (TL_MODE == 'BE')
	    {
	    	$this->Template = new BackendTemplate('be_wildcard');
	    	$this->Template->wildcard = '### Anwesenheitsliste ###';
	    	return $this->Template->parse();
	    }

	    return parent::generate();
	  }

		/**
     * Generate the module
     */
    protected function compile()
    {
        // ID der ausgewählten Anwesenheitsliste
        $attendance_ID = $this->attendance_list;

        /**
         * *****************************************************
         * ************** Daten aus Datenbank laden ************
         * *****************************************************
         */

        // ausgewähltes IconSet laden
        $iconSetPath = \sb_attendanceModel::findSettings($attendance_ID, 'al_iconSet');
        $iconSetPath .= '_icon_set';

        // optionales CSS - Daten laden
        $flagUseCSS = \sb_attendanceModel::findSettings($attendance_ID, 'al_useCSS');

        // optionales CSS - Daten laden
        $flagAskReason = \sb_attendanceModel::findSettings($attendance_ID, 'al_askReason');

        // Hoverbox-Status laden
        $flagShowInfo = \sb_attendanceModel::findSettings($attendance_ID, 'al_showInfos');

        // optionale Coach-Bezeichnung laden
        $strCoachDescription = \sb_attendanceModel::findSettings($attendance_ID, 'al_CoachDescription');

        // optionale Kapitän-Bezeichnung laden
        $strCaptainDescription = \sb_attendanceModel::findSettings($attendance_ID, 'al_CaptainDescription');

        // optionale Kapitän-Bezeichnung laden
        $strAttendantsDescription = \sb_attendanceModel::findSettings($attendance_ID, 'al_AttendantsDescription');


        // Sperrzeit laden
        $expireTime = \sb_attendanceModel::findSettings($attendance_ID, 'al_expireTime');
        $expireTime *= 3600;

        // Namendarstellung laden
        $strNameSetting = \sb_attendanceModel::findSettings($attendance_ID, 'al_name');

        if ($strNameSetting == "first_last")
        {
            // String für DB Abfrage anpassen, wenn Vor- und Nachname ausgegeben werden sollen
            $strNameSetting = "firstname, t1.lastname";
            $strNameSort = "t1.firstname, t1.lastname";
            $flagFirst_Last = 1;
        }
        else
        {
            $strNameSort = $strNameSetting;
        }

        // aktive SpielerIDs aus tl_attendance holen
        $arraySpieler = \sb_attendanceModel::findPlayersIDs($strNameSetting, $strNameSort, $attendance_ID);

        // aktive TerminIDs aus tl_attendance holen
        $arrayTerminIDs = \sb_attendanceModel::findEventIDs($attendance_ID);

        // Wenn kein Spieler oder Termin geladen werden konnte, Fehlermeldung anzeigen
        if (!$arrayTerminIDs || !$arraySpieler)
        {
            $this->Template->noRecords = "Fehler - Übersetzung definieren";
        }

        /*
         *  Pagination
         */
            // aktuelle Seite laden
            $page = $this->Input->get('page') ? $this->Input->get('page') : 1;

            // Elemente pro Seite
            $perPage = \sb_attendanceModel::findSettings($attendance_ID, 'al_eventsPerPage');

            // Anzahl anzuzeigende, abgelaufene Termine
            $intExpiredEvents = \sb_attendanceModel::findSettings($attendance_ID, 'al_expiredEvents');

            // Zahl abgelaufener Events
            $expiredEvents = \sb_attendanceModel::findExpiredEventsNumber($attendance_ID);

            // Fehler abfangen, wenn größere Zahl anzuzeigender, abgelaufener
            // Termine größer ist als tatsächliche Zahl abgelaufener Termine
            if ($intExpiredEvents > $expiredEvents)
            {
                $intExpiredEvents = $expiredEvents;
            }

            // Berechnung der Gesamtzahl
            $totalRecords = count($arrayTerminIDs) - $expiredEvents + $intExpiredEvents;

            // wo soll mit dem Lesen der Daten angefangen werden
            $offset = ($page - 1) * $perPage + $expiredEvents - $intExpiredEvents;

            // Pagination Menu (muss irgendwo nach der Ermittlung der Anzahlsätze (COUNT) stehen)
            $objPagination = new Pagination($totalRecords, $perPage);
            $this->Template->pagination = $objPagination->generate("\n  ");

            // Fetch data (in Abhängigkeit von $perPage)
            if ($perPage)
            {
               $resultTermine = Database::getInstance()
                        ->prepare
                            ('
                                SELECT DISTINCT t1.id, t1.title, t1.startDate, t1.startTime, t1.location, t1.teaser, t1.meetingTime
                                FROM tl_calendar_events t1
                                JOIN tl_attendance t2
                                ON (t2.e_id = t1.id AND t2.attendance_id=?)
                                ORDER BY t1.startTime
                                LIMIT '.$offset.','.$perPage.'
                            ')
                        ->execute($attendance_ID);
               $arrayTerminIDs= $resultTermine->fetchAllAssoc();
            }

        // Trainer suchen
        // $intCoachID = \sb_attendanceModel::findMemberRoles('al_Coach', $attendance_ID);
				$arrCoachIDs = deserialize(\sb_attendanceModel::findMemberRoles('al_Coach', $attendance_ID));

        // Kapitän suchen
        $intCaptainID = \sb_attendanceModel::findMemberRoles('al_Captain', $attendance_ID);

        // Admin suchen
        $intAdminID = \sb_attendanceModel::findMemberRoles('al_Admin', $attendance_ID);

       /**
         * *****************************************************
         * *************** Statusänderung **********************
         * *****************************************************
         */

        // Überprüfen, ob POST-Variablen gesetzt sind, also ein Status geändert werden sollte
        if ($this->Input->post('m_id'))
        {
            // Variablen mit POST-Werten belegen
            $int_POST_memberID = $this->Input->post('m_id');
            $int_POST_eventID = $this->Input->post('e_id');
            $int_POST_status = $this->Input->post('status');
            $int_POST_attendanceID = $this->Input->post('attendanceID');
            $int_POST_reason = $this->Input->post('reason');

            $intOldStatus = \sb_attendanceModel::findAttendance($int_POST_memberID, $int_POST_eventID, $int_POST_attendanceID);

            // Funktion zur Statusänderung aufrufen
            $this->changeStatus($int_POST_memberID,$int_POST_eventID,$int_POST_status,$intOldStatus,$int_POST_attendanceID,$int_POST_reason);
        }

        // Termin abgesagt
        if ($this->Input->post('cancel') && $this->Input->post('attendanceID')==$attendance_ID)
        {
            // Variablen mit POST-Werten belegen
            $int_POST_eventID = $this->Input->post('e_id');
            $int_POST_attendanceID = $this->Input->post('attendanceID');
            $cancelReason = $GLOBALS['TL_LANG']['al_frontend']['cancelReason'];

            $termin = \sb_attendanceModel::findEventData($int_POST_eventID);
            $terminName = $termin->title;
            $terminDatum = date($GLOBALS['TL_CONFIG']['dateFormat'], $termin->startDate);
            $time = time();

            $intCancelerID = $this->Input->post('canceler_id');

            // $coach = \sb_attendanceModel::findCoach($intCoachID);
            $coach = \sb_attendanceModel::findCoach($intCancelerID);

            $coachName .= $coach->firstname. " ";
            $coachName .= $coach->lastname;
            $coachEmail = $coach->email;

            $emails = array();

            // Funktion zur Statusänderung aufrufen
            \sb_attendanceModel::cancelEvent($time, $int_POST_eventID, $int_POST_attendanceID, $cancelReason);

            // Empfänger-Emails holen und ins Array schieben
            foreach ($arraySpieler as $spieler)
            {
                array_push($emails, $spieler['email']);
            }

            // Email an jeden Empfänger verschicken
            foreach ($emails as $empfaenger)
            {
                // Emailbenachrichtigung
                $betreff = $terminName.$GLOBALS['TL_LANG']['al_frontend']['eventCancelHead'];
                $nachricht = $GLOBALS['TL_LANG']['al_frontend']['eventCancelMsg-1'];
                $nachricht .= "\"".$terminName. "\" (".$terminDatum.") ";
                $nachricht .= $GLOBALS['TL_LANG']['al_frontend']['eventCancelMsg-2'];
                $nachricht .= $coachName;
                $header = "From: ".$coachName."<".$coachEmail.">\n";
                $header.= "Content-Type: text/plain;\n\t charset=\"utf-8\"\n";
                $header.= "Content-Transfer-Encoding: 8bit\n";

                mail($empfaenger, $betreff, $nachricht, $header);
            }

        }


        // Austragen aus gesamtem Kalender
        if ($this->Input->post('chosenCal'))
        {
          $postChosenCal = $this->Input->post('chosenCal');
          $postUserID = $this->Input->post('activeUserID');
          $time = time();

          \sb_attendanceModel::setAbsentForCal($time, $postUserID, $postChosenCal, $attendance_ID, $expireTime);

        }

        /**
         * *****************************************************
         * *************** Daten verarbeiten *******************
         * *****************************************************
         */

        /**
         * Eingeloggten Nutzer Laden
         */
        $this->import('FrontendUser');
        $intLoggedUserID = $this->FrontendUser->id;

        // Abfangen von Bearbeitungsmöglichkeit bei keinem eingeloggten Nutzer
        if (!$intLoggedUserID)
        {
            $intLoggedUserID = 'kein eingelogter Nutzer';
            $this->Template->noUser = $GLOBALS['TL_LANG']['al_frontend']['noUser'];
        }

        /**
         * CSS einbinden
         */
        // Einbinden der mitgelieferten CSS-Anweisungen, wenn die Option gesetzt wurde
        if ($flagShowInfo == 1 && TL_MODE == 'FE')
        {
            $GLOBALS['TL_CSS']['al_css_hover'] = 'system/modules/sb_attendance/assets/css/al_hover_box.css';
        }

        if ($flagUseCSS == 1 && TL_MODE == 'FE')
        {
            $GLOBALS['TL_CSS']['al_css'] = 'system/modules/sb_attendance/assets/css/al_style.css';
        }

        /**
         * JS einbinden
         */
        if ($flagAskReason == 1 && TL_MODE == 'FE')
        {
            $GLOBALS['TL_JAVASCRIPT']['al_js'] = 'system/modules/sb_attendance/assets/js/saveReason.js';
        }

        /**
         * Zukünftige und abgelaufene Events trennen
         */
        $future = array();
        $expired = array();

        foreach ($arrayTerminIDs as $termin)
        {
            if (($termin['startTime'] - $expireTime) < time())
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
        if ($intExpiredEvents > sizeof($expired))
        {
            $intExpiredEvents = sizeof($expired);
        }

        // abgelaufene Events zu dem Array der kommenden Events hinzufügen
        $i = 1;
        while ($i <= $intExpiredEvents)
        {
            $last = array_pop($expired);
            array_unshift($future, $last);
            $i++;
        }
        $arrayTermine = $future;

        /**
         * Spaltenüberschriften bilden
         */
        $headings = array();

        $i = 1;
        // Überschriften-Array mit den Titeln der Events befüllen
        foreach ($arrayTermine as $termin)
        {
            // Abgelaufene Events
            $strTitle = "<td";
            if ($i <= $intExpiredEvents)
            {
                $strTitle .= " class='expired'";
            }
            $strTitle .= ">
                <div class='al_title'>";

                if ($flagShowInfo)
                {
                    $strTitle .= "<a href='{{event_url::".$termin['id']."}}'
                                   title='{{event_title::".$termin['id']."}}'>".$termin['title']."</a>";
                    $strTitle .= "
                    <span class='hover_info'>
                        <h2>".$termin['title']."</h2>
                        <table>
                            <tr>
                                <td>".$GLOBALS['TL_LANG']['al_frontend']['datum']."</td><td>".date($GLOBALS['TL_CONFIG']['dateFormat'], $termin['startDate'])."</td>
                            </tr>";
                    if ($termin['meetingTime']!=0)
                    {
                        $strTitle .= "
                            <tr>
                                <td>".$GLOBALS['TL_LANG']['al_frontend']['treffen']."</td>
                                <td>".date($GLOBALS['TL_CONFIG']['timeFormat'], $termin['meetingTime'])
                                . $GLOBALS['TL_LANG']['al_frontend']['time']."</td>
                            </tr>";
                    }
                    if ($termin['startTime']!=0)
                    {
                        $strTitle .= "
                            <tr>
                                <td>".$GLOBALS['TL_LANG']['al_frontend']['anpfiff']."</td><td>".date($GLOBALS['TL_CONFIG']['timeFormat'], $termin['startTime'])
                                . $GLOBALS['TL_LANG']['al_frontend']['time']."</td>
                            </tr>";
                    }
                    if ($termin['location'])
                    {
                        $strTitle .= "
                            <tr>
                                <td>".$GLOBALS['TL_LANG']['al_frontend']['ort']."</td><td>".$termin['location']."</td>
                            </tr>";
                    }
                    if ($termin['teaser'])
                    {
                        $strTitle .= "
                            <tr>
                                <td>".$GLOBALS['TL_LANG']['al_frontend']['infos']."</td><td>".$termin['teaser']."</td>
                            </tr>";
                    }

                    $strTitle .= "
                        <tr>
                            <td>{{event::}}</td>
                            <td><a href='{{event_url::".$termin['id']."}}'
                                   title='{{event_title::".$termin['id']."}}'>".$GLOBALS['TL_LANG']['al_frontend']['link']."</a>
                            </td>
                        </tr>";
                    $strTitle .= "
                        </table>
                    </span>";
                }
                else
                {
                    $strTitle .= $termin['title'];
                }
                $strTitle .= "</div>";

            $termin['title'] = $strTitle;

            // Datumsformate berücksichtigen (Umwandeln in menschenlesbar)
            if ($termin['startTime'] != $termin['startDate'])
            {
                $termin['startTime'] = "<br>"
                        . date($GLOBALS['TL_CONFIG']['timeFormat'], $termin['startTime'])
                        . $GLOBALS['TL_LANG']['al_frontend']['time']
                        . "</p>";
            }
            else
            {
                $termin['startTime'] = '</p>';
            }

            $termin['startDate'] = "<p class='al_date'>" . date($GLOBALS['TL_CONFIG']['dateFormat'], $termin['startDate']);

            // aktive Spieleranzahl holen (abhängig ob ein Trainer definiert ist oder nicht)
            // $resultSpielerzahl = \sb_attendanceModel::findNumberOfParticipants($termin['id'], $intCoachID, $attendance_ID);
						$resultSpielerzahl = \sb_attendanceModel::findNumberOfParticipants($termin['id'], $arrCoachIDs, $attendance_ID);

            if ($strAttendantsDescription)
            {
                $strAttendants = $strAttendantsDescription;
            }
            else
            {
                $strAttendants = $GLOBALS['TL_LANG']['al_frontend']['attendants'];
            }

            $number = "<p>" . $strAttendants . ":<br>" . $resultSpielerzahl . "</p>";

            $termin['summe'] = $number;

            // if (($intLoggedUserID == $intCoachID) || ($intLoggedUserID == $intAdminID))
						if ((in_array($intLoggedUserID, $arrCoachIDs)) || ($intLoggedUserID == $intAdminID))
            {
                $termin['cancelEvent'] = "
                    <form onsubmit='return confirm(\"".$GLOBALS['TL_LANG']['al_frontend']['cancel']."?\");' action='" . Environment::get('requestUri') . "' method='POST'>
                        <input type='hidden' name='REQUEST_TOKEN' value='{{request_token}}'>
                        <input type='image'
                               src='system/modules/sb_attendance/assets/img/" . $iconSetPath . "/cancel.png'
                               alt='".$GLOBALS['TL_LANG']['al_frontend']['cancel']."'
                               title='".$GLOBALS['TL_LANG']['al_frontend']['cancel']."'>
                        <input type='hidden' value='" . $attendance_ID . "' name='attendanceID'>
                        <input type='hidden' value='" . $termin['id'] . "' name='e_id'>
                        <input type='hidden' value='" . $intLoggedUserID . "' name='canceler_id'>
                        <input type='hidden' value='true' name='cancel'>
                    </form>";
                $termin['cancelEvent'] .= "</td>";
            }

            // Überschriften-Array befüllen
            array_push($headings, $termin);

            $i++;
        }


        /**
         * Zeilen erzeugen
         */

        // Eingeloggten Spieler nach oben sortieren (leichtere Bearbeitung)
        $i = 1;
        foreach ($arraySpieler as $logged)
        {
            if ($logged['id'] == $intLoggedUserID)
            {
                array_unshift($arraySpieler, $logged);
                unset($arraySpieler[$i]);
            }
            $i++;
        }

        // Kapitän an erste Stelle im Array sortieren
        // (und später durch Trainer ersetzen: Kapitän-> zweite Stelle)
        $i = 1;
        foreach ($arraySpieler as $captain)
        {
            if ($captain['id'] == $intCaptainID)
            {
                array_unshift($arraySpieler, $captain);
                unset($arraySpieler[$i]);
            }
            $i++;
        }

        // Trainer an erste Stelle im Array sortieren
        $i = 0;
				$arrTrainer = array();
				foreach ($arraySpieler as $trainer)
        {
            // if ($trainer['id'] == $intCoachID)
						if (in_array($trainer['id'], $arrCoachIDs))
            {
							$arrTrainer[] = $trainer;
              // array_unshift($arraySpieler, $trainer);
              unset($arraySpieler[$i]);
            }
            $i++;
        }
				$arraySpieler = array_merge ($arrTrainer, $arraySpieler);

				$CoachCount = 1;
        // Pro Spieler eine Reihe erzeugen
        foreach ($arraySpieler as $reihe)
        {
            // Variablen definieren
            if ($flagFirst_Last == 1)
            {
                $strName = $reihe['firstname'] . " " . $reihe['lastname'];
            }
            else
            {
                $strName = $reihe['' . $strNameSetting . ''];
            }

            // Array für Statusfelder
            $arrayStati = array();

            $i = 1;
            // Termine durchlaufen
            foreach ($arrayTermine as $termin)
            {
                // Pro Termin den entsprechenden Anwesenheitsstatus laden
                $resultAttendances = \sb_attendanceModel::findAttendance($reihe['id'], $termin['id'], $attendance_ID);
                $attendances = $resultAttendances->fetchAllAssoc();

                foreach ($attendances as $attendance)
                {
                    // Flag-Variablen false setzen und nur true setzen, wenn Rechte/Zeit es benötigen
                    $flagExpired = false;
                    $flagEdit = false;

                    // Abhängig von Nutzerrolle die Felder editierbar machen oder nur als Bilder ausgeben
                    // if ($intLoggedUserID == $intCoachID || $intLoggedUserID == $intAdminID)
										if (in_array($intLoggedUserID, $arrCoachIDs) || $intLoggedUserID == $intAdminID)
                    {
                        $flagEdit = true;
                        if ($i <= $intExpiredEvents)
                        {
                            $flagExpired = true;
                        }
                    }
                    else if ($intLoggedUserID == $reihe['id'])
                    {
                        if ($i <= $intExpiredEvents)
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
                        if ($i <= $intExpiredEvents)
                        {
                            $flagExpired = true;
                        }
                    }
                    // Funktion aufrufen, die das entsprechende Feld zurückliefert
                    $att = $this->createStatusField(
                            $attendance['attendance'],
                            $reihe['id'],
                            $termin['id'],
                            $flagExpired,
                            $flagEdit,
                            $iconSetPath,
                            $strName,
                            $attendance['tstamp'],
                            $attendance_ID,
                            $flagAskReason);

                    array_push($arrayStati, $att);
                }
                $i++;
            }

            /**
             * Bearbeiten der Daten vor der Übergabe
             * (eingeloggten Nutzer hinzufügen, gerade/ungerade Zeilen markieren)
             */
            $row += 1;

            // Trainerrolle Hinweis hinzufügen
            // if ($intCoachID == $reihe['id'])
						if (in_array($reihe['id'], $arrCoachIDs))
            {
                // Optionale Bezeichnung zuweisen, sonst standard Bezeichnung
                if ($strCoachDescription)
                {
                    $strName .= " <i>(" . $strCoachDescription . ")</i>";
                }
                else
                {
                    $strName .= " <i>(" . $GLOBALS['TL_LANG']['al_frontend']['coach'] . ")</i>";
                }
            }

            // Kapitänsrolle Hinweis hinzufügen
            if ($intCaptainID == $reihe['id'])
            {
                // Optionale Bezeichnung zuweisen, sonst standard Bezeichnung
                if ($strCaptainDescription)
                {
                    $strName .= " <i>(" . $strCaptainDescription . ")</i>";
                }
                else
                {
                    $strName .= " <i>(" . $GLOBALS['TL_LANG']['al_frontend']['captain'] . ")</i>";
                }
            }

            // Zeilenbeginn abhängig vom eingeloggten Nutzer erstellen
            if ($row % 2 == 1)
            {
                $strUserHTMLclass = "odd";
            }
            else
            {
                $strUserHTMLclass = "even";
            }

            if ($intLoggedUserID == $reihe['id'])
            {
                $strUserHTMLclass .= " logged_user";
            }

            // if ($intCoachID == $reihe['id'])
						if (in_array($reihe['id'], $arrCoachIDs))
            {
							if ($CoachCount == count($arrCoachIDs)) {
								$strUserHTMLclass .= " coach";
							}
							$CoachCount++;
            }

            $strName = "<tr class='" . $strUserHTMLclass . "'><td class='col_member'>" . $strName . "</td>";

            // Daten-Array aufbauen
            $dataArray[] = array(
                'mitglied' => $strName,
                'stati' => $arrayStati
            );
        }

        /**
         * *****************************************************
         * ** Formular für austragen aus bestimmtem Kalender ***
         * *****************************************************
         */
         $result = Database::getInstance()
                 ->prepare('SELECT al_pickCalendar FROM tl_attendance_lists WHERE id=?')
                 ->execute($attendance_ID);
         $objCalsFound = $result;

         $arrCals = unserialize($objCalsFound->al_pickCalendar);

         foreach ($arrCals as $cal) {

           $result = Database::getInstance()
                   ->prepare('SELECT title FROM tl_calendar WHERE id=?')
                   ->execute($cal);
           $objCal = $result;

           $htmlAdditionalForm .= "<p><form name='setAbsent_".$cal."' action='" . Environment::get('requestUri') . "' method='POST'>";

           $htmlAdditionalForm .= "<button name='btn_setAbsent'>Austragen aus Kalender \"".$objCal->title."\"</button>";
           $htmlAdditionalForm .= "<input type='hidden' name='REQUEST_TOKEN' value='{{request_token}}'>";
           $htmlAdditionalForm .= "<input type='hidden' value='".$intLoggedUserID."' name='activeUserID'>";
           $htmlAdditionalForm .= "<input type='hidden' value='".$cal."' name='chosenCal'>";

           $htmlAdditionalForm .= "</form></p>";
         }

         $htmlAdditionalForm .= "<p><form name='setAttending' action='" . Environment::get('requestUri') . "' method='POST'>";

         $htmlAdditionalForm .= "<button name='btn_setAttending'>In alle wieder eintragen</button>";
         $htmlAdditionalForm .= "<input type='hidden' name='REQUEST_TOKEN' value='{{request_token}}'>";
         $htmlAdditionalForm .= "<input type='hidden' value='".$intLoggedUserID."' name='activeUserID'>";
         $htmlAdditionalForm .= "<input type='hidden' value='allActive' name='chosenCal'>";

         $htmlAdditionalForm .= "</form></p>";

        /**
         * *****************************************************
         * ** Weitergabe an das Frontend-Template zur Ausgabe **
         * *****************************************************
         */

        $this->Template->tableBody = $dataArray;
        $this->Template->tableHead = $headings;

        $this->Template->additionalForm = $htmlAdditionalForm;
    }
}
