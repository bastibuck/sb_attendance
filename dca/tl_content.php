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
 * Table tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['attendance_list_viewer'] = 
           '{type_legend},type,headline;
            {attendance_list_legend},attendance_list,al_tips;
            {protected_legend:hide},protected;
            {expert_legend:hide},invisible,cssID,space';

$GLOBALS['TL_DCA']['tl_content']['fields']['attendance_list'] = array
(
    'label'             => &$GLOBALS['TL_LANG']['tl_content']['attendance_list'],
    'inputType'         => 'select',
    'options_callback'  => array('DataContainerAttendanceLists', 'getAttendanceLists'),
    'sql'               => "int(10) unsigned NOT NULL default '0'",
    'eval' => array
        (
            'mandatory' => true,
            'includeBlankOption' => true
        )
);

$GLOBALS['TL_DCA']['tl_content']['fields']['al_tips'] = array
(    
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['al_tips'],
    'exclude'   => true,
    'inputType' => 'text',
    'sql'       => "varchar(1) NULL",
    'eval'      => array
    (
        'disabled' => true,
        'tl_class' => 'm12 tl_info tl_info_fix clr'
    )
);

/**
 * Class DataContainerAttendanceLists
 *
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */
class DataContainerAttendanceLists 
{
    public function getAttendanceLists() 
    {
        $objAttendanceLists = Database::getInstance()
                ->query('SELECT title,id 
                         FROM tl_attendance_lists
			 ORDER BY title');

        $arrOptions = array();
        while ($objAttendanceLists->next()) 
        {
            $arrOptions[$objAttendanceLists->id] = $objAttendanceLists->title;
        }
        return $arrOptions;
    }
}