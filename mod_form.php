<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_cpdlogbook_mod_form extends moodleform_mod {
    function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}