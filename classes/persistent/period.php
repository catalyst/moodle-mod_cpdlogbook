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

use coding_exception;

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
                'null' => NULL_NOT_ALLOWED,
            ],
            'enddate' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
            ],
            'cpdlogbookid' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
            ]
        ];
    }

    /**
     * Validate the start date.
     *
     * @param int $value
     * @return true|\lang_string
     * @throws \coding_exception
     */
    protected function validate_startdate($value) {
        $enddate = $this->raw_get('enddate');

        if ($value < $enddate) {
            return true;
        } else {
            return new \lang_string('startendinvalid', 'mod_cpdlogbook');
        }
    }

    /**
     * Validate the end date.
     *
     * @param int $value
     * @return true|\lang_string
     * @throws \coding_exception
     */
    protected function validate_enddate($value) {
        $startdate = $this->raw_get('startdate');

        if ($startdate < $value) {
            return true;
        } else {
            return new \lang_string('startendinvalid', 'mod_cpdlogbook');
        }
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

    /**
     * After a record has been updated.
     *
     * @param bool $result
     */
    protected function after_update($result) {
        // Only update the entries if the update was successful.
        if ($result) {
            $this->update_entries();
        }
    }

    /**
     * After a record has been created.
     */
    protected function after_create() {
        $this->update_entries();
    }

    /**
     * Updates entries before deleting the period.
     *
     * @throws \dml_exception
     * @throws coding_exception
     */
    protected function before_delete() {
        global $DB;
        $id = $this->get('id');
        $cpdlogbookid = $this->get('cpdlogbookid');
        $DB->execute(
                'UPDATE {cpdlogbook_entries} SET periodid = 0 WHERE periodid = ? AND cpdlogbookid = ?',
                [ $id, $cpdlogbookid ]
        );
    }

    /**
     * Updates entries so that entries that used to belong to this period are unassociated, and then all entries that now apply
     * are updated to reflect that.
     *
     * @throws \dml_exception
     * @throws coding_exception
     */
    public function update_entries() {
        global $DB;
        $id = $this->get('id');
        $cpdlogbookid = $this->get('cpdlogbookid');
        // Remove the association with existing entries.
        $DB->execute(
            'UPDATE {cpdlogbook_entries} SET periodid = 0 WHERE periodid = ? AND cpdlogbookid = ?',
            [ $id, $cpdlogbookid ]
        );
        // Update existing periods that now fall within this period.
        $DB->execute(
            'UPDATE {cpdlogbook_entries} SET periodid = ? WHERE completiondate <= ? AND completiondate >= ? AND cpdlogbookid = ?',
            [ $id, $this->get('enddate'), $this->get('startdate'), $cpdlogbookid ]
        );
    }

    /**
     * Checks if a given period overlaps with existing periods.
     *
     * @param period $period
     * @return boolean
     * @throws \coding_exception
     */
    public static function overlaps($period) {
        $records = self::get_records(['cpdlogbookid' => $period->get('cpdlogbookid')]);
        foreach ($records as $record) {
            if ($record->get('id') != $period->get('id')) {
                // Is the period before the record?
                $before = $period->get('enddate') < $record->get('startdate');

                // Is the period after the record?
                $after = $period->get('startdate') > $record->get('enddate');

                if (!$before && !$after) {
                    return true;
                }
            }
        }
        return false;
    }
}
