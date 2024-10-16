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

namespace mod_booking;

use mod_booking\singleton_service;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/booking/lib.php');

/**
 * Class for booking answers. An instance is linked to one specific option.
 * But the class provides static functions to get information about a users answers for the whole instance as well.
 *
 * @package mod_booking
 * @copyright 2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @author Georg Maißer
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class booking_answers {

    /** @var string $optionid ID of booking option */
    public $optionid = null;

    /** @var booking_option_settings $bookingoptionsettings instance of bookingoptionsettings class */
    public $bookingoptionsettings = null;

    /** @var array array of records from the booking_answers table. */
    public $answers = [];

    /** @var array array of all user objects (waitinglist and booked) */
    public $users = [];

    /** @var array array of all user objects (no waitinglist, only booked) */
    public $usersonlist = [];

    /** @var array array of all user objects (waitinglist, no booked) */
    public $usersonwaitinglist = [];

    /** @var array array of all user objects (only reserved) */
    public $usersreserved = [];

    /** @var array array of all user objects (only with deleted booking answer) */
    public $usersdeleted = [];

    /** @var array array of all user objects (only those to notify) */
    public $userstonotify = [];

    /**
     * Constructor for the booking answers class.
     * The booking answers class is instantiated for all users alike.
     * But it returns information for the individual users.
     *
     * STATUSPARAM_BOOKED (0) ... user has booked the option
     * STATUSPARAM_WAITINGLIST (1) ... user is on the waiting list
     * STATUSPARAM_RESERVED (2) ... user is on the waiting list
     * STATUSPARAM_NOTBOOKED (4) ... user has not booked the option
     * STATUSPARAM_DELETED (5) ... user answer was deleted
     *
     * @param int $optionid Booking option id.
     * @throws dml_exception
     */
    public function __construct(booking_option_settings $bookingoptionsettings) {

        global $DB, $CFG;

        $optionid = $bookingoptionsettings->id;
        $this->optionid = $optionid;
        $this->bookingoptionsettings = $bookingoptionsettings;

        $cache = \cache::make('mod_booking', 'bookingoptionsanswers');
        $data = $cache->get($optionid);

        if (!$data) {

            $params = array('optionid' => $optionid);

            if ($CFG->version >= 2021051700) {
                // This only works in Moodle 3.11 and later.
                $userfields = \core_user\fields::for_name()->with_userpic()->get_sql('u')->selects;
                $userfields = trim($userfields, ', ');
            } else {
                // This is only here to support Moodle versions earlier than 3.11.
                $userfields = \user_picture::fields('u');
            }

            // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
            /* $sql = "SELECT ba.id as baid, ba.userid, ba.waitinglist, ba.timecreated, $userfields, u.institution
            FROM {booking_answers} ba
            JOIN {user} u ON u.id = ba.userid
            WHERE ba.optionid = :optionid
            AND u.deleted = 0
            ORDER BY ba.timecreated ASC"; */

            $sql = "SELECT ba.id as baid, ba.userid as id, ba.userid, ba.waitinglist, ba.timecreated, ba.optionid
            FROM {booking_answers} ba
            WHERE ba.optionid = :optionid
            ORDER BY ba.timecreated ASC";

            $answers = $DB->get_records_sql($sql, $params);

            // We don't want to query for empty bookings, so we also cache these.
            if (count($answers) == 0) {
                $answers = 'empty';
            }

            // If the answer has the empty placeholder, we replace it by an array.
            if ($answers === 'empty') {
                $answers = [];
            }

            $this->answers = $answers;

            foreach ($answers as $answer) {

                // A user might have one or more 'deleted' entries, but else, there should be only one.
                if ($answer->waitinglist != STATUSPARAM_DELETED) {
                    $this->users[$answer->userid] = $answer;
                }

                switch ($answer->waitinglist) {
                    case STATUSPARAM_BOOKED:
                        $this->usersonlist[$answer->userid] = $answer;
                        break;
                    case STATUSPARAM_WAITINGLIST:
                        $this->usersonwaitinglist[$answer->userid] = $answer;
                        break;
                    case STATUSPARAM_RESERVED:
                        if (count($this->usersonlist) < $this->bookingoptionsettings->maxanswers) {
                            $this->usersonlist[$answer->userid] = $answer;
                        } else {
                            $this->usersonwaitinglist[$answer->userid] = $answer;
                        }
                        $this->usersreserved[$answer->userid] = $answer;
                        break;
                    case STATUSPARAM_DELETED:
                        $this->usersdeleted[$answer->userid] = $answer;
                        break;
                    case STATUSPARAM_NOTIFYMELIST:
                        $this->userstonotify[$answer->userid] = $answer;
                        break;
                }
            }

            $data = (object)[
                'answers' => $this->answers,
                'users' => $this->users,
                'usersonlist' => $this->usersonlist,
                'usersonwaitinglist' => $this->usersonwaitinglist,
                'usersreserved' => $this->usersreserved,
                'usersdeleted' => $this->usersdeleted,
                'userstonotify' => $this->userstonotify,
            ];

            $cache->set($optionid, $data);
        } else {
            $this->answers = $data->answers;
            $this->users = $data->users;
            $this->usersonlist = $data->usersonlist;
            $this->usersonwaitinglist = $data->usersonwaitinglist;
            $this->usersreserved = $data->usersreserved;
            $this->usersdeleted = $data->usersdeleted;
            $this->userstonotify = $data->userstonotify;
        }
    }

    /**
     * Checks booking status of $userid for this booking option. If no $userid is given $USER is used (logged in user)
     * The return value of this function is not equal to the former user_status in booking_option.
     *
     * @param int $userid
     * @return int const STATUSPARAM_* for booking status.
     */
    public function user_status(int $userid = 0) {
        global $USER;

        if ($userid == 0) {
            $userid = $USER->id;
        }

        if (isset($this->usersonlist[$userid])) {
            return STATUSPARAM_BOOKED;
        } else if (isset($this->usersonwaitinglist[$userid])) {
            return STATUSPARAM_WAITINGLIST;
        } else {
            return STATUSPARAM_NOTBOOKED;
        }
    }

    /**
     * Checks booking status of $userid for this booking option. If no $userid is given $USER is used (logged in user)
     *
     * @param int $userid
     * @return int status 0 = activity not completed, 1 = activity completed
     */
    public function is_activity_completed(int $userid) {

        if (isset($this->users[$userid])
            && isset($this->users[$userid]->completed)
            && $this->users[$userid]->completed == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * This function returns an array of all the relevant information of the booking status.
     * This will be used mainly for displaying the information.
     *
     * - iambooked
     * - iamonwaitinglist
     * - maxanswers
     * - maxoverbooking
     * - booked
     * - onwaitinglist
     *
     * @param integer $userid
     * @return array
     */
    public function return_all_booking_information(int $userid) {

        $returnarray = [];

        $returnarray['waiting'] = count($this->usersonwaitinglist);
        $returnarray['booked'] = count($this->usersonlist);

        $returnarray['onnotifylist'] = $this->user_on_notificationlist($userid);

        // We can't set the value if it's not true, because of the way mustache templates work.
        if ($this->bookingoptionsettings->maxanswers != 0) {
            $returnarray['maxanswers'] = $this->bookingoptionsettings->maxanswers;

            $returnarray['freeonlist'] = $returnarray['maxanswers'] - $returnarray['booked'];

             // Determine if the option is booked out.
            if ($returnarray['freeonlist'] <= 0) {
                $returnarray['fullybooked'] = true;
            } else {
                $returnarray['fullybooked'] = false;
            }
        } else {
            $returnarray['fullybooked'] = false;
        }

        if ($this->bookingoptionsettings->maxoverbooking != 0) {
            $returnarray['maxoverbooking'] = $this->bookingoptionsettings->maxoverbooking;

            $returnarray['freeonwaitinglist'] = $returnarray['maxoverbooking'] - $returnarray['waiting'];
        }

        // First check list of booked users.
        if (isset($this->usersonlist[$userid]) && $this->usersonlist[$userid]->waitinglist == STATUSPARAM_BOOKED) {
            $returnarray = array('iambooked' => $returnarray);
        } else if (isset($this->usersonwaitinglist[$userid]) &&
            $this->usersonwaitinglist[$userid]->waitinglist == STATUSPARAM_WAITINGLIST) {
            // Now check waiting list.
            $returnarray = array('onwaitinglist' => $returnarray);
        } else {
            // Else it's not booked.
            $returnarray = array('notbooked' => $returnarray);
        }

        if ($this->bookingoptionsettings->minanswers != 0) {
            $returnarray['minanswers'] = $this->bookingoptionsettings->minanswers;
        }

        return $returnarray;
    }

    /**
     * Verify if a user is actually on the booked list or not.
     *
     * @param integer $userid
     * @return void
     */
    public function user_on_notificationlist(int $userid) {

        if (isset($this->userstonotify[$userid])) {
            return true;
        }
        return false;
    }

    /**
     * Static function to construct booking_answers from only optionid.
     *
     * @param int $optionid
     * @return booking_answers
     */
    public static function get_instance_from_optionid($optionid) {
        $bookingoptionsettings = singleton_service::get_instance_of_booking_option_settings($optionid);
        return singleton_service::get_instance_of_booking_answers($bookingoptionsettings);
    }

    /**
     * Returns the number of active bookings for a given user for the whole instance.
     *
     * @param integer $userid
     * @param integer $bookingid not cmid
     * @return integer
     */
    public static function number_of_active_bookings_for_user(int $userid, int $bookingid) {
        global $DB;

        $params = ['statuswaitinglist' => STATUSPARAM_WAITINGLIST,
                   'bookingid' => $bookingid,
                   'userid' => $userid];

        $sql = "SELECT COUNT(*)
                FROM {booking_answers}
                WHERE waitinglist <= :statuswaitinglist
                AND bookingid = :bookingid
                AND userid = :userid";

        return $DB->count_records_sql($sql, $params);
    }
}
