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
 * fbnotifier event handlers definition.
 *
 * @package message
 * @copyright 2017 Paulo Jr.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot.'/user/profile/lib.php'); 

class message_fbnotifier_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function edit_user_profile_parameters() {
        return new external_function_parameters(
                array(
					'username' => new external_value(PARAM_TEXT, 'The username of the user whose profile will be updated.'),
					'facebook_id' => new external_value(PARAM_INT, 'The id of the user facebook account')
				)
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function edit_user_profile($username, $facebook_id) {
        global $USER;
		global $DB;
		global $CFG;
			
		//Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::edit_user_profile_parameters(),
                array('username' => $username, 'facebook_id' => $facebook_id));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:editprofile', $context)) {
            throw new moodle_exception('cannoteditprofile');
        }

		$user_to_be_updated = $DB->get_record('user', array('username' => $username));
        
        if ($user_to_be_updated == null) {
			return 0; 
		}
        
        profile_load_data($user_to_be_updated);
        
        if (!isset($user_to_be_updated->profile_field_fbmessengerid)) {
			return 0; 
		}
        
        $user_to_be_updated->profile_field_fbmessengerid = $facebook_id;
		profile_save_data($user_to_be_updated);
		
		$DB->update_record('user', $user_to_be_updated); 
		
        return $user_to_be_updated->username . ' ' .$user_to_be_updated->profile_field_fbmessengerid;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function edit_user_profile_returns() {
        return new external_value(PARAM_TEXT, '1, if the profile was successfully updated; 0, otherwise.');
    }



}
