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

use action_menu;
use moodle_url;
use renderer_base;
use table_sql;

class entries_table extends table_sql {

    public $cmid;
    public $output;

    /**
     * entries_table constructor.
     *
     * @param $cmid mixed The course module id.
     * @param $cpdlogbookid string The id for the cpdlogbook.
     * @param $userid string The id for the user.
     * @param $output renderer_base The output renderer to use.
     * @param $uniqueid
     */
    public function __construct($cmid, $cpdlogbookid, $userid, $output, $uniqueid) {
        parent::__construct($uniqueid);

        $columns =
            ['id', 'cpdlogbookid', 'userid', 'time', 'name', 'hours', 'points', 'provider', 'location', 'summary', 'actions'];
        $this->define_columns($columns);

        $headers = $columns;
        $this->define_headers($headers);

        $this->cmid = $cmid;
        $this->output = $output;

        $this->set_sql('*', '{cpdlogbook_entries}', 'cpdlogbookid=? AND userid=?', [$cpdlogbookid, $userid]);

    }

    public function col_userid($record) {
        global $DB;
        return fullname($DB->get_record('user', ['id' => $record->userid]));
    }

    public function col_time($record) {
        return userdate(time() - $record->time);
    }

    public function col_actions($record) {
        $updateurl = new moodle_url('/mod/cpdlogbook/edit.php', ['cmid' => $this->cmid, 'id' => $record->id]);
        $deleteurl = new moodle_url(
                '/mod/cpdlogbook/delete.php',
                ['cmid' => $this->cmid, 'id' => $record->id, 'sesskey' => sesskey()]
        );

        $editstr = get_string('edit');
        $deletestr = get_string('delete');

        // The pix_icons use default moodle icons.
        $menu = new action_menu();
        $menu->add(new \action_menu_link_primary($updateurl, new \pix_icon('i/edit', $editstr), $editstr));
        $menu->add(new \action_menu_link_primary($deleteurl, new \pix_icon('i/delete', $deletestr), $deletestr));

        return $this->output->render($menu);
    }

    public function other_cols($column, $row) {
        return null;
    }
}
