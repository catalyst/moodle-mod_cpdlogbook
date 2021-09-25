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
 * Plugin strings.
 *
 * @package mod_cpdlogbook
 * @copyright 2021 Jordan Shatte <jsha773@hotmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Required activity strings.
$string['cpdlogbook:addinstance'] = 'Add an instance';
$string['modulename'] = 'CPD Logbook';
$string['modulenameplural'] = 'CPD Logbooks';
$string['pluginadministration'] = 'Administration';
$string['pluginname'] = 'CPD Logbook';

// Headers and column names.
$string['attachments'] = 'Attachments';
$string['completiondate'] = 'Completion Date';
$string['creationdate'] = 'Creation Date';
$string['duration'] = 'Duration';
$string['enddate'] = 'End date';
$string['location'] = 'Location';
$string['modifieddate'] = 'Modified Date';
$string['name'] = 'Name';
$string['periods'] = 'Periods';
$string['points'] = 'Points';
$string['provider'] = 'Provider';
$string['reflection'] = 'Reflection';
$string['startdate'] = 'Start date';
$string['summary'] = 'Summary';

// Date format strings.
$string['exportdate'] = '%Y-%m-%d';
$string['summarydate'] = '%a, %d %b %Y';

// Entry CRUD titles.
$string['confirmdelete'] = 'Are you sure you want to delete entry "{$a}"?';
$string['createtitle'] = 'Create a new entry';
$string['edittitle'] = 'Edit this entry';

// Errors and invalid data.
$string['daterestriction'] = 'This date must be between {$a->start} and {$a->end}.';
$string['invalidentries'] = 'There are {$a} entries without an appropriate period.';
$string['lessthanzero'] = 'This value cannot be less than zero.';
$string['nocurrentperiod'] = 'There is no period for today, so no entries can be added.';
$string['overlap'] = 'This overlaps with an existing period';
$string['startendinvalid'] = 'Start date after end date';

// Period information.
$string['currentperiod'] = 'Current period';
$string['noperiod'] = 'No period';

// Miscellaneous.
$string['pointratio'] = '{$a->sum} / {$a->required} points';
