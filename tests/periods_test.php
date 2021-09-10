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
 * The mod_cpdlogbook periods test.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpdlogbook;

use mod_cpdlogbook\persistent\period;

/**
 * A periods test class containing all the period tests.
 *
 * @package mod_cpdlogbook
 */
class periods_test extends \advanced_testcase {

    /**
     * Tests the period::get_period_for_date function.
     *
     * @throws \coding_exception
     * @throws \core\invalid_persistent_exception
     */
    public function test_getdate() {
        // Tell moodle that the database will be modified.
        $this->resetAfterTest(true);

        // Create the single period to test with.
        $data = new \stdClass();
        $data->cpdlogbookid = 0;
        $data->startdate = strtotime('2021-09-02');
        $data->enddate = strtotime('2021-09-10');
        $p1 = new period(0, $data);
        $p1->create();
        $id = $p1->get('id');
        $cpdlogbookid = $p1->get('cpdlogbookid');

        // Test dates where the period id should be returned.
        $this->assertEquals($id, period::get_period_for_date(strtotime('2021-09-02'), $cpdlogbookid));
        $this->assertEquals($id, period::get_period_for_date(strtotime('2021-09-10'), $cpdlogbookid));
        $this->assertEquals($id, period::get_period_for_date(strtotime('2021-09-05'), $cpdlogbookid));

        // Test dates where 0 should be returned.
        $this->assertEquals(0, period::get_period_for_date(strtotime('2021-09-01'), $cpdlogbookid));
        $this->assertEquals(0, period::get_period_for_date(strtotime('2021-09-11'), $cpdlogbookid));

        // Delete the period.
        $p1->delete();
    }
}
