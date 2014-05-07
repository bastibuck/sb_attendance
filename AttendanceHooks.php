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
    public function addUserToAttendance($objUser)
    {
        UpdateAttendance::al_createAttendance("all");
    }	 
}