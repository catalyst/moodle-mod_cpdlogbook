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
require_once($CFG->libdir.'/tablelib.php');

$id = required_param('id', PARAM_INT);
$insert = optional_param('insert', false, PARAM_BOOL);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'cpdlogbook');

require_course_login($course, false, $cm);

$record = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ]);

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/view.php', [ 'id' => $id ]));
$PAGE->set_title($record->name);
$PAGE->set_heading($record->name);

if ($insert) {
    $DB->insert_record('cpdlogbook_entries', [
            'user' => $USER->id,
            'cpdlogbook' => $record->id,
            'name' => 'test',
            'time' => time(),
    ]);
    redirect($PAGE->url);
}

echo $OUTPUT->header();

echo html_writer::alist([
        'id' => $record->id,
        'course' => $record->course,
        'name' => $record->name,
        'totalpoints' => $record->totalpoints,
        'info' => $record->intro,
        'introformat' => $record->introformat,
]);

echo html_writer::link($PAGE->url.'&insert=true', 'Insert a record');

$table = new table_sql('cpdlogbook_id');
$table->set_sql('*', '{cpdlogbook_entries}', 'cpdlogbook=? AND user=?', [$record->id, $USER->id]);
$table->define_baseurl($PAGE->url);
$table->out(40, true);

echo $OUTPUT->footer();
