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

use mod_cpdlogbook\output\progressbar;
use mod_cpdlogbook\persistent\period;
use mod_cpdlogbook\tables\entries_table;
use mod_cpdlogbook\event\course_module_viewed;

require_once('../../config.php');
require_once($CFG->libdir.'/tablelib.php');

$id = required_param('id', PARAM_INT);
$download = optional_param('download', false, PARAM_ALPHA);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'cpdlogbook');

require_course_login($course, false, $cm);

$record = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ], '*', MUST_EXIST);

// Trigger the course_module_viewed event.
$eventdata = ['context' => context_module::instance($id), 'objectid' => $record->id];
course_module_viewed::create($eventdata)->trigger();

$PAGE->set_url(new moodle_url('/mod/cpdlogbook/view.php', [ 'id' => $id ]));

if (!$download) {
    // If the table is not being downloaded, render the normal page.
    $PAGE->set_title($record->name);
    $PAGE->set_heading($record->name);

    echo $OUTPUT->header();

    $periodid = period::get_period_for_date(time(), $cm->instance);

    if ($periodid != 0) {
        $period = new period($periodid);

        $sum = $period->total_entry_points();

        // Output the points as a ratio, ie. 5 / 20.
        $a = new stdClass();
        $a->sum = $sum;
        $a->required = $period->get('points');
        echo html_writer::tag('p', get_string('pointratio', 'mod_cpdlogbook', $a), ['class' => 'h2']);

        // Output the points as a progress bar towards completion.
        $percent = 100 * $sum / $period->get('points');
        // Clamp the percentage to be between 100 and 0.
        if ($percent > 100) {
            $percent = 100;
        } else if ($percent < 0) {
            // The percent should never be less than 0, but is still clamped just in case.
            $percent = 0;
        }

        // The target percentage given the current time.
        $target = (time() - $period->get('startdate')) / ($period->get('enddate') - $period->get('startdate'));
        $a->sum = format_float($target * $period->get('points'));
        echo html_writer::tag('p', get_string('targetpoints', 'mod_cpdlogbook', $a));

        $progressbar = new progressbar($percent, 100 * $target);
        echo $OUTPUT->render($progressbar);
    }

    // Formats URLs inside intro to properly display images.
    $formattedintro = file_rewrite_pluginfile_urls($record->intro, 'pluginfile.php', context_module::instance($id)->id,
        'mod_cpdlogbook', 'intro', null);
    echo format_text($formattedintro, $record->introformat);

    echo $OUTPUT->single_button(
            new moodle_url('/mod/cpdlogbook/edit.php', ['id' => $id, 'create' => true]),
            get_string('createtitle', 'mod_cpdlogbook'), 'get', ['primary' => true]);

    $format = get_string('summarydate', 'mod_cpdlogbook');
    $periods = period::get_records(['cpdlogbookid' => $cm->instance], 'startdate', 'DESC');
    // Create a table for each period.
    foreach ($periods as $period) {
        echo html_writer::tag('h2', userdate($period->get('startdate'), $format).' - '.userdate($period->get('enddate'), $format));
        $table = new entries_table($cm, $USER->id, $OUTPUT, $download, $period->get('id'), 'cpdlogbook_id_'.$period->get('id'));
        $table->is_downloading($download);

        $table->define_baseurl($PAGE->url);
        $table->out(40, true);

        echo html_writer::div('', 'mt-5');
    }

    // Create an additional 'unassigned' table.
    echo html_writer::tag('h2', get_string('noperiod', 'mod_cpdlogbook'));
    $table = new entries_table($cm, $USER->id, $OUTPUT, $download, 0, 'cpdlogbook_id_0');
    $table->is_downloading($download);
    $table->define_baseurl($PAGE->url);
    $table->out(40, true);

    echo $OUTPUT->footer();
} else {
    // If the table is being downloaded, then the only show the required columns.
    $table = new entries_table($cm, $USER->id, $OUTPUT, $download, -1, 'cpdlogbook_id');

    $filename = $record->name . ' - ' . fullname($USER) . ' - '
            . userdate(time(), get_string('strftimedate', 'langconfig'));
    $table->is_downloading($download, $filename, 'cpdlogbook');
    $table->define_baseurl($PAGE->url);

    $table->out(40, true);
}
