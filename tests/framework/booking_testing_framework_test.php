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
 * Tests for booking option events.
 *
 * @package mod_booking
 * @category test
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @author Andrii Semenets
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_booking\framework;

use advanced_testcase;
use coding_exception;
use mod_booking\booking_bookit;
use mod_booking\singleton_service;
use mod_booking\bo_availability\bo_info;
use mod_booking\booking_answers\booking_answers;
use mod_booking_generator;
use mod_booking\local\connectedcourse;
use local_entities\entitiesrelation_handler;
use mod_booking\tests\bookingbasetest;
use mod_booking\tests\bookingbasetestsettings;
use context_system;
use context_module;
use core_course_category;
use stdClass;
use tool_mocktesttime\time_mock;

/**
 * Class handling tests for booking options.
 *
 * @package mod_booking
 * @category test
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class booking_testing_framework_test extends advanced_testcase {
    /**
     * Tests set up.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        time_mock::init();
        time_mock::set_mock_time(strtotime('now'));
        singleton_service::destroy_instance();
    }

    /**
     * Mandatory clean-up after each test.
     */
    public function tearDown(): void {
        parent::tearDown();
        /** @var mod_booking_generator $plugingenerator */
        $plugingenerator = self::getDataGenerator()->get_plugin_generator('mod_booking');
        $plugingenerator->teardown();
    }

    /**
     * Test update of bookig option and tracking changes.
     *
     * @covers \mod_booking\event\teacher_added
     * @covers \mod_booking\booking_option::update
     * @covers \mod_booking\option\field_base::check_for_changes
     *
     * @param array $bdata
     * @throws \coding_exception
     *
     * @dataProvider booking_common_settings_provider
     */
    public function test_framework_option_creation(array $bdata): void {

        $this->setAdminUser();
        $basesettings = new bookingbasetestsettings();
        $basesettings->set_option_data($bdata['options'][0]);
        $bookingtest = new bookingbasetest($basesettings, 3, 2, 1, 1);
        $option1 = $bookingtest->returnfirstoption();
        $student1 = $bookingtest->return_student1();

        $settings = singleton_service::get_instance_of_booking_option_settings($option1->id);
        // To avoid retrieving the singleton with the wrong settings, we destroy it.
        singleton_service::destroy_booking_singleton_by_cmid($settings->cmid);

        // Book the first user without any problem.
        $boinfo = new bo_info($settings);

        $this->setUser($student1);
        [$id, $isavailable, $description] = $boinfo->is_available($settings->id, $student1->id, true);
        $this->assertEquals(MOD_BOOKING_BO_COND_BOOKITBUTTON, $id);
        $result = booking_bookit::bookit('option', $settings->id, $student1->id);
        [$id, $isavailable, $description] = $boinfo->is_available($settings->id, $student1->id, true);
        $this->assertEquals(MOD_BOOKING_BO_COND_CONFIRMBOOKIT, $id);
        $result = booking_bookit::bookit('option', $settings->id, $student1->id);
        [$id, $isavailable, $description] = $boinfo->is_available($settings->id, $student1->id, true);
        $this->assertEquals(MOD_BOOKING_BO_COND_ALREADYBOOKED, $id);
    }

    /**
     * Data provider for booking_option_test
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    public static function booking_common_settings_provider(): array {
        return [
            'simple booking scenario' => [
                [
                    'booking' => [
                        'name' => 'Rule Specific Time Test',
                        'eventtype' => 'Test rules',
                        'enablecompletion' => 1,
                        'bookedtext' => ['text' => 'text'],
                        'waitingtext' => ['text' => 'text'],
                        'notifyemail' => ['text' => 'text'],
                        'statuschangetext' => ['text' => 'text'],
                        'deletedtext' => ['text' => 'text'],
                        'pollurltext' => ['text' => 'text'],
                        'pollurlteacherstext' => ['text' => 'text'],
                        'notificationtext' => ['text' => 'text'],
                        'userleave' => ['text' => 'text'],
                        'tags' => '',
                        'completion' => 2,
                        'showviews' => ['mybooking,myoptions,optionsiamresponsiblefor,showall,showactive,myinstitution'],
                    ],
                    'options' => [
                        // Option 1 with 1 session in 3 days.
                        0 => [
                            'text' => 'Option: in 3 days',
                            'description' => 'Will start in 3 days',
                            'chooseorcreatecourse' => 1, // Required.
                            'optiondateid_0' => "0",
                            'daystonotify_0' => "0",
                            'coursestarttime_0' => strtotime('+3 days', time()),
                            'courseendtime_0' => strtotime('+4 days', time()),
                        ],
                        // Option 2 with 2 session started in the remote future.
                        1 => [
                            'text' => 'Option-with-two-sessions',
                            'description' => 'This option has two optiondates',
                            'chooseorcreatecourse' => 1, // Required.
                            'optiondateid_0' => "0",
                            'daystonotify_0' => "0",
                            'coursestarttime_0' => strtotime('2 June 2050 15:00'),
                            'courseendtime_0' => strtotime('2 June 2050 16:00'),
                            'optiondateid_1' => "0",
                            'daystonotify_1' => "0",
                            'coursestarttime_1' => strtotime('8 June 2050 15:00'),
                            'courseendtime_1' => strtotime('8 June 2050 16:00'),
                        ],
                        // Option 3 with 3 session started in the remote future.
                        2 => [
                            'text' => 'Option-with-three-sessions',
                            'description' => 'This option has three optiondates',
                            'chooseorcreatecourse' => 1, // Required.
                            'optiondateid_0' => "0",
                            'daystonotify_0' => "0",
                            'coursestarttime_0' => strtotime('2 June 2050 15:00'),
                            'courseendtime_0' => strtotime('2 June 2050 16:00'),
                            'optiondateid_1' => "0",
                            'daystonotify_1' => "0",
                            'coursestarttime_1' => strtotime('8 June 2050 15:00'),
                            'courseendtime_1' => strtotime('8 June 2050 16:00'),
                            'optiondateid_2' => "0",
                            'daystonotify_2' => "0",
                            'coursestarttime_2' => strtotime('15 June 2050 15:00'),
                            'courseendtime_2' => strtotime('15 June 2050 16:00'),
                        ],
                    ],
                ],
            ],
        ];
    }
}
