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
 * Tests for preserving option dates during a slot-booking type transition.
 *
 * @package mod_booking
 * @category test
 * @copyright 2026 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_booking;

use advanced_testcase;
use mod_booking\option\fields\optiondates;
use mod_booking_generator;
use stdClass;

/**
 * Tests for preserving option dates during a slot-booking type transition.
 *
 * @covers \mod_booking\option\fields\optiondates
 */
final class slotbooking_session_transition_test extends advanced_testcase {
    /**
     * Reset singleton state between tests.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        singleton_service::destroy_instance();
    }

    /**
     * Clean up generator state.
     */
    protected function tearDown(): void {
        /** @var mod_booking_generator $plugingenerator */
        $plugingenerator = self::getDataGenerator()->get_plugin_generator('mod_booking');
        $plugingenerator->teardown();
        parent::tearDown();
    }

    /**
     * Missing dynamic date controls must not delete dates while changing to session-backed slot booking.
     */
    public function test_prepare_save_restores_sessions_during_type_transition(): void {

        $this->setAdminUser();

        $course = self::getDataGenerator()->create_course();
        /** @var mod_booking_generator $plugingenerator */
        $plugingenerator = self::getDataGenerator()->get_plugin_generator('mod_booking');
        $booking = $plugingenerator->create_instance(['course' => $course->id]);

        $startone = strtotime('2050-01-10 09:00:00 UTC');
        $endone = strtotime('2050-01-10 10:00:00 UTC');
        $starttwo = strtotime('2050-01-10 11:00:00 UTC');
        $endtwo = strtotime('2050-01-10 12:00:00 UTC');
        $option = $plugingenerator->create_option((object)[
            'bookingid' => $booking->id,
            'text' => 'Option with sessions',
            'optiontype' => MOD_BOOKING_OPTIONTYPE_DEFAULT,
            'optiondateid_0' => 0,
            'coursestarttime_0' => $startone,
            'courseendtime_0' => $endone,
            'daystonotify_0' => 0,
            'optiondateid_1' => 0,
            'coursestarttime_1' => $starttwo,
            'courseendtime_1' => $endtwo,
            'daystonotify_1' => 0,
        ]);
        singleton_service::destroy_instance();

        $formdata = (object)[
            'optiontype' => MOD_BOOKING_OPTIONTYPE_SLOTBOOKING,
            'slot_type' => 'session',
        ];
        $newoption = new stdClass();
        $newoption->id = $option->id;
        $newoption->type = MOD_BOOKING_OPTIONTYPE_DEFAULT;

        optiondates::prepare_save_field($formdata, $newoption, MOD_BOOKING_UPDATE_OPTIONS_PARAM_DEFAULT);

        $this->assertSame(2, $formdata->datescounter);
        $this->assertSame($startone, $formdata->coursestarttime_0);
        $this->assertSame($endone, $formdata->courseendtime_0);
        $this->assertSame($starttwo, $formdata->coursestarttime_1);
        $this->assertSame($endtwo, $formdata->courseendtime_1);
        $this->assertSame($startone, $newoption->coursestarttime);
        $this->assertSame($endtwo, $newoption->courseendtime);
    }

    /**
     * Existing slot-booking options must still be able to intentionally remove every session.
     */
    public function test_prepare_save_does_not_restore_sessions_for_existing_slotbooking(): void {

        $this->setAdminUser();

        $course = self::getDataGenerator()->create_course();
        /** @var mod_booking_generator $plugingenerator */
        $plugingenerator = self::getDataGenerator()->get_plugin_generator('mod_booking');
        $booking = $plugingenerator->create_instance(['course' => $course->id]);
        $option = $plugingenerator->create_option((object)[
            'bookingid' => $booking->id,
            'text' => 'Existing slot option',
            'optiontype' => MOD_BOOKING_OPTIONTYPE_SLOTBOOKING,
            'slot_enabled' => 1,
            'slot_type' => 'session',
            'optiondateid_0' => 0,
            'coursestarttime_0' => strtotime('2050-01-10 09:00:00 UTC'),
            'courseendtime_0' => strtotime('2050-01-10 10:00:00 UTC'),
            'daystonotify_0' => 0,
        ]);
        singleton_service::destroy_instance();

        $formdata = (object)[
            'optiontype' => MOD_BOOKING_OPTIONTYPE_SLOTBOOKING,
            'slot_type' => 'session',
        ];
        $newoption = new stdClass();
        $newoption->id = $option->id;
        $newoption->type = MOD_BOOKING_OPTIONTYPE_SLOTBOOKING;

        optiondates::prepare_save_field($formdata, $newoption, MOD_BOOKING_UPDATE_OPTIONS_PARAM_DEFAULT);

        $this->assertObjectNotHasProperty('datescounter', $formdata);
        $this->assertNull($newoption->coursestarttime);
        $this->assertNull($newoption->courseendtime);
    }
}
