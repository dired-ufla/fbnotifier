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

function xmldb_message_fbnotifier_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017061791) {
        $processor = new stdClass();
        $processor->name  = 'fbnotifier';
        if (!$DB->record_exists('message_processors', array('name' => $processor->name)) ){
            $DB->insert_record('message_processors', $processor);
        }

        //my message processor savepoint reached
        upgrade_plugin_savepoint(true, 2017061791, 'message', 'fbnotifier');
    }

    return true;
}
