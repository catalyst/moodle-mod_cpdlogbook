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

namespace mod_cpdlogbook\event;

class entry_updated extends entry_created {

    public function init() {
        parent::init();
        $this->data['crud'] = 'u';
    }

    public function get_description() {
        $a = new \stdClass();
        $a->userid = $this->get_data()['userid'];
        $a->entryid = $this->get_data()['objectid'];
        $a->name = $this->get_data()['other']['entryname'];

        return "The user with id '{$a->userid}' updated the '{$a->name}' cpdlogbook entry with id '{$a->entryid}'.";
    }
}
