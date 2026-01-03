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
 * Test settings base class for booking mod_booking.
 *
 * @package mod_booking
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @author Andrii Semenets
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_booking\tests;

/**
 * Booking base test settings.
 *
 * @package mod_booking
 * @category test
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bookingbasetestsettings {
    /**
     * Default course data.
     *
     * @var array
     */
    protected array $coursedata = [
        'shortname' => 'testcourse',
        'fullname' => 'Test Course',
        'enablecompletion' => 1,
    ];

    /**
     * Default booking instance data.
     *
     * @var array
     */
    protected array $bookingdata = [
        'name' => 'Test Booking',
        'eventtype' => 'Test event',
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
        'showviews' => ['mybooking,myoptions,optionsiamresponsiblefor,showall,showactive,myinstitution'],
    ];

    /**
     * Default booking option data.
     *
     * @var array
     */
    protected array $optiondata = [
        'text' => 'Booking Option',
        'description' => 'Option Description',
    ];

    /**
     * Default teacher user data.
     *
     * @var array
     */
    protected array $teacheruserdata = [
        'firstname' => 'Teacher',
    ];

    /**
     * Default student user data.
     *
     * @var array
     */
    protected array $studentuserdata = [
        'firstname' => 'Student',
    ];

    /**
     * Teacher role to use for enrolments.
     *
     * @var string
     */
    protected string $teacherrole = 'editingteacher';

    /**
     * Student role to use for enrolments.
     *
     * @var string
     */
    protected string $studentrole = 'student';

    /**
     * Get default course data.
     *
     * @return array
     */
    public function get_course_data(): array {
        return $this->coursedata;
    }

    /**
     * Set course data.
     *
     * @param array $coursedata
     */
    public function set_course_data(array $coursedata): void {
        $this->coursedata = array_merge($this->coursedata, $coursedata);
    }

    /**
     * Get default booking instance data.
     *
     * @return array
     */
    public function get_booking_data(): array {
        return $this->bookingdata;
    }

    /**
     * Set booking instance data.
     *
     * @param array $bookingdata
     */
    public function set_booking_data(array $bookingdata): void {
        $this->bookingdata = array_merge($this->bookingdata, $bookingdata);
    }

    /**
     * Get default booking option data.
     *
     * @return array
     */
    public function get_option_data(): array {
        return $this->optiondata;
    }

    /**
     * Get default booking option data.
     *
     * @param array $optiondata
     */
    public function set_option_data(array $optiondata): void {
        $this->optiondata = array_merge($this->optiondata, $optiondata);
    }

    /**
     * Set data for the teacher user.
     *
     * @param array $userdata
     */
    public function set_teacher_user_data(array $userdata): void {
        $this->teacheruserdata = array_merge($this->teacheruserdata, $userdata);
    }

    /**
     * Set data for the student user.
     *
     * @param array $userdata
     */
    public function set_student_user_data(array $userdata): void {
        $this->studentuserdata = array_merge($this->studentuserdata, $userdata);
    }

    /**
     * Get the teacher role name.
     *
     * @return string
     */
    public function get_teacher_role(): string {
        return $this->teacherrole;
    }

    /**
     * Get the student role name.
     *
     * @return string
     */
    public function get_student_role(): string {
        return $this->studentrole;
    }

    /**
     * Get data for the teacher user.
     *
     * @param int $index
     * @return array
     */
    public function get_teacher_user_data(int $index): array {
        return array_merge([
            'username' => "teacher{$index}",
            'firstname' => 'Teacher',
            'lastname' => (string) $index,
            'email' => "teacher{$index}@example.com",
        ], $this->teacheruserdata);
    }

    /**
     * Get data for the student user.
     *
     * @param int $index
     * @return array
     */
    public function get_student_user_data(int $index): array {
        return array_merge([
            'username' => "student{$index}",
            'firstname' => 'Student',
            'lastname' => (string) $index,
            'email' => "student{$index}@example.com",
        ], $this->studentuserdata);
    }
}
