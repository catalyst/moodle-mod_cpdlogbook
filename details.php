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

if (has_capability('mod/cpdlogbook:edit', $context)) {
    echo $OUTPUT->single_button(
            new moodle_url('/mod/cpdlogbook/edit.php', ['id' => $id, 'create' => false, 'fromdetails' => true]),
            get_string('edittitle', 'mod_cpdlogbook'), 'get', ['primary' => true]);
}

echo html_writer::alist([
    get_string('name', 'mod_cpdlogbook').': '.$record->name,
    get_string('completiondate', 'mod_cpdlogbook').': '
        .userdate($record->completiondate, get_string('summarydate', 'mod_cpdlogbook')),
    get_string('points', 'mod_cpdlogbook').': '.$record->points,
    get_string('duration', 'mod_cpdlogbook').': '.format_time($record->duration),
    get_string('summary', 'mod_cpdlogbook').': '.$record->summary,
    get_string('provider', 'mod_cpdlogbook').': '.$record->provider,
    get_string('location', 'mod_cpdlogbook').': '.$record->location,
    get_string('creationdate', 'mod_cpdlogbook').': '
        .userdate($record->creationdate, get_string('summarydate', 'mod_cpdlogbook')),
    get_string('modifieddate', 'mod_cpdlogbook').': '
        .userdate($record->modifieddate, get_string('summarydate', 'mod_cpdlogbook')),
]);

$fs = get_file_storage();
if ($files = $fs->get_area_files($context->id, 'mod_cpdlogbook', 'attachments', $record->id)) {
    foreach ($files as $file) {
        $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
        $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        $downloadurl = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path() . ':' 
        . $fileurl->get_port() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
        echo '<a href="' . $downloadurl . '">' . $file->get_filename() . '</a><br/>';
    }
    echo html_writer::div("There are attachments"); // For debugging.
} else {
    echo html_writer::div("No attachments");
}



echo $OUTPUT->footer();
