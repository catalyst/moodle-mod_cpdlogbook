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
 * The mod_cpdlogbook edit_period form.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpdlogbook\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Class edit_period
 *
 * @package mod_cpdlogbook
 */
class edit_period extends \moodleform {

    /**
     * The definition of the form. Used to add elements and rules to the form.
     *
     * @throws \coding_exception
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'mod_cpdlogbook'));
        $mform->setType('startdate', PARAM_TEXT);
        $mform->addRule('startdate', null, 'required', null, 'client');

        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'mod_cpdlogbook'));
        $mform->addRule('enddate', null, 'required', null, 'client');
        $mform->setType('enddate', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'create');
        $mform->setType('create', PARAM_BOOL);

        $this->add_action_buttons();
    }

}
