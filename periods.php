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
 * Periods page. Used to access the cpdlogbook periods.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_cpdlogbook\tables\periods_table;

require_once('../../config.php');

$id = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'cpdlogbook');

require_course_login($course, false, $cm);

$record = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ], '*', MUST_EXIST);

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/periods.php', [ 'id' => $id ]));

$title = get_string('periods', 'mod_cpdlogbook');
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

// Get the number of entries without a period.
$entries = $DB->get_records('cpdlogbook_entries', ['periodid' => 0]);
$count = count($entries);
// If there are any entries like this, display a notification.
if ($count != 0) {
    echo $OUTPUT->notification(
        get_string('invalidentries', 'mod_cpdlogbook', $count),
        \core\output\notification::NOTIFY_WARNING
    );
}

echo $OUTPUT->single_button(
    new moodle_url('/mod/cpdlogbook/editperiod.php', ['id' => $id, 'create' => true]),
    get_string('add')
);

$table = new periods_table($cm, $OUTPUT, 'cpdlogbook_periods');
$table->define_baseurl($PAGE->url);
$table->out(40, true);

echo $OUTPUT->footer();
