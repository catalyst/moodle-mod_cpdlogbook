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

defined('MOODLE_INTERNAL') || die();

function cpdlogbook_add_instance($cpdlogbook) {
    global $DB;

    $cpdlogbook->id = $DB->insert_record('cpdlogbook', $cpdlogbook);

    return $cpdlogbook->id;
}

function cpdlogbook_update_instance($cpdlogbook) {
    global $DB;

    $cpdlogbook->id = $cpdlogbook->instance;

    return $DB->update_record('cpdlogbook', $cpdlogbook);
}
