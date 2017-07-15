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
 * Version details.
 *
 * @package    message
 * @copyright  2017 Paulo Jr.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class message_fbnotifier_notificationtask extends \core\task\adhoc_task {                                                                           
    public function execute() {
		global $CFG;
		
		$data = $this->get_custom_data();
		
        $ch = curl_init($CFG->fbnotifierurl . $CFG->fbnotifieraccesstoken);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data->{'response'});
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_exec($ch);
		curl_close($ch);
    }                                                                                                                               
}
