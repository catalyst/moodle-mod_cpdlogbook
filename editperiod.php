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
 * Edit page. Used to edit and create periods.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_cpdlogbook\form\edit_period;
use mod_cpdlogbook\persistent\period;

require_once('../../config.php');

// Get the course module id and the entry id from either the parameters or the hidden fields.
$id = required_param('id', PARAM_INT);
$create = optional_param('create', 'false', PARAM_BOOL);

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/editperiod.php', [ 'id' => $id, 'create' => $create ]));

if ($create) {
    // If an entry is being created.
    $period = new period();

    // If the form has been submitted and $id has been set to 0.
    if ($id == 0) {
        // Get the hidden cpdlogbookid field.
        $id = required_param('cpdlogbookid', PARAM_INT);
        $period->set('cpdlogbookid', $id);

        // Check that the cpdlogbook instance exists .
        $cpdlogbook = $DB->get_record('cpdlogbook', ['id' => $id], '*', MUST_EXIST);

        // Get the course module from the cpdlogbook instance.
        $cm = get_coursemodule_from_instance('cpdlogbook', $cpdlogbook->id, $cpdlogbook->course);
    } else {
        list ($course, $cm) = get_course_and_cm_from_cmid($id, 'cpdlogbook');
        $period->set('cpdlogbookid', $cm->instance);
    }
} else {
    // Get the existing entry.
    $period = new period($id);

    // Check that the cpdlogbook instance exists .
    $cpdlogbook = $DB->get_record('cpdlogbook', ['id' => $period->get('cpdlogbookid')], '*', MUST_EXIST);

    // Get the course module from the cpdlogbook instance.
    $cm = get_coursemodule_from_instance('cpdlogbook', $cpdlogbook->id, $cpdlogbook->course);
}

require_course_login($cm->course, false, $cm);

$context = context_module::instance($cm->id);

$mform = new edit_period($PAGE->url, [
    'persistent' => $period,
    'create' => $create,
]);

$url = new moodle_url('/mod/cpdlogbook/periods.php', ['id' => $cm->id]);

if ($mform->is_cancelled()) {
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    if ($create) {
        $newperiod = new period(0, $fromform);
        $newperiod->create();
    } else {
        // Update the record according to the submitted form data.
        $newperiod = new period($fromform->id, $fromform);
        $newperiod->update();
    }

    redirect($url);
}

// Set the title according to if an entry is being created or updated.
if ($create) {
    $title = get_string('createtitle', 'mod_cpdlogbook');
} else {
    $title = get_string('edit');
}

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo $id.PHP_EOL;
echo $create;

$mform->display();

echo $OUTPUT->footer();
