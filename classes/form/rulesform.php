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

namespace mod_booking\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;

use context;
use context_system;
use core_form\dynamic_form;
use mod_booking\booking_rules\rules_info;
use moodle_url;
use moodleform;
use MoodleQuickForm;

/**
 * Dynamic rules form.
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @author Georg Maißer
 * @package mod_booking
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rulesform extends dynamic_form {

    /**
     * {@inheritdoc}
     * @see moodleform::definition()
     */
    public function definition() {

        $mform = $this->_form;

        $customdata = $this->_customdata;
        $ajaxformdata = $this->_ajaxformdata;

        // If we open an existing rule, we need to save the id right away.
        if (!empty($ajaxformdata['id'])) {
            $mform->addElement('hidden', 'id', $ajaxformdata['id']);

            $this->prepare_ajaxformdata($ajaxformdata);
        }

        $repeateloptions = [];

        rules_info::add_rules_to_mform($mform, $repeateloptions, $ajaxformdata);

        // As this form is called normally from a modal, we don't need the action buttons.
        // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        /* $this->add_action_buttons(); // Use $this, not $mform. */
    }

    /**
     * Process data for dynamic submission
     * @return object $data
     */
    public function process_dynamic_submission() {
        $data = parent::get_data();

        rules_info::save_booking_rule($data);

        return $data;
    }

    /**
     * Set data for dynamic submission.
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {

        if (!empty($this->_ajaxformdata['id'])) {
            $data = (object)$this->_ajaxformdata;
            $data = rules_info::set_data_for_form($data);
        } else {
            $data = (Object)$this->_ajaxformdata;
        }

        $this->set_data($data);

    }

    /**
     * Validate dates.
     *
     * {@inheritdoc}
     * @see moodleform::validation()
     */
    public function validation($data, $files) {
        $errors = [];

        return $errors;
    }


    /**
     * Get page URL for dynamic submission.
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/mod/booking/edit_rules.php');
    }

    /**
     * Get context for dynamic submission.
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return context_system::instance();
    }

    /**
     * Check access for dynamic submission.
     * @return void
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Prepare the ajax form data with all the information...
     * ... we need no have to load the form with the right handlers.
     *
     * @param array $ajaxformdata
     * @return void
     */
    private function prepare_ajaxformdata(array &$ajaxformdata) {

        global $DB;

        // If we have an ID, we retrieve the right rule from DB.
        $record = $DB->get_record('booking_rules', ['id' => $ajaxformdata['id']]);

        $jsonboject = json_decode($record->rulejson);

        if (empty($ajaxformdata['bookingruletype'])) {
            $ajaxformdata['bookingruletype'] = $jsonboject->rulename;
        }
        if (empty($ajaxformdata['bookingruleconditiontype'])) {
            $ajaxformdata['bookingruleconditiontype'] = $jsonboject->conditionname;
        }
        if (empty($ajaxformdata['bookingruleactiontype'])) {
            $ajaxformdata['bookingruleactiontype'] = $jsonboject->actionname;
        }
    }
}