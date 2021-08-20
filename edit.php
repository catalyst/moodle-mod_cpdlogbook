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

use mod_cpdlogbook\form\edit_entry;

require_once('../../config.php');

// Get the course module id and the entry id from either the parameters or the hidden fields.
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

$mform = new edit_entry();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cm->id]));
} else if ($fromform = $mform->get_data()) {
    // Update the record according to the submitted form data.
    $DB->update_record('cpdlogbook_entries', $fromform);

    redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cm->id]));
}

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/edit.php', [ 'id' => $id ]));

$PAGE->set_title($record->name);
$PAGE->set_heading($record->name);

echo $OUTPUT->header();

$mform->set_data($record);
$mform->display();

echo $OUTPUT->footer();
