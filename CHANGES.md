## Version 7.7.6 (2022121300)
**Bugfixes:**
* Bugfix: Fix bug where canceluntil date was wrongly calculated from $now instead of $coursestarttime.
* Bugfix: Fix auto enrolment of teachers, improve defaults and automatic course creation.

## Version 7.7.5 (2022120900)
**New features:**
* New feature: Add setting to turn off creation of user calendar events, if wanted.
* New feature: Better German language strings ("Buchungen" instead of "Antworten").
* New feature: Turn messages off by entering 0.

**Improvements:**
* Improvement: Optimized and improved DB performance (added keys, indexes etc.)
* Improvement: Use caching for booking option description.
* Improvement: Better settings for automatic course creation category custom field.

**Bugfixes:**
* Bugfix: Fix problem with static functions
* Bugfix: Do not trigger bookingoption_updated when a booking option is cancelled.
* Bugfix: Fixed some errors in prettify_datetime.
* Bugfix: Typo in {eventtype} of fieldmapping.
* Bugfix: Correctly retrieve sessions via singleton_service of booking_option_settings.
* Bugfix: Dates spanning over more than one day did not show second date.
* Bugfix: Calendar events were created twice on creation of booking options.
* Bugfix: We need to purge option settings cache after updating.
* Bugfix: Fix a bug were options without dates showed Jan 1st, 1970.
* Bugfix: Fixed some bugs with automatic course creation.
* Bugfix: Fixed some behat test (issue #217).
* Bugfix: Fixed error string in CSV import.
* Bugfix: Fix missing userid in send notification mails task (function return_all_booking_information) - issue #218
* Bugfix: Optionid was missing when creating new sessions in optiondates.php (multi-session manager).

## Version 7.7.4 (2022120200)
**New features:**
* New feature: New placeholders from user profile:
  username, firstname, lastname, department, address, city, country.

**Improvements:**
* Improvement: Fixed and renamed placeholders: {times} are now {dates},
  introduced {teachers} for list of teachers, and fixed {teacher} and {teacherN}
* Improvement: Introduced price areas to support subbookings in the future.
* Improvement: several changes to optiondates handler.
* Improvement: Add missing capability strings.
* Improvement: Improve performance by more extensive use of caching.
* Improvement: Better function for condition messages.
* Improvement: Performance improvements in answers and option (user_submit_response)
* Improvement: Reduce sql for performance. Booking_answers class has now no further
  information about the users, apart from the id.
* Improvement: Add resilience to booking_answers class
* Improvement: Show titleprefix on "book other users" page.

**Bugfixes:**
* Bugfix: Fix a lot of little bug with booking rules.
* Bugifx: Fixed a param in toggle_notify_user webservice.
* Bugfix: Use correct message providers.
* Bugfix: fixed call of rule->execute()
* Bugfix: catch potential error on user deletion.
* Bugfix: Add userid to check_if_limit function to fix caching problem with booking answers.
* Bugfix: Small fix with user status function.
* Bugfix: first column not unique.

## Version 7.7.3 (2022112300)
**Improvements:**
* Improvement: Correctly use availability conditions in optionview.php
* Improvement: Add indexes to tables where necessary
* Improvement: Delete user events when booking option is cancelled and more.
* Improvement: Show manage responses in menu.

**Bugfixes:**
* Bugfix: Invalidate caches when a booking option is deleted.
* Bugfix: Adhoc tasks failed when booking options were deleted.
* Bugfix: Adhoc tasks failed when booking options were deleted.

## Version 7.7.2 (2022111600)
**New features:**
* New feature: Cancel booking options

## Version 7.7.1 (2022111400)
**New features:**
* New feature: Bew booking rule condition to select specific users via autocomplete.

**Improvements:**
* Improvement: More efficient implementation of rule conditions.

## Version 7.7.0 (2022111001)
**New features:**
* New feature: New booking rules allowing to differentiate between rules, conditions and actions.
  This is really cool and will enable booking to do great things in the near future!
* New feature: New event-based rules allosing to react to any booking option event.
* New feature: Cancelling of booking options without deleting them, the reason will be stored into
  internal annotations. Cancelling of booking options can be undone too.
* New feature: New rule condition allowing to enter the text to be compared (contain/equal)
  with a custom user profile field.

**Improvements:**
* Improvement: Collapsible overview of placeholders like {bookingdetails} for "Send mail" action of booking rules.
* Improvement: More beautiful menu of booking options in view.php.
* Improvement: New event bookingoption_cancelled is triggered when a booking option gets cancelled.

**Bugfixes:**
* Bugfix: When all optiondates were removed in optionform, they were not deleted at all.
* Bugfix: Fix type error in payment service provider.
* Bugfix: Restored Moodle 3.11 compatibility for booking rules.
* Bugfix: Minor code quality fixes.

## Version 7.6.3 (2022110400)
**Improvements:**
* Improvement: Improved conflict handling for entities at same date.
* Improvement: Better styling for customdates button.
* Improvement: For new options automatically check the checkbox to save entities for all optiondates.

**Bugfixes:**
* Bugfix: Fix entity conflicts for different areas (option / optiondate).

## Version 7.6.2 (2022110200)
**Bugfixes:**
* Bugfix: Fixed issue #213 - privacy provider get_contexts_for_userid() - MariaDB, SQL doesn't work.

## Version 7.6.1 (2022103100)
**Improvements:**
* Improvement: Use caching for serving images.

**Bugfixes:**
* Bugfix: Fix formula calculation with non iterable custom fields.

## Version 7.6.0 (2022102700)
**New features:**
* New feature: Entities can now be set for sessions of booking options (a.k.a. optiondates) too.
* New feature: Entities can conflict with each other if on the same date.

**Improvements:**
* Improvement: Entity shortnames (abbreviations like WBO for Wunderbyte Office) are now supported.
* Improvement: If an entity is set, we show it (name + shortname) instead of the value stored in "location".
* Improvement: Better handling of entities associated with booking options.
* Improvement: Better interface for optiondate manager.

**Bugfixes:**
* Bugfix: Duplication with conditions caused an error (optionid: -1).
* Bugfix: Fix undefined index for blocked events (start and endtime cannot be retrieved from string).
* Bugfix: Fix undefined index for blocked events (start and endtime cannot be retrieved from string).
* Bugfix: Postgres fix for teachers instance report.

## Version 7.5.5 (2022101200)
**New features:**
* New feature: Min. number of participants can now be set (currently only shown, no logic).

**Improvements:**
* Improvement: Add support for new shopping cartitem functionality (serviceperiodstart & end).
* Improvement: Header for "actions" in booking option cards settings menu.
* Improvement: New price formula setting to apply unit factor (is now set via config setting,
  not needed in price formula anymore).
* Improvement: Show educational units in tables and cards too.

## Version 7.5.4 (2022100500)
**Improvements:**
* Improvement: Booking rules => use classic moodleform so we can use editor.

## Version 7.5.3 (2022100400)
**New features:**
* New feature: New teachers report for booking instances,
  including courses (booking options), missing hours and substitutions.

## Version 7.5.2 (2022092901)
**Bugfixes:**
* Bugfix: Fix issue #212 - upgrade script for field 'availability' had wrong version number.

## Version 7.5.1 (2022092900)
**Bugfixes:**
* Bugfix: Fix language strings.

## Version 7.5.0 (2022092800)
**New features:**
* New feature: Global Roles (PRO) - Rules can now be added globally.
  The rule 'Send e-mail n days before a certain date' now allows to define
  to send e-mails n days before a certain date within an option (e.g. coursestarttime,
  bookingopeningtime...) to all users who have a custom profile field matching (or
  containing) the same value as a certain field of an option. The mail templates also
  support placeholders (e.g. {bookingdetails}).

## Version 7.4.3 (2022092700)
**Improvements:**
* Improvement: Added notification when a semester is saved in form.

**Bugfixes:**
* Bugfix: Fix bug where error was shown in optionformconfig_form.

## Version 7.4.2 (2022091902)
**Improvements:**
* Improvement: Restored holiday names.

## Version 7.4.1 (2022091900)
**New features:**
* New feature: Add user profile fields to e-mail params.

**Improvements:**
* Improvement: PRO availability conditions and info texts (and fixed correct order).

**Bugfixes:**
* Bugfix: Cleaning override of override concept
* Bugfix: Deal with missing attachments.
* Bugfix: If user profile fields are missing, we need to load them correctly.

## Version 7.4.0 (2022091500)
**New features:**
* New feature: New availability condition for custom profile fields.
* New feature: New performance report for teachers (performed hours/units).
* New feature: CSV Import now works with "identifier" and "titleprefix".
* New feature:

**Improvements:**
* Improvement: New operators for user profile field availability condition.
* Improvement: Added German translations for availability condition strings.
* Improvement: Added titleprefix ("course number") for previously booked availability condition.
* Improvement: Migrate old option names containing separator and identifier and use new "identifier" field.
* Improvement: Better optiondates handling for quickfinder block (bookingoptions_simple_table).

**Bugfixes:**
* Bugfix: Do not show or count reserved and deleted bookings (view.php / coursepage_available_options).
* Bugfix: Fixed Moodle 3.9 compatibility issues.
* Bugfix: Missing titleprefix caused quickfinder block not to work.
* Bugfix: Fixed yet another waitinglist bug on view.php.
* Bugfix: Unique option names are not necessary anymore (we use identifier now).
* Bugfix: Better cachedef strings - closes #210
* Bugfix: Fixed an SQL bug.
* Bugfix: Fixed "isbookable" availability condition.

## Version 7.3.0 (2022090100)
**New features:**
* New feature: Booking availability conditions introduced.
* New feature: New report for teachers (performed units).
* New feature: Manage instance templates (from plugin settings).
* New feature: New setting to round prices after price formula application.

**Improvements:**
* Improvement: Price formula - add support for multiple custom fields.
* Improvement: get_options_filter_sql function now support userid and bookingparam (booked, waitinglist etc.)
* Improvement: More intuitive and simpler holidays interface.
* Improvement: Better displaying of prices.
* Improvement: Now allowing up to 100 option dates.
* Improvement: Search in view.php is now case-insensitive.
* Improvement: Correct feedback when teacher user does not exist (in CSV import).
* Improvement: New scheduled task to clean DB and better task names.
* Improvement: Better string for invalid link (for booked meetings, e.g. teams customfield).
* Improvement: Add possibility to fetch filtersql for special user with booked params.

**Bugfixes:**
* Bugfix: Fix mybookings view to not show reserved and deleted bookings.
* Bugfix: Fix issue #193 (stuck on settings page).
* Bugfix: Correctly delete entries in booking_optiondates_teachers for 'change semester' function.

## Version 7.2.7 (2022080900)
**New features:**
* New feature: Added booking opening time (can be used like booking closing time).

**Improvements:**
* Improvement: New event listener for price category identifier changes updates prices of booking options automatically.
* Improvement: Also delete header images from DB when deleting an instance.#
* Improvement: Added a warning text for semester change.
* Improvement: Better display of course duration (days, hours, minutes).
* Improvement: Better display of search button.

**Bugfixes:**
* Bugfix: Fixed 'book other users' feature of booking (broken since 'unreal' deletion of booking answers).
* Bugfix: Booked out courses may not be bookable.
* Bugfix: Fixed some bugs with simple / expert mode and showing entitities.
* Bugfix: Bugfix where canceluntil didn't work on negative values (after course started).
* Bugfix: Fix errors in create_option_from_optionid.

## Version 7.2.6 (2022072500)
**New features:**
* New feature: Prevent option from recalculating prices.
* New feature: Cancel for all participants.
* New feature: Image duplication (both for options and booking instance header images).

**Improvements:**
* Improvement: Correctly delete image files when deleting booking options.
* Improvement: Duplication of images for individual booking options now working wiht backup/restore.

**Bugfixes:**
* Bugfix: When duplicating or restoring options create new random unique identifiers.
* Bugfix: Fix sql problem in the book for others panel.
* Bugfix: Correct duplication, restoring and deleting of custom fields.
* Bugfix: Fix SQL bug for image files.
* Bugfix: Fix SQL comma bug with get_options_filter_sql.

## Version 7.2.5 (2022071801)
**Improvements:**
* New price calculations with entity factor from entity manager.

**Bugfixes:**
* Hotfix - Missing quote character in install.xml.
* Added missing "dayofweek" in backup.

## Version 7.2.4 (2022071800)
**New features:**
* Added automatic course creation.
* Added price calculation for all options of instance.

**Improvements:**
* Updated automated tests config for M4.
* Performance improvement for construction of booking_settings.
* Added missing language strings.

**Bugfixes:**
* Fixed unit testing fail in externallib.
* Fixed possible error with price on guest view.
* Fixed postgres errors.
* Fixed broken commenting feature for booking options.

## Version 7.2.3 (2022070500)
**New features:**
* Calculate prices for specific booking options using a sophisticated JSON formula.
* Direct connection between booking instances and semesters.
* If we have a semester, only enrol from semester start until semester end.

**Improvements:**
* New identifier field for booking options.
* New annotation field for booking options for internal remarks and comments.
* New title prefix field for booking options (e.g. for non-unique course number).
* Show collapsible optiondates in all_options.php.
* Several improvements for handling of semesters.
* Implement user data deletion requests for Privacy API - closes #197
* Better notification button (for observer list), fixed toggle and improved strings for notification mails.

**Bugfixes:**
* Fix bug where no invisible was provided in webservice.
* Also create optiondates for new booking options.
* Added strings for Privacy API - closes #198

## Version 7.2.2 (2022062900)
**New features:**
* Internal annotations for booking options.

**Improvements:**
* Moved 'description' up to a more prominent place in booking option form.

**Bugfixes:**
* When no teacher was set for a booking option, teacher notifications were sent to participants.
* Fixed broken duplication of booking options.

## Version 7.2.1 (2022062200)
**Bugfixes:**
* Fixed bug relating to invisible options.
* Fixed bugs relating to (missing) entitities (removed dependencies to local_entitities).
* Fixed missing JavaScript.

## Version 7.2.0 (2022062100)
**New features:**
* Possibility to reduce booking option form to necessary elements only (configure simple mode).
* Toggle between simple mode and expert mode for booking option form.
* Notification list (observer list) functionality.

**Improvements:**
* Add support for returnurl for the booking options form

**Bugfixes:**
* Fixed an error with image URL.
* Make sure entities are only used when they are installed
* Fix some unset properties.
* Fixed bug in shopping cart where wrong price was taken.
* Fixed JavaScript for Moodle 4.0.
* Fixed broken URLs for Moodle 4.0.
* Commented out helpbuttons in repeat_elements groups as they cause problems with Moodle 4.0.
* Fixed navigation nodes for Moodle 4.0.

## Version 7.1.5 (2022060700)
**New features:**
* Added possibility to backup/duplicate/restore entities relations.

**Improvements:**
* If entity is set, we use it to set location and address.

**Bugfixes:**
* Restored correct order of upgrades.
* Fixed issue #190 (Upgrade fails) - https://github.com/Wunderbyte-GmbH/moodle-mod_booking/issues/190

## Version 7.1.4 (2022060200)
**Bugfixes:**
* If there are multiple image files with the same name, the first one found will be used.

## Version 7.1.3 (2022060101)
**New features:**
* Added possibility to import entities via CSV.

**Improvements:**
* Better language strings.

## Version 7.1.2 (2022060100)
**New features:**
* New teaching report (teaching journal) - allowing to define different teachers for each session, including export functionality-
* Change the semester for a booking instance - all dates of booking options will be generated newly for the selected semester.
* Added possibility to turn duplication / restoring of prices on and off in plugin settings (if turned off, default prices will be used)-

**Improvements:**
* Better presentation of teachers and link to profiles.
* Added prices to the backup routine, so they will be duplicated and backed up (if turned on in the plugin settings).

**Bugfixes:**
* Do not show separator and unique id in bookingoption_description.
* Fix a bug where the mobile service didn't get all booking options.

## Version 7.1.1 (2022050501)
**Improvements:**
* Add entities relation handler.

**Bugfixes:**
* CSV-import: add default value for column 'invisible'.
* Fix table sort order for PostgreSQL.
* Fix a bug where users couldn't see the corresponding prices.

## Version 7.1.0 (2022050400)
**New features:**
* New possibility to make options invisible for users without permission.
* Add and edit holidays (dynamic form).
* Create date series and take care of holidays.
* Add custom dates to date series (and support blocked events).

**Improvements:**
* Do not show list of booking options on course page by default.

**Bugfixes:**
* Fixed a CSS bug which disabled scrolling possibility.
* Hide dates title in signin sheet did not work correctly.

## Version 7.0.30 (2022042100)
**New features:**
* Add new config setting to include/not-include teachers.
* New default setting for addtocalendar with locking possibility.

**Improvements:**
* New dynamic semesters form.
* Add collapsible option dates to booking option description.
* New edit button in listofbookings and listofbookingscards.
* Improved sign-in-sheet with possibility to add columns for every optiondate.
* Display all prices for users which are not logged in.
* Take out shortcodes default instance.

**Bugfixes:**
* Fix a bug where prices were not imported.
* use no-reply email if no booking manager was set.
* Fix nullpointer when saving booking instances.
* department still missing in SQL
* Excel download not working with special characters.
* Missing minified files for sign-in-sheet.
* Fixed broken sessions in sign-in-sheet.
* Fix issue #185 - Error enrol users in scheduled task
* Fix missing {bookingdetails}-placeholder on viewconfirmation.php
* Option menu hidden behind top of table (if there's only one option).
* Fixed teacher duplication.
* Show images for users which are not logged in.
* Fix bug where edioptionsurl was specific to user who generated cache.
* Small fix if addtocalendar is not found in config.

**Other:**
* Fixed typo: subecribeusersctivity => subecribeusersactivity.

## Version 7.0.28 (2022032800)
**New features:**
* Add new shortcode 'mybookings'.

**Improvements:**
* Improve booking creation via singleton service.

**Bugfixes:**
* Fix a typo in settings.php which led to an error.
* Fix fallback to default image.
* Fix auto enrolment.
* Show 'booked' string when booked in booking option description.

## Version 7.0.27 (2022032601)
**New features:**
* New interface to add and edit semesters.
* Create date series with a string for reoccurring dates (e.g. 'Mo, 18:00 - 19:30').
* Upload images to booking options and show them in bookingoption_description.
* Image fallbacks: define images for a certain category (defined by custom field) and define a default image for booking options.
* New possibility to show a list of bookings (also as cards) via shortcodes.
* Display a booking option on a separate page (including possibility to buy the option, see the price etc.)

**Improvements:**
* Show booked places (instead of free ones).
* Added import of custom fields, dayofweektime string and prices to CSV importer (identified by keys).
* Refactoring for better performance.
* New singleton_service for better performance.
* Nicer presentation of booking options.
* Improved caching.
* Added collapsible description in manager view of block_booking.
* Better descriptions of booking options.
* Better date interface.
* Don't show 'booked' instead of available places.
* Added price and price category to booking option description.
* Only show booking-specific custom fields.

**Bugfixes:**
* Fixed a bug which broke the instance duplication feature.
* Fixed several bugs in caching.
* Fixed several rendering bugs.
* Added missing department to responses fields.
* Fixed badge styling.
* Fixed JS for modal loading.
* Do not show sports badge if no value exists.
* Display correct price in modal in buyforuser scenario.
* Fixed cashier's checkout.
* Fix in CSV-importer: Only run through prices if default column is present.

## Version 7.0.26 (2022021601)
**New features:**
* Nicer presentation of available places.

## Version 7.0.25 (2022021600)
**New features:**
* New sports badge
* Caching of shortcodes table data
* Show description modal in shortcodes pages

**Improvements:**
* Implement shopping cart & transition towards "unreal" deletion of booking_answers
* Support shortcode without category (returns all options)

**Bugfixes:**
* Fix a bug with PostgreSQL

## Version 7.0.24 (2022021500)
**Improvements:**
* Use message_controller for custom messages.

**Bugfixes:**
* Cancel button now works correctly.
* Wrong index in message logs of bookingoption_completed.
* Missing string in message logs of custom messages.
* Closed #183 - Inconsistancy between install.xml and upgrades

## Version 7.0.23 (2022020300)
**New features:**
* New shortcodes class enables dynamic embedding of new bookingoptions_table (using wunderbyte_table).
* Added prices to booking options.
* Show prices and action button in shortcodes table.
* Implement shopping_cart service provider & template.
* Added shopping cart functionality.
* Use new wunderbyte table caching.
* Better message logging: Sent messages get logged by Moodle event logger.
* Add possibility to choose currency globally via plugin settings.
* Add price categories to booking settings (including default category).
* Define a user profile field where the price category for each user is stored.
* Disable price categories and add default values.

**Improvements:**
* New settings classes for booking instances and booking options.
* Refactoring: New message controller class in charge of all notification e-mails.
* Placeholder {optiontimes} now works for both single and multisessions
* Add function to booking_option_settings to get display-text instead of text with separator.
* Use new wunderbyte_table with mustache.js support.

**Bugfixes:**
* Fixed broken view.php.
* Updated deprecated code.
* Book other users: Fixed a bug where selected users where not shown anymore.
* Fixed a bug where we had a duplicated admin page name.
* Fixed a bug where empty prices led to an error.
* Fixed customfields page.
* Fixed an infinite loop caused by message controller.
* Fixed message data preparation.

**Other:**
* Added behat tests.

## Version 7.0.22 (2021112900)
**Bugfixes:**
* Fixed a broken SQL statement which caused an error in the Quickfinder Block.

## Version 7.0.21 (2021112600)
**Bugfixes:**
* Fixed broken phpunit tests.
* Use correct version number for Moodle 3.11 compatibility.
* Fix bug where custom fields where not shown in modal.
* Remove obsolete $plugin->cron.
* Fix datestring to interpret HTML in coursepage_available_options template.

## Version 7.0.20 (2021111602)
**Improvements:**
* Added better feedback for CSV importer.

## Version 7.0.19 (2021110200)
**Bugfixes:**
* Fixed a bug where wrong poll url messages where sent (to both participants and teachers).
* Fixed a function in observer.php which didn't make sense.
* Fixed wrong inclusion of config.php in several files.
* Fixed deprecation of user_picture::fields in Moodle 3.11 and kept compatibility for 3.10, 3.9 and earlier.
* Fixed a bug where poll URL message was not sent to teachers.

## Version 7.0.18 (2021102500)
**Bugfixes:**
* Displaying booking option name now without separator on course page.
* Description for booked users was rendered like for unbooked in calendar.
* Fixed a bug where new bookingmanager list led to error on instantiation.
* Fixed deprecation of user_pictures in Moodle 3.11 and kept compatibility for 3.10, 3.9 and earlier.

## Version 7.0.17 (2021101900)
**Improvements:**
* Added "Department" to "Fields to display in different contexts" for report download.
* Minor code quality improvements.

## Version 7.0.16 (2021101800)
**Improvements:**
* Generic booking_options_simple_table (currently used by Bookings Quickfinder block).

## Version 7.0.15 (2021101500)
**Bugfixes:**
* Fixed deprecated implode => switch params.

**Improvements:**
* Removed "institution" from bookingoptions_simple_table (for compatibility with Bookings Quickfinder block).

## Version 7.0.14 (2021101300)
**Bugfixes:**
* Webservice only targets booking instances which are not in deletion progress.
* Minor code fixes.
* If sort by is set to coursestarttime but coursestarttime column is missing, we still order by coursestarttime.

## Version 7.0.13 (2021100400)
**Bugfixes:**
* Fix bug where calendar event was not created when course was set.

**Improvements:**
* Code quality: More logical deletion sequence.

## Version 7.0.12 (2021092900)
**Improvements:**
* Improved calendar event descriptions.
* Send status change notifications when limits (max. answers, places on waiting list) change.
* Turn off change notifications by setting the template to "0".
* Allow setting of bookingclosingtime via webservice

**Bugfixes:**
* Fixed a bug where a deleted user got 2 mails.

## Version 7.0.11 (2021092800)
**Improvements:**
* Improved availability info texts when events lie in the past.
* Bookings Quickfinder Block: number of participants, waiting list and manage responses in bookingoptions_simple_table.

**Bugfixes:**
* Always send emails from booking manager if a valid booking manager (needs to be an admin user) was defined.
  (Please keep in mind that you still need to set an outgoing noreply-address, add the domain of the booking
   manager's email to the allowed domains in outgoing email settings and set the booking manager's email address
   visible to all users in the user profile.)

## Version 7.0.10 (2021092700)
**Improvements:**
* Webservice: Add possibility to distinguish between courseid & targetcourseid
* Use uniqe booking option name with key for group creation

**Bugfixes:**
* Fix some bugs & potential bugs
* Fixed unwanted group creation

## Version 7.0.9 (2021092200)
**Improvements:**
* Only show "already booked" or "on waiting list" text in modal but not inline.

**Bugfixes:**
* Added missing fields in backup (duplication) of booking instances
* Fixed context and deletion methods in provider.php (Privacy API)

**Other:**
* Added RELEASENOTES, CHANGES and updated README

## Version 7.0.8 (2021092100)
**New features:**
* Sending of mails can be disabled by leaving the message template empty (Known issue: Currently only
  working with mails using the task send_confirmation_mails).

**Improvements:**
* Added metadata to classes/privacy/provider.php

**Bugfixes:**
* Removed "All places are booked" - as we already have new explanation string functionality (PRO) for available
  places and waiting list.
* Only show points in business_card and instance_description if there are any.

## Version 7.0.7 (2021092000)
**Improvements:**
* Added ids to rows in booking options search, so they can be hidden via CSS if needed.
* Booking instance description and business card enhancements.

**Bugfixes:**
* Fixed a bug with unique option names (Offset issue: only do "explode" if separator is part of the option name.)

**Other:**
* Introduced new table bookingoptions_simple_table which will be used by the new Booking Quickfinder block.
* Introduced CHANGES.md

## Version 7.0.6 (2021091400)
**Bugfixes:**
* Fixed a bug where courseid was always set to 0 when adding new booking options.

## Version 7.0.5 (2021091000)
**New features:**
* New cohort and group subscription (within "Book other users") for booking options.
* Unique option names
  When using CSV import for booking options, option names need to be unique. If there are multiple options with the
  same name, a unique key will be added internally to the option name. In the plugin settings, you can now define the
  separator (default: #?#) between the option name and the key.

**New PRO features:**
* Availability info texts for booking places and waiting list
  Instead of showing the numbers of available booking places and waiting list places, you can now go to the plugin
  config and activate availability info texts (you can activate them separately for available places and waiting list
  places). You can also define a percentage for the places low message. If the available booking places reach or get
  below this percentage a booking places low message will be shown. (You need to activate this feature with a PRO
  license.)

**Bugfixes:**
* Hide list of custom fields in booking option description when there are none.

## Version 7.0.3 (2021090800)
**Improvements:**
* New redirect script which fixes links that didn't work before (e.g. links in MS Outlook event texts
  after importing via {usercalendarurl}).
* Add teachers to booking option description.

**Bugfixes:**
* Fixed a bug where $booking object was null.
* Fixed a bug where description was not shown whithout organizatorname.

## Version 7.0.1 (2021090600)
**Bugfixes:**
* Fixed a bug with the placeholders in the completion mails template.
* Completion mails will only be sent if setting for sending confirmation mails is active.
* Only update start end date (of booking options) depending on sessions IF there actually ARE sessions.

## Version 7.0 (2021090100)
**New features:**
* License key checker in plugin config to activate PRO version.
* New dropdown for calendar event types.
* Up to 3 individual custom fields for multiple date sessions with autocomplete functionality.
  (Including special functionality for "TeamsMeeting", "ZoomMeeting" and "BigBlueButtonMeeting").
* Show detailed description of booking option either via modal (little info button) or inline within the
  options table (can be configured in instance settings).
* Show a "business card" of the teacher who is defined via autocomplete "Organizer name" (instance setting).
* Send change notification mails (including new mail template and new placeholder {changes} which will
  create a summary of all changes made to the booking option. The summary includes explanation texts and
  "\[DELETED\]" and "\[NEW\]" strings for text-only mails.
* Links to video meetings will only redirect to the link of the video meeting 15 minutes before until
  the end of the session.
* Session reminder e-mails (Including new mail template and functionality to set the number of days before the
  session when the e-mail should be sent.)
* Show course name, short info and a button redirecting to the available booking options on course page.
  (Can be turned on in instance settings. Short info text is customizable.)
* New placeholders {usercalendarurl} and {coursecalendarurl} (can be used in e-mail templates) to enable
  subscription to Moodle calendar via Outlook or similar calendar tool. Subscription links are made not clickable
  (styled via CSS), because they should be copied and pasted.
* New placeholder {bookingdetails} for detailed booking description including session and custom field data.
* New placeholder {gotobookingoption} linking only to the booking option.
* Booking option completion e-mails
  When you change the completion status of a user on the "Manage responses" page to "completed", an automatically
  generated e-mail will be sent to the user(s) letting them know that they have completed the booking option.
  You can edit the template for this in booking instance settings.

**New PRO features:**
* Global mail templates - each booking instance can define its source of mail templates:
  (Option 1) From within the booking instance (default)
  (Option 2) Use global mail templates defined in plugin settings
  This feature allows you to define global mail templates within the plugin config and use them within every booking
  instance on the whole platform. (You need to activate this feature with a PRO license.)
* Teacher notification e-mails including a new mail template in booking instance settings, number of
  days before the event start to notify teachers and to new placeholders to include in the template:
  {numberparticipants}: The number of successfully booked participants for the option.
  {numberwaitinglist}: The number of people on the waiting list for the option. (You need to activate this feature
  with a PRO license.)
* Webservice importer - it is now possible to import a massive amount of booking options using a CSV file
  in combination with the new importer web service. (Web service will only work with a PRO license. Contact
  info@wunderbyte.at if you need support with that.)

**Improvements:**
* Added missing German localization strings.
* Improved calendar features - show events, booked events and multiple date sessions in Moodle calendar.
  Calendar events include detailed description (supporting multiple dates sessions) and a button linking to the
  booking option.
* Booking option is prefilled with "coursename - eventtype"-Scheme
* Added autocomplete dropdown for location, institution (in booking option settings)
  and event type, organizer name (in booking instance settings).
* It is now possible to add a list of available booking options to the course page (can be turned off
  in instance settings).
* Added classes to columns and buttons in order to enable individual CSS styling.
* Redirect to view.php instead of report.php after editing options or sessions.
* Added localized help buttons for organizer name, event type, institution and location.
* Add string when neither waitinglist nor booking is possible.
* New bookingoption_completed event gets triggered when completion status of a user changes.
* CSV importer now imports optiondates (multisession) & customfields for multisessions
* Show cancel button below booked button.
* Modal is showing the info if a user is already booked or on the waiting list for an option.
* When there are no multisessions defined, the {dates} parameter for notification e-mails will use the
  single date defined within the booking option.
* Added new fields to backup.
* Show button redirecting to the booking option at upper right of the calendar modal.
* iCal attachments including detailed summary of the booking option and improved session iCals.
  Known issue: Updating events still does not work as expected with Microsoft Outlook. If you rely on
  Outlook, please use calendar subscription with the e-mail placeholders {usercalendarurl} (or {coursecalendarurl})
  instead and turn iCal attachments off in the plugin settings.
* Also duplicate associated teachers on booking option duplication.

**Bugfixes:**
* Do not add option templates twice.
* Fixed wrong calculation of available places.
* Show "Save as template" only for new booking options, not for existing ones.
* Calculate duration if not set while saving.
* Update calendar events of sessions when a booking option is edited.
* Fix bug when addtogroup is not set on saving new instance templates.
* Fix bug where booking name (->text) was required unique not only in instance, but everywhere.
* Fixed autofill of option templates (JavaScript-based).
* Fixed autofill of instance templates (JavaScript-based).
* Fixed duplicate creation of option templates.
* Fixed missing link on {bookinglink} placeholder.
* Fixed issues in backup and duplication.
