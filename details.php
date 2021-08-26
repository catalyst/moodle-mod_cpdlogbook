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

$record = $DB->get_record('cpdlogbook_entries', ['id' => $id, 'userid' => $USER->id], '*', MUST_EXIST);
$cpdlogbook = $DB->get_record('cpdlogbook', ['id' => $record->cpdlogbookid], '*', MUST_EXIST);

$cm = get_coursemodule_from_instance('cpdlogbook', $cpdlogbook->id, $cpdlogbook->course);

require_course_login($cpdlogbook->course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/cpdlogbook:view', $context);

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/details.php', ['id' => $id]));
$PAGE->set_title($record->name);
$PAGE->set_heading($record->name);

$PAGE->navbar->add($record->name);

echo $OUTPUT->header();

if (has_capability('mod/cpdlogbook:edit', $context)) {
    echo $OUTPUT->render(
        new action_menu_link_primary(
            new moodle_url('/mod/cpdlogbook/edit.php', ['id' => $id, 'create' => false]),
            new pix_icon('i/edit', get_string('edit')),
            get_string('edit')
        )
    );
}

echo html_writer::alist([
    'Name: '.$record->name,
    'Time: '.$record->time,
    'Points: '.$record->points,
    'Hours: '.$record->hours,
    'Summary: '.$record->summary,
    'Provider: '.$record->provider,
    'Location: '.$record->location,
]);

echo $OUTPUT->footer();
