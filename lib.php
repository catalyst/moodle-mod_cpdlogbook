<?php

defined('MOODLE_INTERNAL') || die();

function cpdlogbook_add_instance($cpdlogbook) {
    global $DB;

    $cpdlogbook->id = $DB->insert_record('cpdlogbook', $cpdlogbook);

    return $cpdlogbook->id;
}