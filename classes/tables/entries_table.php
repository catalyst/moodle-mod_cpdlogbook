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

namespace mod_cpdlogbook\tables;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use moodle_url;
use table_sql;

class entries_table extends table_sql {

    public $cmid;

    public function __construct($cmid, $uniqueid) {
        parent::__construct($uniqueid);
        $this->cmid = $cmid;
    }

    /**
     * Format the column for the entry name.
     *
     * At the moment, this uses html_writer methods to add an update and delete button.
     *
     * @param $record
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function col_name($record) {
        $updateurl = new moodle_url('/mod/cpdlogbook/edit.php', ['cmid' => $this->cmid, 'id' => $record->id]);
        $deleteurl = new moodle_url(
                '/mod/cpdlogbook/delete.php',
                ['cmid' => $this->cmid, 'id' => $record->id, 'sesskey' => sesskey()]
        );
        return \html_writer::div(
            \html_writer::div($record->name).
            \html_writer::alist([
                \html_writer::link($updateurl, 'Update'),
                \html_writer::link($deleteurl, 'Delete'),
            ])
        );
    }

    public function col_user($record) {
        global $DB;
        return fullname($DB->get_record('user', ['id' => $record->user]));
    }

    public function col_time($record) {
        return userdate(time() - $record->time);
    }

    public function other_cols($column, $row) {
        return null;
    }
}
