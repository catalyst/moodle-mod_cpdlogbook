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
 * The mod_cpdlogbook periods_table table.
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
class periods_table extends table_sql {


    /**
     * entries_table constructor.
     *
     * @param mixed $cm The course module.
     * @param string $uniqueid
     */
    public function __construct($cm, $uniqueid) {
        global $DB;

        parent::__construct($uniqueid);

        $columns = [
            'startdate',
            'enddate',
        ];

        $this->sort_default_column = 'startdate';
        $this->sort_default_order = SORT_DESC;

        $this->define_columns($columns);
        $this->collapsible(false);

        $headers = [
            get_string('startdate', 'mod_cpdlogbook'),
            get_string('enddate', 'mod_cpdlogbook'),
        ];

        $this->define_headers($headers);
        $this->show_download_buttons_at([TABLE_P_BOTTOM]);

        $record = $DB->get_record('cpdlogbook', [ 'id' => $cm->instance ]);

        $this->set_sql('*', '{cpdlogbook_periods}', 'cpdlogbookid=?', [$record->id]);
    }

    /**
     * Formats the startdate column.
     *
     * @param \stdClass $record
     * @return string
     */
    public function col_startdate($record) {
        return userdate($record->startdate);
    }

    /**
     * Formats the enddate column.
     *
     * @param \stdClass $record
     * @return string
     */
    public function col_enddate($record) {
        return userdate($record->enddate);
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
