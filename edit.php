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

use mod_cpdlogbook\forms\edit_entry;

require_once('../../config.php');

// Get the course module id and the entry id from either the parameters or the hidden fields.
$cmid = required_param('cmid', PARAM_INT);
$id = optional_param('id', null,PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'cpdlogbook');

require_course_login($course, false, $cm);

$cpdlogbook = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ]);

$mform = new edit_entry();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cmid]));
} else if ($fromform = $mform->get_data()) {
    // Update the record according to the submitted form data.

    if ($fromform->id != null) {
        $DB->update_record('cpdlogbook_entries', $fromform);
    } else {
        $fromform->cpdlogbook = $cpdlogbook->id;
        $fromform->user = $USER->id;

        // A placeholder until the CRUD form has the correct required field.
        $fromform->time = time();

        $DB->insert_record('cpdlogbook_entries', $fromform);
    }
    redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cmid]));
}

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/edit.php', [ 'cmid' => $cmid, 'id' => $id ]));

if ($id != null) {
    $record = $DB->get_record('cpdlogbook_entries', ['id' => $id]);

    $PAGE->set_title($record->name);
    $PAGE->set_heading($record->name);
} else {
    $record = new stdClass();

    $PAGE->set_title(get_string('createtitle', 'mod_cpdlogbook'));
    $PAGE->set_heading(get_string('createtitle', 'mod_cpdlogbook'));
}
echo $OUTPUT->header();

$record->cmid = $cmid;

$mform->set_data($record);
$mform->display();

echo $OUTPUT->footer();
