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
 * Tests framework for booking mod_booking.
 *
 * @package mod_booking
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @author Andrii Semenets
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_booking\tests;

use mod_booking_generator;
use stdClass;
use testing_util;

/**
 * Booking Base test class.
 *
 * @package mod_booking
 * @category test
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bookingbasetest {
    /**
     * @var bookingbasetestsettings
     */
    protected bookingbasetestsettings $settings;

    /**
     * @var mod_booking_generator
     */
    protected mod_booking_generator $plugingenerator;

    /**
     * @var array
     */
    protected array $courses = [];

    /**
     * @var array
     */
    protected array $bookings = [];

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @var stdClass|null
     */
    protected ?stdClass $teacher = null;

    /**
     * @var array
     */
    protected array $students = [];

    /**
     * @var array
     */
    protected array $users = [];

    /**
     * @var int
     */
    protected int $numberofusers;

    /**
     * @var int
     */
    protected int $numberofbookings;

    /**
     * @var int
     */
    protected int $numberofcourses;

    /**
     * @var int
     */
    protected int $numberofinstances;

    /**
     * Constructor.
     *
     * @param bookingbasetestsettings|null $settings
     * @param int $numberofusers
     * @param int $numberofbookings
     * @param int $numberofcourses
     * @param int $numberofinstances
     */
    public function __construct(
        bookingbasetestsettings $settings,
        int $numberofusers,
        int $numberofbookings,
        int $numberofcourses,
        int $numberofinstances = 1
    ) {
        $this->settings = $settings;
        $this->numberofusers = $numberofusers;
        $this->numberofbookings = $numberofbookings;
        $this->numberofcourses = $numberofcourses;
        $this->numberofinstances = $numberofinstances;

        $this->initialize_data();
    }

    /**
     * Return the first teacher.
     *
     * @return stdClass
     */
    public function return_teacher(): stdClass {
        return $this->teacher;
    }

    /**
     * Return the first student.
     *
     * @return stdClass|null
     */
    public function return_student1(): ?stdClass {
        return $this->students[0] ?? null;
    }

    /**
     * Return all students.
     *
     * @return array
     */
    public function return_students(): array {
        return $this->students;
    }

    /**
     * Return all users.
     *
     * @return array
     */
    public function return_users(): array {
        return $this->users;
    }

    /**
     * Return all courses.
     *
     * @return array
     */
    public function return_courses(): array {
        return $this->courses;
    }

    /**
     * Return all booking instances.
     *
     * @return array
     */
    public function return_bookings(): array {
        return $this->bookings;
    }

    /**
     * Return all booking options.
     *
     * @return array
     */
    public function returnalloptions(): array {
        return $this->options;
    }

    /**
     * Return the first booking option.
     *
     * @return stdClass|null
     */
    public function returnfirstoption(): ?stdClass {
        return $this->options[0] ?? null;
    }

    /**
     * Initialize data for tests.
     *
     * @return void
     */
    protected function initialize_data(): void {
        $datagenerator = testing_util::get_data_generator();
        $this->plugingenerator = $datagenerator->get_plugin_generator('mod_booking');

        $this->create_courses($datagenerator);
        $this->create_users($datagenerator);
        $this->enrol_users($datagenerator);
        $this->create_booking_instances($datagenerator);
        $this->create_booking_options();
    }

    /**
     * Create courses.
     *
     * @param object $datagenerator
     * @return void
     */
    protected function create_courses(object $datagenerator): void {
        $coursedata = $this->settings->get_course_data();
        for ($i = 1; $i <= $this->numberofcourses; $i++) {
            $this->courses[] = $datagenerator->create_course($coursedata);
        }
    }

    /**
     * Create users (teacher and students).
     *
     * @param object $datagenerator
     * @return void
     */
    protected function create_users(object $datagenerator): void {
        $this->teacher = $datagenerator->create_user($this->settings->get_teacher_user_data(1));
        $this->users[] = $this->teacher;

        $studentcount = max(0, $this->numberofusers - 1);
        for ($i = 1; $i <= $studentcount; $i++) {
            $student = $datagenerator->create_user($this->settings->get_student_user_data($i));
            $this->students[] = $student;
            $this->users[] = $student;
        }
    }

    /**
     * Enrol users to courses.
     *
     * @param object $datagenerator
     * @return void
     */
    protected function enrol_users(object $datagenerator): void {
        foreach ($this->courses as $course) {
            $datagenerator->enrol_user($this->teacher->id, $course->id, $this->settings->get_teacher_role());
            foreach ($this->students as $student) {
                $datagenerator->enrol_user($student->id, $course->id, $this->settings->get_student_role());
            }
        }
    }

    /**
     * Create booking instances.
     *
     * @param object $datagenerator
     * @return void
     */
    protected function create_booking_instances(object $datagenerator): void {
        $bookingdata = $this->settings->get_booking_data();
        $bookingcounter = 1;

        foreach ($this->courses as $course) {
            for ($i = 1; $i <= $this->numberofinstances; $i++) {
                $record = $bookingdata;
                $record['course'] = $course->id;
                $record['bookingmanager'] = $this->teacher->username;

                if (empty($record['name'])) {
                    $record['name'] = 'Test Booking';
                }
                $record['name'] = $record['name'] . " {$bookingcounter}";

                $this->bookings[] = $datagenerator->create_module('booking', $record);
                $bookingcounter++;
            }
        }
    }

    /**
     * Create booking options.
     *
     * @return void
     */
    protected function create_booking_options(): void {
        $optiondata = $this->settings->get_option_data();
        $optioncounter = 1;

        foreach ($this->bookings as $index => $booking) {
            $course = $this->courses[(int) floor($index / $this->numberofinstances)] ?? $this->courses[0];
            for ($i = 1; $i <= $this->numberofbookings; $i++) {
                $record = [
                    'bookingid' => $booking->id,
                    'text' => $optiondata['text'] ?? "Option {$optioncounter}",
                    'chooseorcreatecourse' => 1,
                    'courseid' => $course->id,
                    'description' => $optiondata['description'] ?? "Option description {$optioncounter}",
                    'optiondateid_0' => '0',
                    'daystonotify_0' => '0',
                    'coursestarttime_0' => $optiondata['coursestarttime_0'] ?? strtotime('now + 1 day'),
                    'courseendtime_0' => $optiondata['courseendtime_0'] ?? strtotime('now + 2 day'),
                ];

                $record = (object) array_merge($record, $optiondata);
                $this->options[] = $this->plugingenerator->create_option($record);
                $optioncounter++;
            }
        }
    }
}
