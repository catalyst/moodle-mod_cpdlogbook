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
 * The standard functions used by the plugin.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Check if a feature is supporte by the plugin.
 *
 * @param string $feature
 * @return bool|null
 */
function cpdlogbook_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Add a cpdlogbook module to a course.
 *
 * @param stdClass $cpdlogbook
 * @return int new cpdlogbook instance id
 * @throws dml_exception
 */
function cpdlogbook_add_instance($cpdlogbook) {
    global $DB;

    $cpdlogbook->id = $DB->insert_record('cpdlogbook', $cpdlogbook);

    return $cpdlogbook->id;
}

/**
 * Update an existing cpdlogbook instance.
 *
 * @param stdClass $cpdlogbook
 * @return bool
 * @throws dml_exception
 */
function cpdlogbook_update_instance($cpdlogbook) {
    global $DB;

    $cpdlogbook->id = $cpdlogbook->instance;

    return $DB->update_record('cpdlogbook', $cpdlogbook);
}

/**
 * Delete a cpdlogbook instance.
 *
 * @param int $id
 * @return bool
 * @throws dml_exception
 */
function cpdlogbook_delete_instance($id) {
    global $DB;

    $DB->delete_records('cpdlogbook_entries', ['cpdlogbookid' => $id]);

    $DB->delete_records('cpdlogbook', ['id' => $id]);

    return true;
}
