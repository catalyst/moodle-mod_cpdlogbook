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

use mod_cpdlogbook\event\entry_updated;
use mod_cpdlogbook\form\edit_entry;
use mod_cpdlogbook\event\entry_created;

require_once('../../config.php');

// Get the course module id and the entry id from either the parameters or the hidden fields.
$id = required_param('id', PARAM_INT);
$create = required_param('create', PARAM_BOOL);

if ($create) {
    // If an entry is being created.
    $record = new stdClass();
    $record->id = $id;

    list ($course, $cm) = get_course_and_cm_from_cmid($id, 'cpdlogbook');

    // If an existing entry is being edited.
    require_course_login($course, false, $cm);
} else {
    // If the entry doesn't exist.
    $record = $DB->get_record('cpdlogbook_entries', ['id' => $id, 'userid' => $USER->id], '*', MUST_EXIST);

    // If the cpdlogbook doesn't exist.
    $cpdlogbook = $DB->get_record('cpdlogbook', ['id' => $record->cpdlogbookid], '*', MUST_EXIST);

    // Get the course module from the cpdlogbook instance.
    $cm = get_coursemodule_from_instance('cpdlogbook', $cpdlogbook->id, $cpdlogbook->course);

    require_course_login($cpdlogbook->course, false, $cm);
}

$context = context_module::instance($cm->id);

$mform = new edit_entry();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cm->id]));
} else if ($fromform = $mform->get_data()) {
    if ($create) {
        unset($fromform->id);

        $fromform->cpdlogbookid = $cm->instance;
        $fromform->userid = $USER->id;
        $fromform->time = time();

        $entryid = $DB->insert_record('cpdlogbook_entries', $fromform, true);
        $newentry = $DB->get_record('cpdlogbook_entries', ['id' => $entryid]);

        // Trigger an entry_created event after the record has been inserted into the database.
        entry_created::create_from_entry($newentry, $context)->trigger();
    } else {
        // Update the record according to the submitted form data.
        $DB->update_record('cpdlogbook_entries', $fromform);
        $entry = $DB->get_record('cpdlogbook_entries', ['id' => $fromform->id]);

        // Trigger an entry_updated event.
        entry_updated::create_from_entry($entry, $context)->trigger();
    }

    redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cm->id]));
}

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/edit.php', [ 'id' => $id, 'create' => $create ]));

// Set the title according to if an entry is being created or updated.
if ($create) {
    $title = get_string('createtitle', 'mod_cpdlogbook');
} else {
    $title = $record->name;
}

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$record->create = $create;
$mform->set_data($record);
$mform->display();

echo $OUTPUT->footer();
