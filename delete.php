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

require_once('../../config.php');

$id = required_param('id', PARAM_INT);

// If the entry doesn't exist.
if (! $record = $DB->get_record('cpdlogbook_entries', ['id' => $id, 'userid' => $USER->id])) {
    throw new moodle_exception('invalidentry');
}

// If the cpdlogbook doesn't exist.
if (! $cpdlogbook = $DB->get_record('cpdlogbook', ['id' => $record->cpdlogbookid])) {
    throw new moodle_exception('invalidentry');
}

// Get the course module from the cpdlogbook instance.
$cm = get_coursemodule_from_instance('cpdlogbook', $cpdlogbook->id, $cpdlogbook->course);

require_course_login($cpdlogbook->course, false, $cm);

require_sesskey();

// From here, we can be sure that the entry exists, and is associated with the current user and the cpdlogbook.
$DB->delete_records('cpdlogbook_entries', ['id' => $id, 'cpdlogbookid' => $cpdlogbook->id, 'userid' => $USER->id]);
redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cm->id]));
