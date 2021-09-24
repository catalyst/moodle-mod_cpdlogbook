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
 * The mod_cpdlogbook progressbar renderable.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpdlogbook\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

/**
 * Class progressbar
 *
 * @package mod_cpdlogbook
 */
class progressbar implements renderable, templatable {

    /**
     * The completion percentage.
     * @var float
     */
    public $percent;

    /**
     * @var float
     */
    public $target;

    /**
     * progressbar constructor.
     *
     * @param float $percent
     * @param float $target
     */
    public function __construct($percent, $target) {
        $this->percent = $percent;
        $this->target = $target;
    }

    /**
     * Export the required data in the required format to be used in the mustache template.
     *
     * @param renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new \stdClass();
        $data->percent = format_float($this->percent);
        // Calculate the difference between the current percent and the target.
        $data->targetdiff = format_float($this->target - $this->percent);
        $data->hastarget = $data->targetdiff > 0;
        return $data;
    }

}
