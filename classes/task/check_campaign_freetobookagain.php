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
 * Adhoc Task to check if campaign transitions free up places and trigger the freetobookagain event.
 *
 * When a campaign starts or ends, the number of available places (maxanswers) may change.
 * If a booking option was fully booked before the campaign transition but has free places
 * after the transition, we need to trigger the bookingoption_freetobookagain event so that
 * waitlist notifications and booking rules are executed.
 *
 * @package mod_booking
 * @copyright 2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_booking\task;

use cache_helper;
use mod_booking\booking_answers\booking_answers;
use mod_booking\booking_option;
use mod_booking\singleton_service;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/booking/lib.php');

/**
 * Class to handle adhoc Task to check for freetobookagain on campaign transitions.
 *
 * @package mod_booking
 * @copyright 2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_campaign_freetobookagain extends \core\task\adhoc_task {
    /**
     * Get the task name.
     *
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function get_name() {
        return get_string('taskcheckcampaignfreetobookagain', 'mod_booking');
    }

    /**
     * Execution function.
     *
     * {@inheritdoc}
     * @throws \coding_exception
     * @throws \dml_exception
     * @see \core\task\task_base::execute()
     */
    public function execute() {
        global $DB;

        $taskdata = $this->get_custom_data();
        $campaignid = $taskdata->campaignid ?? 0;
        $limitfactor = (float)($taskdata->limitfactor ?? 1.0);

        if (empty($campaignid)) {
            mtrace('check_campaign_freetobookagain task: No campaign ID provided, skipping.');
            return;
        }

        mtrace("check_campaign_freetobookagain task: Checking options for campaign {$campaignid}.");

        // First, purge all relevant caches so that settings objects reflect the current campaign state.
        if (!get_config('booking', 'skipsetbackoptionstable')) {
            cache_helper::purge_by_event('setbackoptionstable');
        }
        cache_helper::purge_by_event('setbackoptionsettings');
        cache_helper::purge_by_event('setbackprices');

        // Destroy all campaign singletons to ensure fresh data.
        singleton_service::destroy_all_campaigns();

        // Get all booking option IDs where maxanswers is set (limited places).
        $alloptionids = $DB->get_fieldset_select('booking_options', 'id', 'maxanswers > 0');

        if (empty($alloptionids)) {
            mtrace('check_campaign_freetobookagain task: No booking options with limited places found.');
            return;
        }

        $triggeredcount = 0;

        foreach ($alloptionids as $optionid) {
            try {
                // Get the DB maxanswers (without campaign modification).
                $dbmaxanswers = (int)$DB->get_field('booking_options', 'maxanswers', ['id' => $optionid]);

                if (empty($dbmaxanswers)) {
                    continue;
                }

                // Get the current settings (WITH campaign modification applied).
                $settings = singleton_service::get_instance_of_booking_option_settings($optionid);

                if (empty($settings->cmid)) {
                    continue;
                }

                $ba = singleton_service::get_instance_of_booking_answers($settings);
                $bookedplaces = booking_answers::count_places($ba->get_usersonlist());

                // The campaign-modified maxanswers.
                $campaignmaxanswers = $settings->maxanswers;

                // Determine the "before" state: was the option fully booked before the campaign transition?
                // If the campaign is starting (limitfactor > 1), the "before" state used DB maxanswers.
                // If the campaign is ending (limitfactor > 1), the "before" state used campaign maxanswers.
                // We check both directions: the key question is whether places became free.
                $wasfullybooked = ($bookedplaces >= $dbmaxanswers) || ($bookedplaces >= $campaignmaxanswers);

                // Is the option currently NOT fully booked (with campaign-adjusted maxanswers)?
                $isnowfree = !$ba->is_fully_booked();

                if ($wasfullybooked && $isnowfree) {
                    // Sync waiting list first.
                    $option = singleton_service::get_instance_of_booking_option($settings->cmid, $optionid);
                    $option->sync_waiting_list(false, true);

                    // Trigger the freetobookagain event. Use admin user ID (no logged-in user in cron).
                    booking_option::check_if_free_to_book_again($settings, 2, true);
                    $triggeredcount++;

                    mtrace("  Option {$optionid}: places freed by campaign transition, event triggered.");
                }

                // Clean up singletons to save memory when processing many options.
                singleton_service::destroy_booking_option_singleton($optionid);
                singleton_service::destroy_booking_answers($optionid);
            } catch (\Exception $e) {
                mtrace("  Option {$optionid}: Error - " . $e->getMessage());
            }
        }

        mtrace("check_campaign_freetobookagain task: Done. Triggered freetobookagain for {$triggeredcount} options.");
    }
}
