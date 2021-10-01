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
 * Details page. Used to show entry details.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_cpdlogbook\event\entry_viewed;
use mod_cpdlogbook\output\entrydetails;

require_once('../../config.php');
require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);

// If the record doesn't exist, then require that the user is logged in before throwing an error.
if (! $record = $DB->get_record('cpdlogbook_entries', ['id' => $id], '*')) {
    require_login();
    throw new dml_missing_record_exception('cpdlogbook_entries');
};
$cpdlogbook = $DB->get_record('cpdlogbook', ['id' => $record->cpdlogbookid], '*', MUST_EXIST);

$cm = get_coursemodule_from_instance('cpdlogbook', $cpdlogbook->id, $cpdlogbook->course);

require_course_login($cpdlogbook->course, false, $cm);

// This can be changed for different capabilities.
// For example, a 'readall' capability could allow someone to see an entry even if they didn't create it.
if ($record->userid != $USER->id) {
    throw new moodle_exception('invalidaccess');
}

$context = context_module::instance($cm->id);
require_capability('mod/cpdlogbook:view', $context);

// Trigger the entry_viewed event.
entry_viewed::create_from_entry($record, $context)->trigger();

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/details.php', ['id' => $id]));
$PAGE->set_title($record->name);
$PAGE->set_heading($record->name);

$PAGE->navbar->add($record->name);

echo $OUTPUT->header();

$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_cpdlogbook', 'evidence', $record->id);
$entrydetails = new entrydetails($record, $files, $context->id);
echo $OUTPUT->render($entrydetails);

if (has_capability('mod/cpdlogbook:edit', $context)) {
    echo $OUTPUT->single_button(
            new moodle_url('/mod/cpdlogbook/edit.php', ['id' => $id, 'create' => false, 'fromdetails' => true]),
            get_string('edittitle', 'mod_cpdlogbook'), 'get', ['primary' => true]);
}

echo $OUTPUT->footer();
