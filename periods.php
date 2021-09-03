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
 * View page. Used as the default page when viewing the activity.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_cpdlogbook\tables\periods_table;

require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$insert = optional_param('insert', false, PARAM_BOOL);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'cpdlogbook');

require_course_login($course, false, $cm);

$record = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ], '*', MUST_EXIST);

if ($insert) {
    $DB->insert_record('cpdlogbook_periods', [
        'startdate' => time(),
        'enddate' => strtotime('+1 month', time()),
        'cpdlogbookid' => $cm->instance,
    ]);
    redirect(new moodle_url('/mod/cpdlogbook/periods.php', [ 'id' => $id ]));
}

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/periods.php', [ 'id' => $id ]));

$PAGE->set_title(get_string('periods', 'mod_cpdlogbook'));
$PAGE->set_heading(get_string('periods', 'mod_cpdlogbook'));

echo $OUTPUT->header();

echo $OUTPUT->single_button(
    new moodle_url('/mod/cpdlogbook/periods.php', ['id' => $id, 'insert' => true]),
    get_string('add')
);

$table = new periods_table($cm, 'cpdlogbook_periods');
$table->define_baseurl($PAGE->url);
$table->out(40, true);

echo $OUTPUT->footer();
