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
// We defined the web service functions to install.
$functions = array(
        'message_fbnotifier_edit_user_profile' => array(
                'classname'   => 'message_fbnotifier_external',
                'methodname'  => 'edit_user_profile',
                'classpath'   => 'message/output/fbnotifier/externallib.php',
                'description' => 'Include the facebook id int the user profile.',
                'type'        => 'read',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'FBNotifier service' => array(
                'functions' => array ('message_fbnotifier_edit_user_profile'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);
