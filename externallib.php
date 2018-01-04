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

class message_fbnotifier_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function edit_user_profile_parameters() {
        return new external_function_parameters(
                array(
					'user_login' => new external_value(PARAM_TEXT, 'The login of the user whose profile will be updated.'),
					'facebook_id' => new external_value(PARAM_INT, 'The id of the user facebook account')
				)
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function edit_user_profile($user_login, $facebook_id) {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::edit_user_profile_parameters(),
                array('user_login' => $user_login, 'facebook_id' => $facebook_id));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:editprofile', $context)) {
            throw new moodle_exception('cannoteditprofile');
        }

        return $params['user_login'] . ', ' . $params['facebook_id'] . ', ' . $USER->firstname ;;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function edit_user_profile_returns() {
        return new external_value(PARAM_TEXT, '1, if the profile was successfully updated; 0, otherwise.');
    }



}
