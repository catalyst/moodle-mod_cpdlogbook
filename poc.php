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

require_once('../../config.php');

require_login();
$PAGE->set_context(context_system::instance());

$start = optional_param('start', '', PARAM_TEXT);
$target = optional_param('target', '', PARAM_TEXT);
$diff = optional_param('diff', '-1 month', PARAM_TEXT);

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/poc.php'));

$PAGE->set_title('Proof of Concept');
$PAGE->set_heading('Proof of Concept');

echo $OUTPUT->header();

// Output a simple form using html_writer for simplicity.
echo html_writer::tag(
    'form',
    html_writer::tag('input', '', ['name' => 'start', 'value' => $start]).
    html_writer::tag('input', '', ['name' => 'target', 'value' => $target]).
    html_writer::tag('input', '', ['name' => 'diff', 'value' => $diff]).
    html_writer::tag('button', 'Submit', ['type' => 'submit'])
);

// Calculate the given start time.
$starttime = strtotime($start);
$time = $starttime;

$targettime = strtotime($target);

// If there is a 'goal' date.
if ($targettime != false) {

    // Search for the period in the future or in the past as necessary.
    if ($targettime < $time) {
        // While the variable $time is after $targettime, move it back by $diff.
        // A for loop is used to avoid an infinite while loop for this testing page.
        for ($i = 0; $targettime < $time && $i < 10000; $i++) {
            $time = strtotime('-' . $diff, $time);
        }
    } else {
        for ($i = 0; $targettime > $time && $i < 10000; $i++) {
            $time = strtotime('+' . $diff, $time);
        }
        // Since the for loop finds the start of the proceeding time period, the time must be reduced.
        $time = strtotime('-' . $diff, $time);
    }
}

// Now, $t should be the start of the period for $date.
echo html_writer::alist([
    $start,
    $target,
    'start: '.userdate($starttime),
    'next: '.userdate(strtotime('+'.$diff, $starttime)),
    'goal: '.userdate($time),
]);

echo $OUTPUT->footer();
