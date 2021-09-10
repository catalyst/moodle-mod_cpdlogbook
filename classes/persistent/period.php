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
 * The mod_cpdlogbook persistent period class..
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpdlogbook\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Class period
 *
 * @package mod_cpdlogbook
 */
class period extends \core\persistent {

    /**
     * Table name for the persistent.
     */
    const TABLE = 'cpdlogbook_periods';

    /**
     * Defines the database fields for the period object.
     *
     * @return array[]
     */
    protected static function define_properties() {
        return [
            'startdate' => [
                'type' => PARAM_INT,
            ],
            'enddate' => [
                'type' => PARAM_INT,
            ],
            'cpdlogbookid' => [
                'type' => PARAM_INT,
            ]
        ];
    }

    /**
     * Validate the start date.
     *
     * @param int $value
     * @return bool
     * @throws \coding_exception
     */
    protected function validate_startdate($value) {
        $enddate = $this->raw_get('enddate');

        return $value < $enddate;
    }

    /**
     * Validate the end date.
     *
     * @param int $value
     * @return bool
     * @throws \coding_exception
     */
    protected function validate_enddate($value) {
        $startdate = $this->raw_get('startdate');

        return $startdate < $value;
    }

    /**
     * Finds a valid period for a given date and cpdlogbook instance.
     * Returns the id of the period if found, and 0 if no period is found.
     *
     * @param int $date
     * @param int $cpdlogbookid
     * @return int
     * @throws \coding_exception
     */
    public static function get_period_for_date($date, $cpdlogbookid) {
        global $DB;
        $record = $DB->get_record_sql(
            'SELECT * FROM {cpdlogbook_periods} WHERE startdate <= ? AND enddate >= ? AND cpdlogbookid = ?',
            [$date, $date, $cpdlogbookid]
        );

        return $record ? $record->id : 0;
    }
}
