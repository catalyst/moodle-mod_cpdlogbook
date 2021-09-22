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
 * The mod_cpdlogbook entries_table table.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpdlogbook\tables;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use action_link;
use action_menu;
use mod_cpdlogbook\persistent\period;
use moodle_url;
use renderer_base;
use table_sql;

/**
 * Class entries_table
 *
 * @package mod_cpdlogbook
 */
class entries_table extends table_sql {

    /**
     * @var renderer_base
     */
    public $output;

    /**
     * entries_table constructor.
     *
     * @param mixed $cm The course module.
     * @param string $userid The id for the user.
     * @param renderer_base  $output The output renderer to use.
     * @param string $download The download format. If not '', then only the columns to be downloaded are displayed.
     * @param int $periodid
     * @param string $uniqueid
     */
    public function __construct($cm, $userid, $output, $download, $periodid, $uniqueid) {
        global $DB;

        parent::__construct($uniqueid);

        $columns = [
            'points',
            'name',
            'duration',
            'provider',
            'location',
            'completiondate',
            'actions',
        ];

        if ($download) {
            $columns = [
                'points',
                'name',
                'summary',
                'duration',
                'provider',
                'location',
                'completiondate',
                'startdate',
                'enddate',
            ];
        }
        $this->sort_default_column = 'completiondate';
        $this->sort_default_order = SORT_DESC;

        $this->define_columns($columns);
        $this->column_class('completiondate',     'text-right');
        $this->column_class('points',   'text-right');
        $this->column_class('duration',    'text-right');
        $this->column_style('duration',    'text-wrap', 'none');
        $this->collapsible(false);

        $headers = [
            get_string('points', 'mod_cpdlogbook'),
            get_string('name', 'mod_cpdlogbook'),
            get_string('duration', 'mod_cpdlogbook'),
            get_string('provider', 'mod_cpdlogbook'),
            get_string('location', 'mod_cpdlogbook'),
            get_string('completiondate', 'mod_cpdlogbook'),
            get_string('actions'),
        ];

        if ($download) {
            $headers = [
                get_string('points', 'mod_cpdlogbook'),
                get_string('name', 'mod_cpdlogbook'),
                get_string('summary', 'mod_cpdlogbook'),
                get_string('duration', 'mod_cpdlogbook').' ('.get_string('hours').')',
                get_string('provider', 'mod_cpdlogbook'),
                get_string('location', 'mod_cpdlogbook'),
                get_string('completiondate', 'mod_cpdlogbook'),
                get_string('startdate', 'mod_cpdlogbook'),
                get_string('enddate', 'mod_cpdlogbook'),
            ];
        }

        $this->define_headers($headers);
        $this->show_download_buttons_at([TABLE_P_BOTTOM]);

        $this->output = $output;

        $record = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ]);

        if ($download) {
            $this->set_sql(
                'E.*, P.startdate, P.enddate',
                '{cpdlogbook_entries} E LEFT JOIN {cpdlogbook_periods} P ON E.periodid=P.id',
                'E.cpdlogbookid=? AND userid=?',
                [$record->id, $userid]
            );
        } else {
            $this->set_sql('*', '{cpdlogbook_entries}', 'cpdlogbookid=? AND userid=? AND periodid=?',
                [$record->id, $userid, $periodid]);
        }

        $this->no_sorting('actions');
    }

    /**
     * Format the name column.
     *
     * @param \stdClass $record
     * @return string
     * @throws \moodle_exception
     */
    public function col_name($record) {
        if ($this->download == '') {
            $html = \html_writer::link(new moodle_url('/mod/cpdlogbook/details.php',
                ['id' => $record->id]), $record->name);
            $html .= '<br>';
            $html .= format_text($record->summary);
            return $html;
        } else {
            return $record->name;
        }
    }

    /**
     * Format the userid column.
     *
     * @param \stdClass $record
     * @return \lang_string|string
     * @throws \dml_exception
     */
    public function col_userid($record) {
        global $DB;
        return fullname($DB->get_record('user', ['id' => $record->userid]));
    }

    /**
     * Format the completiondate column.
     *
     * @param \stdClass $record
     * @return string
     * @throws \coding_exception
     */
    public function col_completiondate($record) {
        if ($this->download == '') {
            return userdate($record->completiondate, get_string('summarydate', 'mod_cpdlogbook'));
        } else {
            // If the table is being downloaded, then export the time as a date in a friendly format.
            return userdate($record->completiondate, get_string('exportdate', 'mod_cpdlogbook'));
        }
    }

    /**
     * Format the actions column.
     *
     * @param \stdClass $record
     * @return bool|string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function col_actions($record) {
        $updateurl = new moodle_url('/mod/cpdlogbook/edit.php', ['id' => $record->id, 'create' => false]);
        $deleteurl = new moodle_url(
                '/mod/cpdlogbook/delete.php',
                ['id' => $record->id, 'sesskey' => sesskey()]
        );

        $editstr = get_string('edit');
        $deletestr = get_string('delete');

        // The pix_icons use default moodle icons.
        $menu = new action_menu();
        $menu->add(new \action_menu_link_primary($updateurl, new \pix_icon('i/edit', $editstr), $editstr));

        $deleteaction = new \confirm_action(get_string('confirmdelete', 'mod_cpdlogbook', $record->name));
        $delete = new action_link($deleteurl, '', $deleteaction, [], new \pix_icon('i/delete', $deletestr));
        $menu->add_primary_action($delete);

        return $this->output->render($menu);
    }

    /**
     * Format the duration column.
     *
     * @param \stdClass $record
     * @return \lang_string|string
     */
    public function col_duration($record) {
        // Only render this within a 'nobr' tag if the table isn't being downloaded.
        if ($this->download == '') {
            return \html_writer::tag('nobr', format_time($record->duration));
        } else {
            // If the table is being downloaded, then display the time in hours.
            return $record->duration / HOURSECS;
        }
    }

    /**
     * Format the points column
     *
     * @param \stdClass $record
     * @return string
     */
    public function col_points($record) {
        // Only display the badge if the table isn't being downloaded.
        if ($this->download == '') {
            return \html_writer::tag('span', $record->points, ['class' => 'badge badge-success']);
        } else {
            return $record->points;
        }
    }

    /**
     * Format the startdate column.
     *
     * @param \stdClass $record
     * @return string
     * @throws \coding_exception
     */
    public function col_startdate($record) {
        if ($record->startdate == 0) {
            return '';
        } else {
            return userdate($record->startdate, get_string('exportdate', 'mod_cpdlogbook'));
        }
    }

    /**
     * Format the enddate column.
     *
     * @param \stdClass $record
     * @return string
     * @throws \coding_exception
     */
    public function col_enddate($record) {
        if ($record->enddate == 0) {
            return '';
        } else {
            return userdate($record->enddate, get_string('exportdate', 'mod_cpdlogbook'));
        }
    }

    /**
     * The default format for all other columns.
     *
     * @param array|object $column
     * @param string $row
     * @return null
     */
    public function other_cols($column, $row) {
        return null;
    }
}
