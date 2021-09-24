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
 * The mod_cpdlogbook entrydetails renderable.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpdlogbook\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class entrydetails
 *
 * @package mod_cpdlogbook
 */
class entrydetails implements renderable, templatable {

    /**
     * The completion percentage.
     * @var \stdClass
     */
    public $entry;

    /**
     * @var \stored_file[]
     */
    public $files;

    /**
     * progressbar constructor.
     *
     * @param \stdClass $entry
     * @param \stored_file[] $files
     */
    public function __construct($entry, $files) {
        $this->entry = $entry;
        $this->files = $files;
    }

    /**
     * Export the required data in the required format to be used in the mustache template.
     *
     * @param renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = $this->entry;
        $data->completiondate = userdate($data->completiondate, get_string('summarydate', 'mod_cpdlogbook'));
        $data->duration = format_time($data->duration);
        $data->summary = format_text($data->summary);
        $data->files = [];
        foreach ($this->files as $file) {
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(),
                    $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), true);
            $filedata = ['url' => $url, 'filename' => $file->get_filename()];
            $data->files[] = $filedata;
        }
        return $this->entry;
    }

}
