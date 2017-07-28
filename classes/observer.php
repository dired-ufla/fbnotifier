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
 * Event observers used in fbnotifier.
 *
 * @package    message
 * @copyright  2017 Paulo Jr.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/../../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

class message_output_fbnotifier_observer {    
    
    public static function course_module_created(\core\event\course_module_created $event) {
		global $DB;
		
		$user_that_creates_a_module = $DB->get_record('user', array('id' => $event->userid));
		
		$a = new stdClass;
		$a->modulename = $event->other['name'];
		$a->username = $user_that_creates_a_module->firstname . ' ' . $user_that_creates_a_module->lastname;
		$content = get_string_manager()->get_string('modulecreatedmessage', 'message_fbnotifier', $a); 
		
		// Find users that will receive a notification in their messenger app
		$context = context_course::instance($event->courseid);
		$users = get_enrolled_users($context);
		foreach ($users as $user) {
			$message = new \core\message\message();
				
			// Moodle 2.9 does not have this property
			// and Moodle 3.2 requires it
			if (property_exists('\core\message\message', 'courseid')) { 
				$message->courseid = $event->courseid;
			}
			$message->component = 'message_fbnotifier';
			$message->name = 'coursemodulecreated';
			$message->userfrom = get_admin();
			$message->userto = $user;
		
			// I'm using this field to decide whether I must send the message immediately or not.
			// I have to change this before, using $message->set_additional_content('email', $content); method. 
			$message->subject = 'no';
			$message->fullmessage = '';
			$message->fullmessageformat = FORMAT_PLAIN;
			$message->fullmessagehtml = '';
			$message->smallmessage = $content;
			$message->contexturl = $event->get_url();
			
			message_send($message);
		}
	}
	
	public static function course_module_graded(\core\event\user_graded $event) {
		global $DB;
		
		$user_that_provided_the_grade = $DB->get_record('user', array('id' => $event->userid));
		$user_that_receipt_the_grade = $DB->get_record('user', array('id' => $event->relateduserid));
		$item_graded = $DB->get_record('grade_items', array('id' => $event->other['itemid']));
		
		$a = new stdClass;
		$a->modulename = $item_graded->itemname;
		$a->username = $user_that_provided_the_grade->firstname . ' ' . $user_that_provided_the_grade->lastname;
		$content = get_string_manager()->get_string('modulegradedmessage', 'message_fbnotifier', $a); 
		
		$message = new \core\message\message();
				
		// Moodle 2.9 does not have this property
		// and Moodle 3.2 requires it
		if (property_exists('\core\message\message', 'courseid')) { 
			$message->courseid = $event->courseid;
		}
		$message->component = 'message_fbnotifier';
		$message->name = 'coursemodulegraded';
		$message->userfrom = $user_that_provided_the_grade;
		$message->userto = $user_that_receipt_the_grade;
		$message->subject = 'yes';
		$message->fullmessage = '';
		$message->fullmessageformat = FORMAT_PLAIN;
		$message->fullmessagehtml = '';
		$message->smallmessage = $content;
		$message->contexturl = $event->get_url();
		
		message_send($message);		
	}
	
	private static function forum_communication_created($courseid, $userid, $url, $forumid) {
		global $DB;
		
		$forum = $DB->get_record('forum', array('id' => $forumid));
		$user_that_creates_a_communication = $DB->get_record('user', array('id' => $userid));
		
		$a = new stdClass;
		$a->username = $user_that_creates_a_communication->firstname . ' ' . $user_that_creates_a_communication->lastname;
		$a->forumname = $forum->name;
		$content = get_string_manager()->get_string('forummessage', 'message_fbnotifier', $a); 
		
		// Find users that will receive a notification in their messenger app
		if ($users = \mod_forum\subscriptions::fetch_subscribed_users($forum)) {	
			foreach ($users as $user) {
				$message = new \core\message\message();
				
				// Moodle 2.9 does not have this property
				// and Moodle 3.2 requires it
				if (property_exists('\core\message\message', 'courseid')) { 
					$message->courseid = $courseid;
				}
				
				$message->component = 'message_fbnotifier';
				if ($forum->type == 'news') {
					$message->name = 'newscommunicationcreated';
				} else {
					$message->name = 'forumcommunicationcreated';
				}

 				$message->userfrom = $user_that_creates_a_communication;
				$message->userto = core_user::get_user($user->id);;
				$message->subject = 'no';
				$message->fullmessage = '';
				$message->fullmessageformat = FORMAT_PLAIN;
				$message->fullmessagehtml = '';
				$message->smallmessage = $content;
				$message->contexturl = $url;
				
				message_send($message);
			}
		}
	}
	
	public static function forum_discussion_created(\mod_forum\event\discussion_created $event) {
		self::forum_communication_created($event->courseid, $event->userid, $event->get_url(), $event->other['forumid']);
	}
	
	public static function forum_post_created(\mod_forum\event\post_created $event) {
		self::forum_communication_created($event->courseid, $event->userid, $event->get_url(), $event->other['forumid']);
	}
	
	public static function user_profile_updated(\core\event\user_updated $event) {
		global $DB;
        $user = $DB->get_record('user', array('id' => $event->userid));
		$message = new \core\message\message();
            
        // Moodle 2.9 does not have this property
        // and Moodle 3.2 requires it
		if (property_exists('\core\message\message', 'courseid')) { 
			$message->courseid = SITEID;
		}            
        $message->component = 'message_fbnotifier';
        $message->name = 'userprofileupdated';
        $message->userfrom = get_admin();
        $message->userto = $user;
        $message->subject = 'yes';
        $message->fullmessage = '';
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = '';
        $message->smallmessage = get_string_manager()->get_string('userprofileupdatedmessage', 'message_fbnotifier');
		
        message_send($message);        	
	}
}
