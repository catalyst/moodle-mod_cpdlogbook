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

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $cpdlogbooknode
 * @return void
 */
function cpdlogbook_extend_settings_navigation($settings, $cpdlogbooknode) {
    global $PAGE;
    $cpdlogbooknode->add(
        get_string('periods', 'mod_cpdlogbook'),
        new moodle_url('/mod/cpdlogbook/periods.php', ['id' => $PAGE->cm->id])
    );
}

/**
 * This function is a required callback function
 * From the File_API documentation (https://docs.moodle.org/dev/File_API):
 *
 * Serve the files from the MYPLUGIN file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not to force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function mod_cpdlogbook_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    if ($filearea != 'attachments') {
        return false;
    }

    require_login($course, true, $cm);

    if (!has_capability('mod/cpdlogbook:view', $context)) {
        return false;
    }

    $itemid = array_shift($args);

    $fs = get_file_storage();

    $filename = array_pop($args);
    $filepath = '';
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }

    $file = $fs->get_file($context->id, 'mod_cpdlogbook', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    send_stored_file($file, 0, 0, true, $options);
}
