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
 * The form displayed when adding the activity to a course.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Class mod_cpdlogbook_mod_form
 */
class mod_cpdlogbook_mod_form extends moodleform_mod {

    /**
     * The definition of the form. Used to add elements and rules to the form.
     *
     * @throws coding_exception
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'mod_cpdlogbook'));
        $mform->setType('name', PARAM_TEXT);

        $this->standard_intro_elements();

        $mform->addElement('float', 'totalpoints', get_string('points', 'mod_cpdlogbook'));

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
