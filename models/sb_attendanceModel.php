<?php

/*
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
 *
 */

/**
 * Class sb_attendanceSettingsModel
 *
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */
if (!class_exists('sb_attendanceModel'))
{
    class sb_attendanceModel extends \Model
    {
        // Funktion zum Holen aller AnwesenheitslistenIDs
        public static function findAttendanceIDs()
        {
            $result = Database::getInstance()
                    ->prepare('SELECT id FROM tl_attendance_lists')
                    ->execute();
            $settingFound = $result->fetchAllAssoc();

            return $settingFound;
        }

        // Funktion zum Finden der Einstellungen einer bestimmten Anwesenheitsliste
        public static function findSettings($attendance_ID, $setting)
        {
            $result = Database::getInstance()
                    ->prepare('SELECT ' . $setting . ' FROM tl_attendance_lists WHERE id=?')
                    ->execute($attendance_ID);
            $settingFound = $result->$setting;

            return $settingFound;
        }

        // Funktion zum Finden der Mitgliederrollen
        public static function findMemberRoles($role, $id)
        {
            $result = Database::getInstance()
                    ->prepare('SELECT ' . $role . ' FROM tl_attendance_lists WHERE id=?')
                    ->execute($id);
            $roleIdFound = $result->$role;

            return $roleIdFound;
        }

        public static function findCoach($intCoachID)
        {
            $coach = Database::getInstance()
                    ->prepare('SELECT firstname,lastname,email FROM tl_member WHERE id=? LIMIT 1')
                    ->execute($intCoachID);
            return $coach;
        }

        public static function findEventData($intEventID)
        {
            $termin = Database::getInstance()
                    ->prepare('SELECT title,startDate FROM tl_calendar_events WHERE id=?')
                    ->execute($intEventID);
            return $termin;
        }



        // Funktion zum Finden aller Spieler-IDs in der Anwesenheitsliste
        public static function findPlayersIDs($strNameSetting, $strNameSort, $attendanceID)
        {
            $resultSpieler = Database::getInstance()
                    ->prepare
                        ('
                            SELECT DISTINCT t1.' . $strNameSetting . ', t1.id,t1.email
                            FROM tl_member t1
                            JOIN tl_attendance t2
                            ON (t2.m_id = t1.id AND t2.attendance_id=?)
                            ORDER BY '.$strNameSort
                        )
                    ->execute($attendanceID);
            $return = $resultSpieler->fetchAllAssoc();
            return $return;
        }

        // Funktion zum Finden aller Event-IDs in der Anwesenheitsliste
        public static function findEventIDs($id)
        {
            $resultTermine = Database::getInstance()
                    ->prepare
                        ('
                            SELECT DISTINCT t1.id, t1.title, t1.startDate, t1.startTime, t1.location, t1.teaser, t1.meetingTime
                            FROM tl_calendar_events t1
                            JOIN tl_attendance t2
                            ON (t2.e_id = t1.id AND t2.attendance_id=?)
                            ORDER BY t1.startTime
                        ')
                    ->execute($id);
            $return = $resultTermine->fetchAllAssoc();
            return $return;
        }

        // Funktion, um Anzahl abgelaufener Events zu finden
        public static function findExpiredEventsNumber($id)
        {
            $resultTermine = Database::getInstance()
                    ->prepare
                        ('
                            SELECT DISTINCT t1.id
                            FROM tl_calendar_events t1
                            JOIN tl_attendance t2
                            ON (t2.e_id = t1.id AND t1.startDate<? AND t2.attendance_id=?)
                            ORDER BY t1.startTime
                        ')
                    ->execute(time(),$id);
            $number = $resultTermine->count();
            return $number;
        }

        // Funktion zum Finden eines bestimmten Anwesenheitsstatus
        public static function findAttendance($memberID, $eventID, $attendanceID)
        {
            $result = Database::getInstance()
                    ->prepare('SELECT attendance,tstamp,reason FROM tl_attendance WHERE m_id=? AND e_id=? AND attendance_id=?')
                    ->execute($memberID, $eventID, $attendanceID);
            $attendanceFound = $result;

            return $attendanceFound;
        }

        // Funktion zum Ändern eines Status
        public static function setAttendance($newStatus, $time, $reason, $memberID, $eventID, $attendance_ID)
        {
            Database::getInstance()
                    ->prepare('UPDATE tl_attendance SET attendance=?,tstamp=?,reason=? WHERE m_id=? AND e_id=? AND attendance_id=?')
                    ->execute($newStatus, $time, $reason, $memberID, $eventID, $attendance_ID);
        }

        public static function cancelEvent($time, $int_POST_eventID, $int_POST_attendanceID, $cancelReason)
        {
            Database::getInstance()
                    ->prepare('UPDATE tl_attendance SET attendance=?,tstamp=?,reason=? WHERE e_id=? AND attendance_id=? AND attendance != ?')
                    ->execute(2, $time, $cancelReason, $int_POST_eventID, $int_POST_attendanceID, 2);
        }

        // Funktion zum Finden der Teilnehmerzahl
        public static function findNumberOfParticipants($termin, $coach, $attendance_ID)
        {
            $statement = "SELECT id FROM tl_attendance WHERE e_id=? AND attendance_id=? AND (attendance=? OR attendance=?)";
            if ($coach)
            {
                $statement .= " AND m_id!=?";
            }

            $result = Database::getInstance()
                    ->prepare(''.$statement.'')
                    ->execute($termin,$attendance_ID,1,3,$coach);

            return $result->count();
        }

        // Funktion zum Löschen eines Events oder Spielers aus der Liste
        public static function deleteFromAttendanceTable($delField, $delID)
        {
            Database::getInstance()
                ->prepare('DELETE FROM tl_attendance WHERE '.$delField.'=?')
		->execute($delID);
        }

        // Funktion zum Löschen der Events eines gelöschten Kalenders
        public static function deleteCalEvents($delCalID)
        {
            Database::getInstance()
                ->prepare("DELETE FROM tl_attendance WHERE e_id IN (SELECT id FROM tl_calendar_events WHERE pid=?)")
		->execute($delCalID);
        }

        // Funktion zum Löschen der Mitgliederrollen einer Liste
        public static function removeRole($removeRole, $id)
        {
            Database::getInstance()
                    ->prepare('UPDATE tl_attendance_lists SET '.$removeRole.'="" WHERE id=?')
                    ->execute($id);
        }


        // Funktion zum Austragen aus dem gesamten Kalender
        public static function setAbsentForCal($time, $memberID, $calID, $attendance_ID, $sperrZeit)
        {

          $cancel = $time + $sperrZeit;

          if ($calID != "allActive") {

            $futureEvents = Database::getInstance()
                  ->prepare('SELECT id FROM tl_calendar_events WHERE pid=? AND startTime>?')
                  ->execute($calID, $cancel);

            $arrFutureEvents = $futureEvents->fetchAllAssoc();

            foreach ($arrFutureEvents as $event) {
              Database::getInstance()
                    ->prepare('UPDATE tl_attendance SET attendance=?,tstamp=?,reason=? WHERE e_id=? AND m_id=? AND attendance_id=?')
                    ->execute(2, $time, "--generelle Abmeldung über Button--", $event['id'], $memberID, $attendance_ID);
            }
          }
          else {

            $futureEvents = Database::getInstance()
                  ->prepare('SELECT id FROM tl_calendar_events WHERE startTime>?')
                  ->execute($cancel);

            $arrFutureEvents = $futureEvents->fetchAllAssoc();

            foreach ($arrFutureEvents as $event) {
              Database::getInstance()
                    ->prepare('UPDATE tl_attendance SET attendance=?,tstamp=?,reason=? WHERE e_id=? AND m_id=? AND attendance_id=?')
                    ->execute(1, $time, "klappt", $event['id'], $memberID, $attendance_ID);
            }
          }

        }
    }
}
