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
 * Base class for a single booking option availability condition.
 *
 * All bo condition types must extend this class.
 *
 * @package mod_booking
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_booking\bo_availability\conditions;

use cache;
use context_system;
use mod_booking\bo_availability\bo_condition;
use mod_booking\bo_availability\bo_info;
use mod_booking\booking_option_settings;
use mod_booking\singleton_service;
use mod_booking\utils\wb_payment;
use MoodleQuickForm;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * This class takes the configuration from json in the available column of booking_options table.
 *
 * All bo condition types must extend this class.
 *
 * @package mod_booking
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customform implements bo_condition {

    /** @var int $id Id is set via json during construction but we still need a default ID */
    public $id = BO_COND_JSON_CUSTOMFORM;

    /** @var bool $overridable Indicates if the condition can be overriden. */
    public $overridable = true;

    /** @var stdClass $customsettings an stdclass coming from the json which passes custom settings */
    public $customsettings = null;

    /**
     * Constructor.
     *
     * @param integer $id
     * @return void
     */
    public function __construct(int $id = null) {

        if ($id) {
            $this->id = $id;
        }
    }

    /**
     * Needed to see if class can take JSON.
     * @return bool
     */
    public function is_json_compatible(): bool {
        return true; // Customizable condition.
    }

    /**
     * Needed to see if it shows up in mform.
     * @return bool
     */
    public function is_shown_in_mform(): bool {
        return true;
    }

    /**
     * Determines whether a particular item is currently available
     * according to this availability condition.
     * @param booking_option_settings $settings Item we're checking
     * @param int $userid User ID to check availability for
     * @param bool $not Set true if we are inverting the condition
     * @return bool True if available
     */
    public function is_available(booking_option_settings $settings, int $userid, bool $not = false): bool {

        // This is the return value. Not available to begin with.
        $isavailable = false;

        if (empty($this->customsettings->formsarray)) {
            $isavailable = true;
        } else {

            $cache = cache::make('mod_booking', 'customformuserdata');
            $cachekey = $userid . "_" . $settings->id . '_customform';

            if ($formdata = $cache->get($cachekey)) {
                // $isavailable = true;
            }
        }

        // If it's inversed, we inverse.
        if ($not) {
            $isavailable = !$isavailable;
        }

        return $isavailable;
    }

    /**
     * The hard block is complementary to the is_available check.
     * While is_available is used to build eg also the prebooking modals and...
     * ... introduces eg the booking policy or the subbooking page, the hard block is meant to prevent ...
     * ... unwanted booking. It's the check just before booking if we really...
     * ... want the user to book. It will return always return false on subbookings...
     * ... as they are not necessary, but return true when the booking policy is not yet answered.
     * Hard block is only checked if is_available already returns false.
     *
     * @param booking_option_settings $booking_option_settings
     * @param integer $userid
     * @return boolean
     */
    public function hard_block(booking_option_settings $settings, $userid):bool {

        $context = context_system::instance();
        if (has_capability('mod/booking:overrideboconditions', $context)) {
            return false;
        }
        return false;
    }

    /**
     * Obtains a string describing this restriction (whether or not
     * it actually applies). Used to obtain information that is displayed to
     * students if the activity is not available to them, and for staff to see
     * what conditions are.
     *
     * The $full parameter can be used to distinguish between 'staff' cases
     * (when displaying all information about the activity) and 'student' cases
     * (when displaying only conditions they don't meet).
     *
     * @param bool $full Set true if this is the 'full information' view
     * @param booking_option_settings $settings Item we're checking
     * @param int $userid User ID to check availability for
     * @param bool $not Set true if we are inverting the condition
     * @return array availability and Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_description(booking_option_settings $settings, $userid = null, $full = false, $not = false):array {

        $description = '';

        $isavailable = $this->is_available($settings, $userid, $not);

        $description = $this->get_description_string($isavailable, $full, $settings);

        return [$isavailable, $description, BO_PREPAGE_PREBOOK, BO_BUTTON_INDIFFERENT];
    }

    /**
     * Only customizable functions need to return their necessary form elements.
     *
     * @param MoodleQuickForm $mform
     * @param int $optionid
     * @param moodleform $moodleform
     * @return void
     */
    public function add_condition_to_mform(MoodleQuickForm &$mform, int $optionid = 0, $moodleform = null) {
        global $DB;

        // Check if PRO version is activated.
        if (wb_payment::pro_version_is_activated()) {

            $mform->addElement('checkbox', 'restrictwithcustomform',
                    get_string('restrictwithcustomform', 'mod_booking'));

            $formelementsarray = [
                0 => get_string('noelement', 'mod_booking'),
                'checkbox' => get_string('checkbox', 'mod_booking'),
                'static' => get_string('displaytext', 'mod_booking'),
                // phpcs:ignore moodle.Commenting.InlineComment.NotCapital,Squiz.PHP.CommentedOutCode.Found
                // 'shorttext' => get_string('shorttext', 'mod_booking'),
            ];

            // We add four potential elements.
            $counter = 1;
            $previous = 0;
            while ($counter < 3) {

                $buttonarray = array();

                if ($counter == 1) {
                    $formelementsarray = ['static' => get_string('displaytext', 'mod_booking')];
                } else if ($counter == 2) {
                    $formelementsarray = ['advcheckbox' => get_string('checkbox', 'mod_booking')];
                }

                // Create a select to chose which tpye of form element to display.
                $buttonarray[] =& $mform->createElement('select', 'bo_cond_customform_select_1_' . $counter,
                    get_string('formtype', 'mod_booking'), $formelementsarray);

                // We need to create all possible elements and hide them via "hideif" right now.

                if ($counter == 1) {
                    // Here we create the display-text element.
                    $buttonarray[] =& $mform->createElement('textarea', 'bo_cond_customform_value_1_' . $counter,
                        get_string('bo_cond_customform_label', 'mod_booking'), []);
                } else if ($counter == 2) {
                    $buttonarray[] =& $mform->createElement('text', 'bo_cond_customform_label_1_' . $counter,
                    get_string('bo_cond_customform_label', 'mod_booking'), []);

                    $mform->setType('bo_cond_customform_label_1_' . $counter, PARAM_TEXT);
                    // If the select is not currently on this element, we hide it.
                }

                $mform->addGroup($buttonarray, 'formgroupelement_1_' . $counter, 'Formgroup ' . $counter, '', [], false);
                $mform->hideIf('formgroupelement_1_' . $counter, 'restrictwithcustomform', 'notchecked');

                if (!empty($previous)) {
                    $mform->hideIf('formgroupelement_1_' . $counter,
                    'bo_cond_customform_select_1_' . $previous,
                    'eq', 0);
                }

                $previous = $counter;
                $counter++;
            }

        } else {
            // No PRO license is active.
            $mform->addElement('static', 'restrictwithcustomform',
                get_string('restrictwithcustomform', 'mod_booking'),
                get_string('proversiononly', 'mod_booking'));
        }

        // Workaround: Only show, if it is not turned off in the option form config.
        // We currently need this, because html elements do not show up in the option form config.
        // In expert mode, we always show everything.
        $showhorizontalline = true;
        $formmode = get_user_preferences('optionform_mode');
        if ($formmode !== 'expert') {
            $cfgrestrictwithcustomform = $DB->get_field('booking_optionformconfig', 'active',
                ['elementname' => 'restrictwithcustomform']);
            if ($cfgrestrictwithcustomform === "0") {
                $showhorizontalline = false;
            }
        }
        if ($showhorizontalline) {
            $mform->addElement('html', '<hr class="w-50"/>');
        }
    }

    /**
     * The page refers to an additional page which a booking option can inject before the booking process.
     * Not all bo_conditions need to take advantage of this. But eg a condition which requires...
     * ... the acceptance of a booking policy would render the policy with this function.
     *
     * @param int $optionid
     * @param int $userid optional user id
     * @return array
     */
    public function render_page(int $optionid, int $userid = 0) {

        $dataarray['data'] = [
            'optionid' => $optionid,
            // 'formsarray' => [],
        ];

        $returnarray = [
            // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
            /* 'json' => $jsonstring, */
            'data' => [$dataarray],
            'template' => 'mod_booking/condition/customform',
            'buttontype' => 1, // This means that the continue button is disabled.
        ];

        return $returnarray;
    }

    /**
     * Returns a condition object which is needed to create the condition JSON.
     *
     * @param stdClass $fromform
     * @return stdClass|null the object for the JSON
     */
    public function get_condition_object_for_json(stdClass $fromform): stdClass {

        // Remove the namespace from classname.
        $classname = __CLASS__;
        $classnameparts = explode('\\', $classname);
        $shortclassname = end($classnameparts); // Without namespace.

        $conditionobject = new stdClass;

        $conditionobject->id = BO_COND_JSON_CUSTOMFORM;
        $conditionobject->name = $shortclassname;
        $conditionobject->class = $classname;

        $conditionobject->formsarray = [];

        $formcounter = 1;
        $counter = 1;

        // In the future, we will allow for more than one custom form.
        // We create a new form.
        $newform = [];

        $key = 'bo_cond_customform_select_' . $formcounter . '_' . $counter;
        while (isset($fromform->{$key})) {

            $formobject = new stdClass();

            $formobject->formtype = $fromform->{$key};

            $key = 'bo_cond_customform_label_' . $formcounter . '_' . $counter;
            $formobject->label = $fromform->{$key} ?? null;

            $key = 'bo_cond_customform_value_' . $formcounter . '_' . $counter;
            $formobject->value = $fromform->{$key} ?? null;

            $newform[$counter] = $formobject;

            // If the next key is not there, we increase $formcounter, else $counter;
            $key = 'bo_cond_customform_select_' . $formcounter . '_' . $counter + 1;
            if (isset($fromform->{$key})) {
                $counter++;
            } else {

                // Make sure we start a new form and save this one.
                $conditionobject->formsarray[$formcounter] = $newform;
                $newform = [];
                $formcounter++;
            }

        }

        // Might be an empty object.
        return $conditionobject;
    }

    /**
     * Set default values to be shown in form when loaded from DB.
     * @param stdClass &$defaultvalues the default values
     * @param stdClass $acdefault the condition object from JSON
     */
    public function set_defaults(stdClass &$defaultvalues, stdClass $acdefault) {

        if (!empty($acdefault->formsarray)) {
            $defaultvalues->restrictwithcustomform = 1;
        }

        foreach ($acdefault->formsarray as $formcounter => $form) {

            foreach ($form as $counter => $formelement) {

                $key = 'bo_cond_customform_select_' . $formcounter . '_' . $counter;
                $defaultvalues->{$key} = $formelement->formtype;

                $key = 'bo_cond_customform_label_' . $formcounter . '_' . $counter;
                $defaultvalues->{$key} = $formelement->label;

                $key = 'bo_cond_customform_value_' . $formcounter . '_' . $counter;
                $defaultvalues->{$key} = $formelement->value;

            }

        }
    }

    /**
     * Some conditions (like price & bookit) provide a button.
     * Renders the button, attaches js to the Page footer and returns the html.
     * Return should look somehow like this.
     * ['mod_booking/bookit_button', $data];
     *
     * @param booking_option_settings $settings
     * @param int $userid
     * @param boolean $full
     * @param boolean $not
     * @return array
     */
    public function render_button(booking_option_settings $settings,
        $userid = 0, $full = false, $not = false, bool $fullwidth = true): array {

        $label = $this->get_description_string(false, $full, $settings);

        return bo_info::render_button($settings, $userid, $label, 'alert alert-warning', true, $fullwidth, 'alert', 'option');
    }

    /**
     * Helper function to return localized description strings.
     *
     * @param bool $isavailable
     * @param bool $full
     * @param booking_option_settings $settings
     * @return string
     */
    private function get_description_string($isavailable, $full, $settings) {
        if ($isavailable) {
            $description = $full ? get_string('bo_cond_userprofilefield_full_available', 'mod_booking') :
                get_string('bo_cond_userprofilefield_available', 'mod_booking');
        } else {

            if (!$this->customsettings) {
                // This description can only works with the right custom settings.
                $availabilityarray = json_decode($settings->availability);

                foreach ($availabilityarray as $availability) {
                    if (strpos($availability->class, 'userprofilefield_1_default') > 0) {

                        $this->customsettings = (object)$availability;
                    }
                }
            }

            $description = $full ? get_string('bo_cond_userprofilefield_full_not_available',
                'mod_booking',
                $this->customsettings) :
                get_string('bo_cond_userprofilefield_not_available', 'mod_booking');
        }
        return $description;
    }

    /**
     * This static functions checks if the user has saved something in customform.
     * If so, we add it to the json column in booking_answers.
     * @param stdClass $newanswer
     * @return void
     */
    public static function add_json_to_booking_answer(stdClass &$newanswer, int $userid) {

        global $USER;

        $settings = singleton_service::get_instance_of_booking_option_settings($newanswer->optionid);

        $index = strpos( $settings->availability, '"id":16,"name":"customform"');

        if (empty($settings->availability) || $index === false) {
            return;
        }

        $cache = cache::make('mod_booking', 'customformuserdata');
        $cachekey = $userid . "_" . $settings->id . '_customform';

        // Only if we find the form in cache, we save it to the answer.
        // We can just overwrite any preivous answer.
        if ($data = $cache->get($cachekey)) {

            $data = (object)[
                "condition_customform" => $data
            ];

            $newanswer->json = json_encode($data);
        }
    }
}