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

namespace mod_booking\tests\price;

use mod_booking_generator;
use stdClass;
use testing_util;
use mod_booking\tests\bookingbasetest;
use local_shopping_cart\shopping_cart;
use local_shopping_cart\shopping_cart_history;
use local_shopping_cart\local\cartstore;
use local_shopping_cart\output\shoppingcart_history_list;

/**
 * Booking Base test class.
 *
 * @package mod_booking
 * @category test
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bookingwithprice extends bookingbasetest{
    /**
     * @var array
     */
    protected array $pricecategoryies = [];

    /**
     * Constructor.
     *
     * @param bookingpricetestsettings|null $settings
     * @param int $numberofusers
     * @param int $numberofbookingoptions
     * @param int $numberofcourses
     * @param int $numberofbookings
     */
    public function __construct(
        ?bookingpricetestsettings $settings = null,
        int $numberofusers = 1,
        int $numberofbookingoptions = 1,
        int $numberofcourses = 1,
        int $numberofbookings = 1
    ) {
        $this->settings = $settings ?? new bookingpricetestsettings();
        $this->numberofusers = $numberofusers;
        $this->numberofbookingoptions = $numberofbookingoptions;
        $this->numberofcourses = $numberofcourses;
        $this->numberofbookings = $numberofbookings;

        $this->initialize_data();
    }

    /**
     * Return all price categories.
     *
     * @return array
     */
    public function return_pricecategoryies(): array {
        return $this->pricecategoryies;
    }

    /**
     * Return the first price category.
     *
     * @return stdClass|null
     */
    public function return_pricecategory1(): ?stdClass {
        return $this->pricecategoryies[0] ?? null;
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
        $this->create_price_category();
        $this->create_booking_options();
    }

    /**
     * Create a price category.
     *
     * @return void
     */
    protected function create_price_category(): void {
        // Get option's default settings.
        $pricecategorydatadata = $this->settings->get_pricecategorydata_data();
        $this->pricecategoryies[] = $this->plugingenerator->create_pricecategory($pricecategorydatadata);
    }

    /**
     * Purchase an option for a user.
     *
     * @param stdClass $user
     * @param int $itemid
     * @return void
     */
    public function purchase_option_for_user(stdClass $user, int $itemid): void {
        // Purchase item in behalf of user if shopping_cart installed.
        if (class_exists('local_shopping_cart\shopping_cart')) {
            // Clean cart.
            shopping_cart::delete_all_items_from_cart($user->id);
            // Set user to buy in behalf of.
            shopping_cart::buy_for_user($user->id);
            // Get cached data or setup defaults.
            $cartstore = cartstore::instance($user->id);
            // Put in a test item with given ID (or default if ID > 4).
            shopping_cart::add_item_to_cart('mod_booking', 'option', $itemid, -1);
            // Confirm cash payment.
            $res = shopping_cart::confirm_payment($user->id, LOCAL_SHOPPING_CART_PAYMENT_METHOD_CASHIER_CASH);
        }
    }
}
