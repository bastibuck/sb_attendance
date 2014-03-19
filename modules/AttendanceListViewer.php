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
        $attendanceID) 
    {
        $attendanceName = array('unknown', 'yes', 'no', 'later');

        // Änderungszeitpunkt zusammensetzen
        if ($tstamp != 0)
        {
            $timeChanged = ' (';
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

        // Bearbeitbare Felder erzeugen
        if ($flagEdit) 
        {
            $strStatusField .= ">"
                . "<form action='" . Environment::get('requestUri') . "' method='POST'>
                        <input type='hidden' name='REQUEST_TOKEN' value='{{request_token}}'>
                        <input type='image' 
                               src='system/modules/sb_attendance/assets/img/" . $iconSetPath . "/" . $attendanceName[$att] . $strExpired . ".png'
                               alt='" . $strAltTitleTag . "'
                               title='" . $strAltTitleTag . "'>
                        <input type='hidden' value='" . $att . "' name='status'>
                        <input type='hidden' value='" . $attendanceID . "' name='attendanceID'>
                        <input type='hidden' value='" . $intReiheID . "' name='m_id'>
                        <input type='hidden' value='" . $intTerminID . "' name='e_id'>
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
        $attendance_ID)
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
                    $intNewStatus = 2;
                    break;
                case 2:
                    // Wenn 'Dritte Option deaktivieren' gesetzt wurde, 
                    // direkt wieder Status 1 setzen
                    if ($flagDisableThird == 1) 
                    {
                        $intNewStatus = 1;
                    } 
                    else 
                    {
                        $intNewStatus = 3;
                    }
                    break;
                case 3:
                    $intNewStatus = 1;
                    break;
            }

            // aktuelle Uhrzeit als Zeitstempel
            $time = time();

            // Eintragen in DB
            \sb_attendanceModel::setAttendance($intNewStatus, $time, $intMemberID, $intEventID, $attendance_ID);
        }
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
        
        // Hoverbox-Status laden			
        $flagShowInfo = \sb_attendanceModel::findSettings($attendance_ID, 'al_showInfos');
       
        // Anzahl abgelaufener Termine
        $intExpiredEvents = \sb_attendanceModel::findSettings($attendance_ID, 'al_expiredEvents');

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

        // Trainer suchen        
       $intCoachID = \sb_attendanceModel::findMemberRoles('al_Coach', $attendance_ID);

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
            
            $intOldStatus = \sb_attendanceModel::findAttendance($int_POST_memberID, $int_POST_eventID, $int_POST_attendanceID);     
                        
            // Funktion zur Statusänderung aufrufen
            $this->changeStatus($int_POST_memberID,$int_POST_eventID,$int_POST_status,$intOldStatus,$int_POST_attendanceID);            
        }
        
        // Termin abgesagt
        if ($this->Input->post('cancel') && $this->Input->post('attendanceID')==$attendance_ID) 
        {
            // Variablen mit POST-Werten belegen            
            $int_POST_eventID = $this->Input->post('e_id');
            $int_POST_attendanceID = $this->Input->post('attendanceID');
            
            $termin = \sb_attendanceModel::findEventData($int_POST_eventID);
            $terminName = $termin->title;
            $terminDatum = date($GLOBALS['TL_CONFIG']['dateFormat'], $termin->startDate);            
            $time = time();
            
            $coach = \sb_attendanceModel::findCoach($intCoachID);
            
            $coachName .= $coach->firstname. " ";
            $coachName .= $coach->lastname;
            $coachEmail = $coach->email;
            
            $emails = array();    
                                    
            // Funktion zur Statusänderung aufrufen
            \sb_attendanceModel::cancelEvent($time, $int_POST_eventID, $int_POST_attendanceID);
            
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
                                <td>Datum: </td><td>".date($GLOBALS['TL_CONFIG']['dateFormat'], $termin['startDate'])."</td>
                            </tr>";
                    if ($termin['meetingTime']!=0)
                    {
                        $strTitle .= "
                            <tr>
                                <td>Treffen: </td><td>".date($GLOBALS['TL_CONFIG']['timeFormat'], $termin['meetingTime']) 
                                . $GLOBALS['TL_LANG']['al_frontend']['time']."</td>
                            </tr>";
                    }
                    if ($termin['startTime']!=0)
                    {
                        $strTitle .= "
                            <tr>
                                <td>Anpfiff: </td><td>".date($GLOBALS['TL_CONFIG']['timeFormat'], $termin['startTime']) 
                                . $GLOBALS['TL_LANG']['al_frontend']['time']."</td>
                            </tr>"; 
                    }
                    if ($termin['location'])
                    {
                        $strTitle .= "
                            <tr>
                                <td>Ort: </td><td>".$termin['location']."</td>
                            </tr>";
                    }
                    if ($termin['teaser'])
                    {
                        $strTitle .= "
                            <tr>
                                <td>Infos: </td><td>".$termin['teaser']."</td>
                            </tr>";
                    }
                    
                    $strTitle .= "
                        <tr>
                            <td>{{event::}}</td>
                            <td><a href='{{event_url::".$termin['id']."}}' 
                                   title='{{event_title::".$termin['id']."}}'>Alle Informationen</a>
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
            $resultSpielerzahl = \sb_attendanceModel::findNumberOfParticipants($termin['id'], $intCoachID, $attendance_ID);            

            $number = "<p>" . $GLOBALS['TL_LANG']['al_frontend']['attendants'] . ":<br>" . $resultSpielerzahl . "</p>";

            $termin['summe'] = $number;
            
            if ($intLoggedUserID == $intCoachID) 
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
        // Trainer an erste Stelle im Array sortieren
        $i = 1;
        foreach ($arraySpieler as $trainer) 
        {
            if ($trainer['id'] == $intCoachID) 
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
                    if ($intLoggedUserID == $intCoachID || $intLoggedUserID == $intAdminID) 
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
                            $attendance_ID);

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
            if ($intCoachID == $reihe['id']) 
            {
                $strName .= " <i>(" . $GLOBALS['TL_LANG']['al_frontend']['coach'] . ")</i>";
            }

            // Kapitänsrolle Hinweis hinzufügen
            if ($intCaptainID == $reihe['id']) 
            {
                $strName .= " <i>(" . $GLOBALS['TL_LANG']['al_frontend']['captain'] . ")</i>";
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

            if ($intCoachID == $reihe['id']) 
            {
                $strUserHTMLclass .= " coach";
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
         * ** Weitergabe an das Frontend-Template zur Ausgabe **
         * *****************************************************
         */     
        
        $this->Template->tableBody = $dataArray;
        $this->Template->tableHead = $headings;        
    }
}