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

namespace mod_booking\tests\price;

use mod_booking\tests\bookingbasetestsettings;

/**
 * Booking base test settings.
 *
 * @package mod_booking
 * @category test
 * @copyright 2025 Wunderbyte GmbH <info@wunderbyte.at>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bookingpricetestsettings extends bookingbasetestsettings {
    /**
     * Default price category data.
     *
     * @var array
     */
    protected array $pricecategorydata = [
            'ordernum' => 1,
            'name' => 'default',
            'identifier' => 'default',
            'defaultvalue' => 100,
            'pricecatsortorder' => 1,
    ];

    /**
     * Default booking option data.
     *
     * @var array
     */
    protected array $optiondata = [
        'text' => 'Booking Option',
        'description' => 'Option Description',
        'useprice' => 1,
        'importing' => 1,
    ];

    /**
     * Get default price category data.
     *
     * @return array
     */
    public function get_pricecategorydata_data(): array {
        return $this->pricecategorydata;
    }

    /**
     * Set price category data.
     *
     * @param array $pricecategorydata
     */
    public function set_pricecategory_data(array $pricecategorydata): void {
        $this->pricecategorydata = array_merge($this->pricecategorydata, $pricecategorydata);
    }
}
