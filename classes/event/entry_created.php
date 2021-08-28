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
 * The mod_cpdlogbook entry_created event.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpdlogbook\event;

use core\event\base;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Class entry_created.
 *
 * @package mod_cpdlogbook
 */
class entry_created extends base {


    /**
     * Create an entry_created event given an entry and a context.
     * @param \stdClass $entry
     * @param \context_module $context
     * @return base
     * @throws \coding_exception
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

    /**
     * Sets crud, edulevel and objecttable data for the event.
     *
     * @return void
     */
    public function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'cpdlogbook_entries';
    }

    /**
     * Returns the description of the event.
     *
     * @return string
     */
    public function get_description() {
        $a = new \stdClass();
        $a->userid = $this->get_data()['userid'];
        $a->entryid = $this->get_data()['objectid'];
        $a->name = $this->get_data()['other']['entryname'];

        return "The user with id '{$a->userid}' created the '{$a->name}' cpdlogbook entry with id '{$a->entryid}'.";
    }

    /**
     * Returns a link to the details page for this record created.
     *
     * @return moodle_url
     * @throws \moodle_exception
     */
    public function get_url() {
        $id = $this->get_data()['objectid'];
        return new moodle_url('/mod/cpdlogbook/details.php', ['id' => $id]);
    }

}
