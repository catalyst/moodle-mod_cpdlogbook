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

    if ($oldversion < 2021080303) { // Adds 'creationdate' field
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

    if ($oldversion < 2021080304) { // Adds 'modifieddate' field
        // Define field modifieddate to be added to cpdlogbook_entries.
        $table = new xmldb_table('cpdlogbook_entries');
        $field = new xmldb_field('modifieddate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'creationdate');

        // Conditionally launch add field modifieddate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cpdlogbook savepoint reached.
        upgrade_mod_savepoint(true, 2021080304, 'cpdlogbook');
    }

    if ($oldversion < 2021080305) { // Renames hours to duration
        // Rename field time on table cpdlogbook_entries to duration.
        $table = new xmldb_table('cpdlogbook_entries');
        $field = new xmldb_field('hours', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '3600', 'name');

        // Launch rename field hours.
        $dbman->rename_field($table, $field, 'duration');

        // Cpdlogbook savepoint reached.
        upgrade_mod_savepoint(true, 2021080305, 'cpdlogbook');
    }

    if ($oldversion < 2021080306) {

        // Define table cpdlogbook_periods to be created.
        $table = new xmldb_table('cpdlogbook_periods');

        // Adding fields to table cpdlogbook_periods.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('startdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enddate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cpdlogbookid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table cpdlogbook_periods.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('cpdlogbookid', XMLDB_KEY_FOREIGN, ['cpdlogbookid'], 'cpdlogbook', ['id']);

        // Conditionally launch create table for cpdlogbook_periods.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cpdlogbook savepoint reached.
        upgrade_mod_savepoint(true, 2021080306, 'cpdlogbook');
    }

    if ($oldversion < 2021080307) {

        // Define field periodid to be added to cpdlogbook_entries.
        $table = new xmldb_table('cpdlogbook_entries');
        $field = new xmldb_field('periodid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'userid');
        // Define key periodid (foreign) to be added to cpdlogbook_entries.
        $key = new xmldb_key('periodid', XMLDB_KEY_FOREIGN, ['periodid'], 'cpdlogbook_periods', ['id']);

        // Conditionally launch add field periodid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Launch add key periodid.
        $dbman->add_key($table, $key);

        // Cpdlogbook savepoint reached.
        upgrade_mod_savepoint(true, 2021080307, 'cpdlogbook');
    }

    if ($oldversion < 2021080308) {

        // Define field usermodified to be added to cpdlogbook_periods.
        $table = new xmldb_table('cpdlogbook_periods');
        $field = new xmldb_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'cpdlogbookid');

        // Conditionally launch add field usermodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field timecreated to be added to cpdlogbook_periods.
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'usermodified');

        // Conditionally launch add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field timemodified to be added to cpdlogbook_periods.
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cpdlogbook savepoint reached.
        upgrade_mod_savepoint(true, 2021080308, 'cpdlogbook');
    }

    if ($oldversion < 2021080309) {

        // Define field points to be added to cpdlogbook_periods.
        $table = new xmldb_table('cpdlogbook_periods');
        $field = new xmldb_field('points', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field points.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cpdlogbook savepoint reached.
        upgrade_mod_savepoint(true, 2021080309, 'cpdlogbook');
    }

    if ($oldversion < 2021080310) { // Adds reflection field to logbook entries.
        // Define field reflection to be added to cpdlogbook_entries.
        $table = new xmldb_table('cpdlogbook_entries');
        $field = new xmldb_field('reflection', XMLDB_TYPE_TEXT, null, null, null, null, null, 'summary');

        // Conditionally launch add field reflection.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field reflectionformat to be added to cpdlogbook_entries.
        $field = new xmldb_field('reflectionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'reflection');

        // Conditionally launch add field reflectionformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cpdlogbook savepoint reached.
        upgrade_mod_savepoint(true, 2021080310, 'cpdlogbook');
    }

    return true;
}
