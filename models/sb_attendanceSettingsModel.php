<?php

/*
* Contao Open Source CMS
* Copyright (C) 2005-2013 Leo Feyer
*
*/

/**
 * Class sb_attendanceSettingsModel
 *
 * @copyright  Sebastian Buck 2013
 * @author     Sebastian Buck
 * @package    Attendance
*/


if(!class_exists('sb_attendanceSettingsModel'))
{

	class sb_attendanceSettingsModel extends \Model
	{
	/**
	* Table name
	* @var string
	*/

		public static function findSettings($moduleID, $setting)
		{
			static $strTable = 'tl_module';
		
			$result = Database::getInstance()
				->prepare('SELECT '.$setting.' FROM tl_module WHERE id=?')
				->execute($moduleID);				
			$settingFound = $result->$setting;

			return $settingFound;
		}
		
	}

}