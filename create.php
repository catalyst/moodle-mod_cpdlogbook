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

use mod_cpdlogbook\form\create_entry;

require_once('../../config.php');

// Get the course module id and the entry id from either the parameters or the hidden fields.
$cmid = required_param('cmid', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'cpdlogbook');

require_course_login($course, false, $cm);

if (! $record = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ])) {
    throw new moodle_exception('invalidentry');
};

$mform = new create_entry();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cmid]));
} else if ($fromform = $mform->get_data()) {
    // Update the record according to the submitted form data.

    $fromform->cpdlogbookid = $cm->instance;
    $fromform->userid = $USER->id;
    $fromform->time = time();

    $DB->insert_record('cpdlogbook_entries', $fromform);

    redirect(new moodle_url('/mod/cpdlogbook/view.php', ['id' => $cmid]));
}

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/edit.php', [ 'id' => $cmid ]));

$PAGE->set_title(get_string('createtitle', 'mod_cpdlogbook'));
$PAGE->set_heading(get_string('createtitle', 'mod_cpdlogbook'));

echo $OUTPUT->header();

$mform->set_data(['cmid' => $cmid]);
$mform->display();

echo $OUTPUT->footer();
