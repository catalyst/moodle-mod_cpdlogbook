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
 * Plugin capabilities.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jack Moloney <obnullref@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Handles updates for changes in the database.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_cpdlogbook_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2021080302) { // Updates field 'time' to 'completiondate'
        // Rename field time on table cpdlogbook_entries to completiondate.
        $table = new xmldb_table('cpdlogbook_entries');
        $field = new xmldb_field('time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Launch rename field time.
        $dbman->rename_field($table, $field, 'completiondate');

        upgrade_plugin_savepoint(true, 2021080302, 'mod', 'cpdlogbook');
    }

    if ($oldversion < 2021080303) { // Adds 'creationtime' field
        // Define field creationdate to be added to cpdlogbook_entries.
        $table = new xmldb_table('cpdlogbook_entries');
        $field = new xmldb_field('creationdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field creationdate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cpdlogbook savepoint reached.
        upgrade_mod_savepoint(true, 2021080303, 'cpdlogbook');
    }

    return true;
}
