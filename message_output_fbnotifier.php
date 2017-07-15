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
 * Facebook Messenger output plugin.
 *
 * @package    message
 * @copyright  2017 Paulo Jr.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/profile/lib.php'); 
 
class message_output_fbnotifier extends message_output {

    /**
     * Processes the message
     * @param object $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     */
    function send_message($eventdata) {
		global $DB;
		global $CFG;
		
        
        // Skip any messaging that does not provide courseid property.
		if (!isset($eventdata->courseid) or empty($eventdata->courseid)) {
			return true;
		}
		
		// Load profile fields defined by the admin
		profile_load_data($eventdata->userto);

		// Skip any messaging that does not provide fbmessengerid property.
		if (!isset($eventdata->userto->profile_field_fbmessengerid) or 
				empty($eventdata->userto->profile_field_fbmessengerid) or
				!is_numeric($eventdata->userto->profile_field_fbmessengerid)) {
			return true;
		}
		
		// Skip any messaging that does not provide usageconditions property (or its value is false).
		if (isset($eventdata->userto->profile_field_usageconditions) and 
				($eventdata->userto->profile_field_usageconditions == false)) {
			return true;
		}
		
		$course = $DB->get_record('course', array('id' => $eventdata->courseid));
		$message_text = $course->shortname . ' >> ' . $eventdata->smallmessage;
		
        $senderId = $eventdata->userto->profile_field_fbmessengerid;
		$response = '';
		if ($eventdata->contexturl != null and !empty($eventdata->contexturl))	{
			$url = $eventdata->contexturl;
			$lbbutton = get_string('lbbuttonurl', 'message_fbnotifier');
			$response = 
				"{
					'recipient' : {
						'id' : '$senderId'
					},
					'message' : {
						'attachment' : {
							'type':'template',
							'payload':{
								'template_type':'button',
								'text':'$message_text',
								'buttons':[
									{
										'type':'web_url',
										'url':'$url',
										'title':'$lbbutton'
									}
								]
							}
						}
					}
				}";
		} else {
			$response = "{
					'recipient' : {
						'id' : '$senderId'
					},
                    'message' : {
                        'text' : '$message_text'
                    }
                }";
		}
	
		$ch = curl_init($CFG->fbnotifierurl . $CFG->fbnotifieraccesstoken);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_exec($ch);
		curl_close($ch);		
		
		return true;
    }

    /**
     * Creates necessary fields in the messaging config form.
     * @param object $mform preferences form class
     */
    function config_form($preferences) {
        return null;
    }

    /**
     * Parses the form submitted data and saves it into preferences array.
     * @param object $mform preferences form class
     * @param array $preferences preferences array
     */
    function process_form($form, &$preferences) {
        return true;
    }

    /**
     * Loads the config data from database to put on the form (initial load)
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    function load_data(&$preferences, $userid) {
        global $USER;
        return true;
    }
    
    /**
     * Tests whether the Jabber settings have been configured
     * @return boolean true if Jabber is configured
     */
    function is_system_configured() {
        global $CFG;
        return (!empty($CFG->fbnotifierurl) && !empty($CFG->fbnotifieraccesstoken));
    }
    
    /**
     * Returns the default message output settings for this output
     *
     * @return int The default settings
     */
    public function get_default_messaging_settings() {
        return MESSAGE_DISALLOWED;
    }
}
