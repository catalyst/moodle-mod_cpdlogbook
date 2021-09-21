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
 * Edit page. Used to edit and create records.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_cpdlogbook\event\entry_updated;
use mod_cpdlogbook\form\edit_entry;
use mod_cpdlogbook\event\entry_created;
use mod_cpdlogbook\persistent\period;

require_once('../../config.php');
require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

// Get the course module id and the entry id from either the parameters or the hidden fields.
$id = required_param('id', PARAM_INT);
$create = required_param('create', PARAM_BOOL);
// This parameter is used to check if the user accessed the form from the record's details.
$fromdetails = optional_param('fromdetails', false, PARAM_BOOL);

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

$draftitemid = file_get_submitted_draft_itemid('attachments');
file_prepare_draft_area($draftitemid, $context->id, 'mod_cpdlogbook', 'attachments', $record->id);
$record->attachments = $draftitemid;

require_capability('mod/cpdlogbook:edit', $context);

$mform = new edit_entry();
// Set the cpdlogbookid used for validation.
if ($create) {
    $mform->set_cpdlogbookid($cm->instance);
} else {
    $mform->set_cpdlogbookid($record->cpdlogbookid);
}

if (!$fromdetails) {
    $url = new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cm->id]);
} else {
    $url = new moodle_url('/mod/cpdlogbook/details.php', ['id' => $id]);
}

if ($mform->is_cancelled()) {
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    $fromform->periodid = period::get_period_for_date($fromform->completiondate, $cm->instance);

    // Extracts usable data from raw editor form.
    $fromform->reflection = $fromform->reflectionraw['text'];
    $fromform->reflectionformat = $fromform->reflectionraw['format'];

    if ($create) {
        unset($fromform->id);

        $fromform->cpdlogbookid = $cm->instance;
        $fromform->userid = $USER->id;
        $fromform->creationdate = time();

        $entryid = $DB->insert_record('cpdlogbook_entries', $fromform, true);
        $newentry = $DB->get_record('cpdlogbook_entries', ['id' => $entryid]);

        file_save_draft_area_files($fromform->attachments, $context->id, 'mod_cpdlogbook', 'attachments', $entryid);

        // Trigger an entry_created event after the record has been inserted into the database.
        entry_created::create_from_entry($newentry, $context)->trigger();
    } else {
        // Update the record according to the submitted form data.
        $fromform->modifieddate = time();

        $DB->update_record('cpdlogbook_entries', $fromform);
        $entry = $DB->get_record('cpdlogbook_entries', ['id' => $fromform->id]);

        file_save_draft_area_files($fromform->attachments, $context->id, 'mod_cpdlogbook', 'attachments', $fromform->id);

        // Trigger an entry_updated event.
        entry_updated::create_from_entry($entry, $context)->trigger();
    }

    redirect($url);
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
$record->fromdetails = $fromdetails;
$record->reflectionraw = ['text' => $record->reflection, 'format' => $record->reflectionformat];
$mform->set_data($record);
$mform->display();

echo $OUTPUT->footer();
