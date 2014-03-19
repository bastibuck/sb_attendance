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
 * Class AttendanceHooks
 *
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */

class AttendanceHooks extends Backend
{
    /*
      public function addUserToAttendance($objUser)
	{
		// User-ID holen
		$result = Database::getInstance() ->query('SELECT id FROM tl_member WHERE id='.$objUser->id.'');
		$members = $result->fetchAllAssoc();
		
		// Events-IDs holen
		$result = Database::getInstance() ->query('SELECT id FROM tl_calendar_events ORDER BY id');		
		$events = $result->fetchAllAssoc();
		
		// Beides in tl_attendance eintragen
		foreach ($members as $member)
		{
			$arrNewData['m_id'] = $member['id'];
			foreach ($events as $event)
			{
				$arrNewData['e_id'] = $event['id'];
				$objData = $this->Database->prepare("INSERT IGNORE INTO tl_attendance %s")->set($arrNewData)->execute();
			}			
		}
	}*/
        
        
	public function addUserToAttendance($objUser)
	{
		// Events-IDs holen
		$result = Database::getInstance() ->query('SELECT id FROM tl_calendar_events ORDER BY id');		
		$events = $result->fetchAllAssoc();		
		
                $arrNewData['m_id'] = $objUser->id;
                foreach ($events as $event)
                {
                        $arrNewData['e_id'] = $event['id'];                        
                }			
                $this->Database->prepare("INSERT IGNORE INTO tl_attendance %s")->set($arrNewData)->execute();		
	}	 
}