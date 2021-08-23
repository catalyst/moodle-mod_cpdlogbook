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

use core\event\base;

defined('MOODLE_INTERNAL') || die();

class entry_created extends base {

    /**
     * @param $entry \stdClass
     * @param $context \context_module
     */
    public static function create_from_entry($entry, $context) {
        $data = [
            'context' => $context,
            'objectid' => $entry->id,
            'other' => ['entryname' => $entry->name],
        ];
        $event = self::create($data);
        $event->add_record_snapshot('cpdlogbook_entries', $entry);
        return $event;
    }

    public function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'cpdlogbook_entries';
    }

    public function get_description() {
        $a = new \stdClass();
        $a->userid = $this->get_data()['userid'];
        $a->entryid = $this->get_data()['objectid'];
        $a->name = $this->get_data()['other']['entryname'];

        return "The user with id '{$a->userid}' created the '{$a->name}' cpdlogbook entry with id '{$a->entryid}'.";
    }

}
