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
     * @param string  $output The output renderer to use.
     * @param renderer_base $download The download format. If not '', then only the columns to be downloaded are displayed.
     * @param string $uniqueid
     */
    public function __construct($cm, $userid, $output, $download, $uniqueid) {
        global $DB;

        parent::__construct($uniqueid);

        $columns = [
            'points',
            'name',
            'hours',
            'provider',
            'location',
            'time',
            'actions',
        ];

        if ($download) {
            $columns = [
                'points',
                'name',
                'summary',
                'hours',
                'provider',
                'location',
                'time',
            ];
        }
        $this->sort_default_column = 'time';
        $this->sort_default_order = SORT_DESC;

        $this->define_columns($columns);
        $this->column_class('time',     'text-right');
        $this->column_class('points',   'text-right');
        $this->column_class('hours',    'text-right');
        $this->column_style('hours',    'text-wrap', 'none');
        $this->collapsible(false);

        $headers = $columns;
        $this->define_headers($headers);
        $this->show_download_buttons_at([TABLE_P_BOTTOM]);

        $this->output = $output;

        $record = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ]);

        $this->set_sql('*', '{cpdlogbook_entries}', 'cpdlogbookid=? AND userid=?', [$record->id, $userid]);
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
     * Format the time column.
     *
     * @param \stdClass $record
     * @return string
     * @throws \coding_exception
     */
    public function col_time($record) {
        if ($this->download == '') {
            return userdate($record->time, get_string('summarydate', 'mod_cpdlogbook'));
        } else {
            // If the table is being downloaded, then export the time as a date in a friendly format.
            return userdate($record->time, get_string('exportdate', 'mod_cpdlogbook'));
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
     * Format the hours column.
     *
     * @param \stdClass $record
     * @return \lang_string|string
     */
    public function col_hours($record) {
        // Only render this within a 'nobr' tag if the table isn't being downloaded.
        if ($this->download == '') {
            return \html_writer::tag('nobr', format_time($record->hours));
        } else {
            // If the table is being downloaded, then display the time in hours.
            return $record->hours / HOURSECS;
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
