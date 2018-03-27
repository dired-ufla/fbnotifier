<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines message providers (types of messages being sent)
 *
 * @package    message
 * @copyright  2017 Paulo Jr.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$messageproviders = array (
    // A new announcement was created 
    'newscommunicationcreated' => array (
		'defaults' => array(
            'fbnotifier' => MESSAGE_FORCED, 
            'email' => MESSAGE_DISALLOWED,            
            'popup' => MESSAGE_DISALLOWED      
       ),
    ),
    
    // A new course module was created
    'forumcommunicationcreated' => array (
		'defaults' => array(
            'fbnotifier' => MESSAGE_PERMITTED,
           'email' => MESSAGE_DISALLOWED,            
            'popup' => MESSAGE_DISALLOWED
        ),
    ),
    
    // User updated her/his profile
    'userprofileupdated' => array (
		'defaults' => array(
            'fbnotifier' => MESSAGE_FORCED,
            'email' => MESSAGE_DISALLOWED,            
            'popup' => MESSAGE_DISALLOWED
        ),
    ),
    
    // A new course module was created
    'coursemodulecreated' => array (
		'defaults' => array(
           'fbnotifier' => MESSAGE_FORCED,
            'email' => MESSAGE_DISALLOWED,            
            'popup' => MESSAGE_DISALLOWED
       ),
    ),
    
    // A new course module receipt grades
    'coursemodulegraded' => array (
		'defaults' => array(
            'fbnotifier' => MESSAGE_PERMITTED,
            'email' => MESSAGE_DISALLOWED,            
            'popup' => MESSAGE_DISALLOWED
        ),
    )           
);
