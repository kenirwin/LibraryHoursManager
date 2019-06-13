# LibraryHoursManager

## Purpose

Library Hours Manager maintains a database of rules and exceptions for what times and days a library (or other institution) is open. The Is uses General Timeperiods (e.g. Normal Semester Hours) and Special Timeperiods (e.g. Fall Break Hours) to manage a calendar of hours and provide a queryable source for a single day's hours (e.g. "today's hours") which can easily be included on web page. 

## Requirements

This software uses MySQL and PHP on the server side; JavaScript/jQuery on the web client side. 

## Usage 

### Setup

1. Unzip or git-clone the files in a directory on your web server.
2. Use the **sql_structure.sql** file to set up a new MySQL database.
3. Copy the **config_sample.php** file to a new file called **config.php** and add the MySQL user, password, database, and host information. 
4. Copy the admin/htaccess sample file to admin/.htaccess and configure it to password-protect the admin directory. Please see your system administrator for assistance setting up the .htpasswd file.
5. Using a web browser, navigate to the **Timeframes** page and begin to populate the database by adding new timeframes. A timeframe will have:
   * name
   * start date
   * end date
   * associated settings -- settings define the open/closed hours and can apply to more than one timeframe 
     * rank (general hours=1, special hours=2) -- special hours override general hours if occuring during the same time period
     * foreach day:
       * open time
        * close time
         * Y/N "open late", meaning "open past midnight"
          * Y/N "closed", meaning "closed all day"
6. Once timeframes have been set up, individual exceptions to those hours (e.g. for holidays, etc) can be set up using the Exceptions page. Add dates and time for individual exceptions. 
7. Once the timeframes and exceptions have been established for a period of time (e.g. a whole semester or year), you can generate the content for the hours.xml file using the "Generate XML Hours" button. Input the first and last dates for the file and generate the list of hours. The whole contents of the XML output should be saved in a file called hours.xml and placed in the web directory. You can also generate 

You can also generate the XML file on the command line and capture the output using:
`php generate.php action=getlist format=xmlIthaca > hours.xml`

You can copy the **GetXML-sample.sh** to **GetXML.sh** and uncomment a method that works well for you to make command-line generation of the hours easier.

Use the `calendar.php` file to read the resulting static `hours.xml` file.

### Updating Google

As of version 0.3.0, this tool can update hours for a single location on Google MyBusiness using the Google MyBusiness API. 

To configure this function, you'll need to first set up access to the API...
Add the appropriate information to the config.php file.
Then run set_google_hours.php on a weekly basis using a cron job.


## Database Tables

There are four tables: 

* `presets` - Names general time periods, e.g. "Normal Semeseter Hours", "Fall Break Hours", etc. Periods are ranked so that "Fall Break Hours" supercedes "Normal Semester Hours" because it has a higher rank.
* `settings` - Defines Sunday-Saturday hours for each day of the week for each period defined in `presets`. 
* `timeframes` - Defines the dates for which `presets` and `settings` values apply.
* `exceptions` - Single-date settings for hours -- these override any values defined in `settings`.

## Credits

* Front-end (calendar.php, style.css) by Andrew Darby and Ron Gilmour at Ithaca College; based in part on Andrew Darby's 'Code4Lib Journal' article on "Using Google Calendar to Manage Library Website Hours": http://journal.code4lib.org/articles/46 
* Back-end database structure and PHP to generate XML (generate.php, hours.class.php, etc.) by Ken Irwin at Wittenberg University
* the Manage Exceptions function uses the jTable plugin developed by Halil Ibrahim Kalkan and licensed under [MIT License] (http://opensource.org/licenses/MIT).
* the Timeframes functionality uses the Moment.js plugin [MIT License] (http://momentjs.com)

## License

This software is made available under the [Creative Commons Attribution-NonCommercial-ShareAlike (BY-NC-SA) 3.0 Unported License] (http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en_US).