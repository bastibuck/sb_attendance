<?php

/**
 * Class AttendanceListViewer
 *
 * Zusätzliche Methoden, um beim Erstellen/Ändern des Moduls, die Tabelle zu aktualisieren
 * 
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */
class UpdateAttendance extends Backend 
{
    public static function al_createAttendance($dc) 
    {
        // Anwesenheitslisten-IDs holen
        // Wenn eine Liste bearbeitet wurde, nur diesen Datensatz durcharbeiten
        // Sonst (Mitglieder, Events) alle bearbeiten
        if ($dc!="all")
        {            
            $attendanceIDs['id'] = $dc->id;           
        }
        else
        {
            $attendanceIDs = \sb_attendanceModel::findAttendanceIDs();
        }
        
        foreach ($attendanceIDs as $list)
        {       
            if (is_array($list))
            {
                $id = $list['id'];
            }
            else 
            {
                $id = $list;                
            }            
            
            // Ausgewählte Mitgliedergruppen laden        
            $al_mems = unserialize(\sb_attendanceModel::findSettings($id, 'al_pickMembers'));
            
            // Kalender-IDs nur für aktive Kalender holen
            $al_cals = unserialize(\sb_attendanceModel::findSettings($id, 'al_pickCalendar'));            
            $al_cals = implode(', ', $al_cals);

            if ($al_cals) 
            {
                // Events aus Kalendern, die nicht ausgewählt sind, aus tl_attendance löschen
                $delete = Database::getInstance()
                        ->prepare("
                                DELETE FROM tl_attendance 
                                WHERE attendance_id=?
                                AND e_id 
                                IN (SELECT id 
                                    FROM tl_calendar_events 
                                    WHERE published<1 
                                    OR pid NOT IN(" . $al_cals . "))")
                        ->execute($id);

                // Event-IDs für veröffentlichte Termine aus ausgesuchten Kalendern suchen und speichern
                $result = Database::getInstance()
                        ->prepare("
                                SELECT id
                                FROM tl_calendar_events 
                                WHERE pid 
                                IN(" . $al_cals . ") AND published=1 
                                ORDER BY id")
                        ->execute();
                $events = $result->fetchAllAssoc();
            }

            // User-ID von aktiven Mitgliedern holen und speichern
            $result = Database::getInstance()
                    ->query('SELECT id,groups
                             FROM tl_member 
                             WHERE disable!=1 
                             AND al_inactiveMember!=1
                             '); 
            $members = $result->fetchAllAssoc();

            // Nicht ausgewählte Mitglieder aussortieren
            $chosenMembers = array();
            foreach ($members as $member)
            {                   
                if (array_intersect(unserialize($member['groups']), $al_mems))
                {
                    array_push($chosenMembers, $member);
                }
                else
                {  
                    Database::getInstance()
                            ->prepare("DELETE FROM tl_attendance WHERE attendance_id=? AND m_id=?")
                            ->execute($id, $member['id']);
                }
            }               
            
            // tl_attendance aus User-IDs und Kalender-IDs erstellen/aktualisieren
            foreach ($chosenMembers as $member) 
            {
                $arrNewData['m_id'] = $member['id'];
                foreach ($events as $event) 
                {
                    $arrNewData['e_id'] = $event['id'];
                    $arrNewData['attendance_id'] = $id;
                    Database::getInstance()->prepare("INSERT IGNORE INTO tl_attendance %s")
                            ->set($arrNewData)
                            ->execute();
                }
            }

            // standard Status setzen für alle Felder, die noch nicht geändert wurden (tstamp=0)			
            $defaultStatus = sb_attendanceModel::findSettings($id, 'al_defaultStatus');

            Database::getInstance()
                    ->prepare('UPDATE tl_attendance SET attendance=? WHERE tstamp=? AND attendance_id=?')
                    ->execute($defaultStatus, 0, $id);
        }
    }
}