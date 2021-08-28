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
 * The mod_cpdlogbook renderer.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpdlogbook\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * Class renderer
 *
 * @package mod_cpdlogbook
 */
class renderer extends plugin_renderer_base {

    /**
     * Render a simple progress bar
     *
     * @param progressbar $progressbar
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_progressbar(progressbar $progressbar) {
        $data = $progressbar->export_for_template($this);
        return parent::render_from_template('mod_cpdlogbook/progressbar', $data);
    }

}
