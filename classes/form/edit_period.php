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

use core\form\persistent;
use mod_cpdlogbook\persistent\period;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Class edit_period
 *
 * @package mod_cpdlogbook
 */
class edit_period extends persistent {

    /**
     * The class for the persistent.
     *
     * @var string
     */
    protected static $persistentclass = 'mod_cpdlogbook\\persistent\\period';

    /**
     * The fields to remove when creating the period.
     *
     * @var string[]
     */
    protected static $fieldstoremove = ['submitbutton', 'create'];

    /**
     * The definition of the form. Used to add elements and rules to the form.
     *
     * @throws \coding_exception
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'mod_cpdlogbook'));
        $mform->addRule('startdate', null, 'required', null, 'client');

        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'mod_cpdlogbook'));
        $mform->addRule('enddate', null, 'required', null, 'client');

        $mform->addElement('float', 'points', get_string('points', 'mod_cpdlogbook'));
        $mform->setType('points', PARAM_INT);
        $mform->addRule('points', null, 'required', null, 'client');

        $mform->addElement('hidden', 'id');

        $mform->addElement('hidden', 'cpdlogbookid');

        $mform->addElement('hidden', 'create');
        $mform->setType('create', PARAM_BOOL);
        $mform->setConstant('create', $this->_customdata['create']);

        $this->add_action_buttons();
    }

    /**
     * Additional validation. Checks for period overlaps.
     *
     * @param \stdClass $data
     * @param array $files
     * @param array $errors
     * @return array
     * @throws \coding_exception
     */
    protected function extra_validation($data, $files, array &$errors) {
        $newerrors = [];

        // If there aren't existing errors for these fields and they cause an overlap.
        if (period::overlaps(new period(0, $data))) {
            $newerrors['startdate'] = get_string('overlap', 'mod_cpdlogbook');
            $newerrors['enddate'] = get_string('overlap', 'mod_cpdlogbook');
        }

        return $newerrors;
    }
}
