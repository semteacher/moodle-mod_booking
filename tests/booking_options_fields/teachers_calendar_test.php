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
 * Tests for booking option field class teachers.
 *
 * @package mod_booking
 * @category test
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @author 2025 Bernhard Fischer-Sengseis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_booking;

use advanced_testcase;
use coding_exception;
use mod_booking\option\fields_info;
use mod_booking_generator;
use stdClass;
use tool_mocktesttime\time_mock;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("$CFG->dirroot/mod/booking/lib.php");
require_once("$CFG->dirroot/mod/booking/classes/price.php");

/**
 * Tests for booking option field class teachers.
 *
 * @package mod_booking
 * @category test
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @author 2025 Bernhard Fischer-Sengseis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @runTestsInSeparateProcesses
 */
final class teachers_calendar_test extends advanced_testcase {
    /**
     * Tests set up.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
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
     * Test creation and update of recurring options.
     *
     * @covers \mod_booking\option\fields\teachers::changes_collected_action
     *
     * @dataProvider rule_option_and_event_visibility_for_teacher_provider
     *
     * @param array $data describes the type of change to the option
     * @param array $expected expected traces for messages sent vs prevented
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function test_create_teacher_calendar_events(array $data, array $expected): void {
        global $DB;
        $bdata = self::provide_bdata();

        // Course is needed for module generator.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);

        // Create users.
        $teacher1 = $this->getDataGenerator()->create_user();
        $teacher2 = $this->getDataGenerator()->create_user();
        $teacher3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher1->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher2->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher3->id, $course->id, 'editingteacher');
        $bookingmanager = $this->getDataGenerator()->create_user(); // Booking manager.

        $bdata['course'] = $course->id;
        $bdata['bookingmanager'] = $bookingmanager->username;
        $booking = $this->getDataGenerator()->create_module('booking', $bdata);

        $this->setAdminUser();

        // Create an initial booking option.
        // The option has 2 optiondates and 1 teacher.
        $record = new stdClass();
        $record->importing = 1;
        $record->bookingid = $booking->id;
        $record->text = 'Testoption';
        $record->description = 'Test description';
        $record->chooseorcreatecourse = 1; // Reqiured.
        $record->courseid = $course->id;
        $record->useprice = 0;
        $record->default = 0;
        $record->optiondateid_1 = "0";
        $record->daystonotify_1 = "0";
        $record->coursestarttime_1 = strtotime('20 May 2050 15:00');
        $record->courseendtime_1 = strtotime('20 May 2050 16:00');
        $record->optiondateid_2 = "0";
        $record->daystonotify_2 = "0";
        $record->coursestarttime_2 = strtotime('21 May 2050 15:00');
        $record->courseendtime_2 = strtotime('21 May 2050 16:00');
        $record->teacheremail = $teacher1->email;
        $record->invisible = ($data['optionsettings'][0]['invisible']) ?? MOD_BOOKING_OPTION_VISIBLE;
        $record->addtocalendar = ($data['optionsettings'][0]['addtocalendar']) ?? 0;

        // Create the booking option.
        /** @var mod_booking_generator $plugingenerator */
        $plugingenerator = self::getDataGenerator()->get_plugin_generator('mod_booking');
        $option = $plugingenerator->create_option($record);

        // Now let's check, if the calendar events are created for the teacher.
        $sql = "SELECT * FROM {event} e
                WHERE e.name LIKE 'Testoption'
                AND e.userid = :userid
                AND e.visible = :visible
                AND e.component LIKE 'mod_booking'
                AND e.eventtype LIKE 'user'";
        $params['userid'] = (int)$teacher1->id;
        $params['visible'] = 1;
        $calendarevents = $DB->get_records_sql($sql, $params);

        $this->assertCount(
            $expected['teacher1oncreate'],
            $calendarevents,
            'There should be ' . $expected['teacher1oncreate'] . ' calendar events for the teacher.'
        );

        // Now we change the teacher and update one add another optiondate.
        $settings = singleton_service::get_instance_of_booking_option_settings($option->id);

        $record = (object)[
            'identifier' => $settings->identifier,
            'id' => $option->id,
            'cmid' => $settings->cmid,
        ];
        fields_info::set_data($record);

        unset($record->importing);
        unset($record->teacheremail);
        $record->coursestarttime_2 = strtotime('21 May 2050 17:00');
        $record->courseendtime_2 = strtotime('21 May 2050 18:00');
        $record->optiondateid_3 = "0";
        $record->daystonotify_3 = "0";
        $record->coursestarttime_3 = strtotime('22 May 2050 15:00');
        $record->courseendtime_3 = strtotime('22 May 2050 16:00');
        $record->teachersforoption = [$teacher2->id];
        booking_option::update($record);

        $params['userid'] = (int)$teacher1->id;
        $calendarevents = $DB->get_records_sql($sql, $params);
        $this->assertCount(
            $expected['teacher1onupdate'],
            $calendarevents,
            'There should now be ' . $expected['teacher1onupdate'] . ' calendar events for teacher1.'
        );

        $params['userid'] = (int)$teacher2->id;
        $calendarevents = $DB->get_records_sql($sql, $params);
        $this->assertCount(
            $expected['teacher2onupdate'],
            $calendarevents,
            'There should be ' . $expected['teacher2onupdate'] . ' calendar events for teacher2.'
        );
    }

    /**
     * Data provider for test_rule_on_answer_and_option_cancelled.
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    public static function rule_option_and_event_visibility_for_teacher_provider(): array {
        return [
            'Check events oncreate and onupdate when option is visible (invisible=0, addtocalendar=0, 2-0-3 messages)' => [
                [
                    'optionsettings' => [
                        [
                            'addtocalendar' => 0, // Do not add to calendar.
                            'invisible' => MOD_BOOKING_OPTION_VISIBLE,
                        ],
                    ],
                ],
                [
                    'teacher1oncreate' => 2,
                    'teacher1onupdate' => 0,
                    'teacher2onupdate' => 3,
                ],
            ],
            'Check events oncreate and onupdate when option is invisible (invisible=0, addtocalendar=0, 2-0-3 messages)' => [
                [
                    'optionsettings' => [
                        [
                            'addtocalendar' => 0, // Do not add to calendar.
                            'invisible' => MOD_BOOKING_OPTION_INVISIBLE,
                        ],
                    ],
                ],
                [
                    'teacher1oncreate' => 0,
                    'teacher1onupdate' => 0,
                    'teacher2onupdate' => 0,
                ],
            ],
            'Check events for all oncreate and onupdate when option is visible (invisible=0, addtocalendar=1, 2-3-3 messages)' => [
                [
                    'optionsettings' => [
                        [
                            'addtocalendar' => 1, // Do not add to calendar.
                            'invisible' => MOD_BOOKING_OPTION_VISIBLE,
                        ],
                    ],
                ],
                [
                    'teacher1oncreate' => 2,
                    'teacher1onupdate' => 3,
                    'teacher2onupdate' => 3,
                ],
            ],
        ];
    }

    /**
     * Provides the data that's constant for the test.
     *
     * @return array
     *
     */
    private static function provide_bdata(): array {
        return [
            'name' => 'Test Booking Policy 1',
            'eventtype' => 'Test event',
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
            'showviews' => ['mybooking,myoptions,showall,showactive,myinstitution'],
        ];
    }
}
