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

namespace mod_booking\table;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../lib.php');
require_once($CFG->libdir.'/tablelib.php');

use mod_booking\optiondates_handler;
use table_sql;

defined('MOODLE_INTERNAL') || die();

/**
 * Report table to show an individual performance report
 * of a specific teacher (performed units).
 */
class teacher_performed_units_table extends table_sql {

    /**
     * Constructor
     * @param string $uniqueid all tables have to have a unique id, this is used
     */
    public function __construct(string $uniqueid) {
        parent::__construct($uniqueid);

        global $PAGE;
        $this->baseurl = $PAGE->url;

        // For German use "," as comma and " " as thousands separator.
        if (current_language() == "de") {
            $this->decimal_separator = ",";
            $this->thousands_separator = " ";
        } else {
            // In all other cases, we use the default separators.
            $this->decimal_separator = ".";
            $this->thousands_separator = ",";
        }

        // Columns and headers are not defined in constructor, in order to keep things as generic as possible.
    }

    /**
     * This function is called for each data row to allow processing of the
     * optiondateid value.
     *
     * @param object $values Contains object with all the values of record.
     * @return string $string Rendered date and time of the optiondate.
     * @throws dml_exception
     */
    public function col_optiondate(object $values): string {

        return optiondates_handler::prettify_optiondates_start_end($values->coursestarttime,
            $values->courseendtime, current_language());

    }

    /**
     * This function is called for each data row to allow processing of the
     * coursestarttime value.
     *
     * @param object $values Contains object with all the values of record.
     * @return string $coursestarttime Returns course start time as a readable string.
     * @throws coding_exception
     */
    public function col_coursestarttime($values) {
        // Prepare date string.
        if ($values->coursestarttime != 0) {
            $coursestarttime = date("Y-m-d H:i", $values->coursestarttime);
        } else {
            $coursestarttime = '';
        }

        return $coursestarttime;
    }

    /**
     * This function is called for each data row to allow processing of the
     * courseendtime value.
     *
     * @param object $values Contains object with all the values of record.
     * @return string $courseendtime Returns course end time as a readable string.
     * @throws coding_exception
     */
    public function col_courseendtime($values) {
        // Prepare date string.
        if ($values->courseendtime != 0) {
            $courseendtime = date("Y-m-d H:i", $values->courseendtime);
        } else {
            $courseendtime = '';
        }

        return $courseendtime;
    }

    /**
     * This function is called for each data row to allow processing of the
     * titleprefix value.
     *
     * @param object $values Contains object with all the values of record.
     * @return string $titleprefix Returns titleprefix as a readable string.
     * @throws coding_exception
     */
    public function col_titleprefix($values) {
        // Prepare titleprefix string.
        if (!empty($values->titleprefix)) {
            return $values->titleprefix;
        } else {
            return '';
        }
    }

    /**
     * This function is called for each data row to allow processing of the
     * col_duration_units value.
     *
     * @param object $values Contains object with all the values of record.
     * @return string $titleprefix Returns titleprefix as a readable string.
     * @throws coding_exception
     */
    public function col_duration_units($values) {
        return number_format($values->duration_units, 1, $this->decimal_separator, $this->thousands_separator);
    }
}