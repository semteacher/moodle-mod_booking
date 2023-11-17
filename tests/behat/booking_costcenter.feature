@mod @mod_booking @booking_campaigns
Feature: Use cost center to separate purchase of booking options by users.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | T1       |
      | student1 | Student   | 1        | student1@example.com | S1       |
      | student2 | Student   | 2        | student2@example.com | S2       |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C1     | manager        |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name       | intro               | bookingmanager | eventtype | Default view for booking options | Send confirmation e-mail |
      | booking  | C1     | BookingCMP | Booking description | teacher1       | Webinar   | All bookings                     | Yes                      |
    And the following "custom field categories" exist:
      | name     | component   | area    | itemid |
      | CCenters | mod_booking | booking | 0      |
    And the following "custom fields" exist:
      | name        | category | type | shortname | configdata[defaultvalue] |
      | CCenterName | CCenters | text | ccnm1     | cc0                      |
    And the following "mod_booking > pricecategories" exist:
      | ordernum | identifier | name  | defaultvalue | disabled | pricecatsortorder |
      | 1        | default    | Price | 18           | 0        | 1                 |
      | 2        | discount1  | Disc1 | 17           | 0        | 2                 |
      | 3        | discount2  | Disc2 | 16           | 0        | 3                 |
    And I log in as "admin"
    And I set the following administration settings values:
      | Custom booking option field for cost center | ccnm1 |
      | Only one cost center per payment | 1 |
    And I log out
    And the following "mod_booking > options" exist:
      | booking     | text       | course | description | startendtimeknown | coursestarttime | courseendtime | optiondatestart[0] | optiondateend[0] | optiondatestart[1] | optiondateend[1] | useprice | customfield_ccnm1 |
      | BookingCMP  | Option-cc1 | C1     | Deskr-cc1   | 1                 | ## tomorrow ##  | ## +4 days ## | ## tomorrow ##     | ## +2 days ##    | ## +3 days ##      | ## +4 days ##    | 1        | costcenter1       |
      | BookingCMP  | Option-cc2 | C1     | Deskr-cc2   | 1                 | ## tomorrow ##  | ## +5 days ## | ## +2 days ##      | ## +3 days ##    | ## +4 days ##      | ## +4 days ##    | 1        | costcenter2       |
  @javascript
  Scenario: Booking: an attempt to buy options with different costcenters as student
    Given I am on the "BookingCMP" Activity page logged in as student1
    And I click on "Add to cart" "text" in the ".allbookingoptionstable_r1 .booknow" "css_element"
    And I wait "1" seconds
    And I click on "Add to cart" "text" in the ".allbookingoptionstable_r2 .booknow" "css_element"
    Then I should see "Different cost center" in the ".modal-dialog .modal-header" "css_element"
