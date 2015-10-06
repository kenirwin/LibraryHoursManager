# LibraryHoursManager

## Usage 
Generate Ithaca-College-style XML:
`generate.php?action=getlist&format=xmlIthaca`

Use the `calendar.php` file to read the resulting static XML

Or on the command line:
`php generate.php action=getlist format=xmlIthaca`

## Database Setup

There are four tables: 

* `presets` - Names general time periods, e.g. "Normal Semeseter Hours", "Fall Break Hours", etc. Periods are ranked so that "Fall Break Hours" supercedes "Normal Semester Hours" because it has a higher rank.
* `settings` - Defines Sunday-Saturday hours for each day of the week for each period defined in `presets`. 
* `timeframes` - Defines the dates for which `presets` and `settings` values apply.
* `exceptions` - Single-date settings for hours -- these override any values defined in `settings`.

Currently, the values for these tables are entered manually. Hopefully future versions will allow for GUI data management/entry with error correction.

## Credits

* Front-end (calendar.php, style.css) by Andrew Darby and Ron Gilmour at Ithaca College; based in part on Andrew Darby's 'Code4Lib Journal' article on "Using Google Calendar to Manage Library Website Hours": http://journal.code4lib.org/articles/46 
* Back-end database structure and PHP to generate XML (genereate.php, hours.class.php, etc) by Ken Irwin at Wittenberg University


