Feed2JS Code
============
by Alan Levine http://cogdogblog.com/

This directory contains the latest release of the Feed2JS source code. Until June 2013, this ran as a free public service at http://feed2js.org but the time has come to retire that and let people do this DIY style (here's the story http://cogdogblog.com/2013/04/01/crossroads-for-feed2js/). Feed2JS is provided as open-source under a GNU General Public License.

ABOUT
-----
This PHP script will take an RSS feed as a value of src="...." and return a JavaScript file that can be linked remotely from any other web page. Output includes site title, link, and description as well as item site, link, and description with these outouts contolled by extra parameters.

CODE
----
https://github.com/cogdog/feed2js
 
Feed2JS makes use of the Magpie RSS parser from
http://magpierss.sourceforge.net/

### License
> GNU General Public License 
> Copyright (C) 2004-2013 Alan Levine
> This program is free software; you can redistribute it and/or
> modify it under the terms of the GNU General Public License
> as published by the Free Software Foundation; either version 2
> of the License, or (at your option) any later version.

> This program is distributed in the hope that it will be useful,
> but WITHOUT ANY WARRANTY; without even the implied warranty of
> MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
> GNU General Public License for more details
> http://www.gnu.org/licenses/gpl.html

INSTALLATION
------------

Get the Latest Source Code now available at https://github.com/cogdog/feed2js

The files provided include:

*   build.php - build page which will allow your web site users to easily generate their proper JavaScript that works from your installation of Feed2JS.
*   feed2js_config.php - local configuration, set the file paths for Magpie, etc.
*   feed2js.php - the main workhorse script; see below for configuration details.
*   feed2js.inc - another version that you can access from the same server using a PHP include method rather than JavaScript.
*   footer - text for footer on Feed2JS web pages
*   magpie (directory) - latest version of MagpieRSS (http://magpierss.sourceforge.net/)
*   magpie_debug.php - test script if you have trouble setting up Magpie (best to remove from server if it works OK)
*   magpie_simple.php - demo page for magpie
*   nosource.php - a simple error file if the script is not provided an URL for the RSS feed.
*   popup.js - external Javascript library to enable links to appear in JavaScript generated window.
*   preview.php - used to generate previews from the build form.
*   styles (directory) - sample style sheets used for the preview feature of the build script.
*   style_preview.php - generates the styled previews
*   style.php - a version of our style tool which will allow your web site users to select and modify CSS styles for their feeds.


### Directory structure

```
(main server directory)

/feed2js
  feed2js.php
     :
     :

  /magpie
	/extlib  
	  Snoopy.class.inc
	rss_cache.inc
	rss_fetch.inc
	rss_parse.inc
	rss_utils.inc
	/cache
	/cache-utf8
```
	
The "extlib" directory contains the a library file required by magpie "Snoopy.class.inc".

Inside the magpie directory are 2 directories named cache and cache_utf8 to store the cache files used by Magpie (the second are for RSS feeds that use UTF8 encoding). Make sure you change the permissions on both of these directories to be world writable with unix commands 

```
chmod 0777 cache  
chmod 0777 cache_utf8
```
	
Before going further, you may want to run the magpie test file, magpie_debug.php. If you keep this file in  /feed2js and keep the magpie files in its own subdirectory as provided in the download, there should be no edits required to make this work. Loading this in a web browser, e.g. http://www.mydomain.org/feed/magpie_debug.php will test your magpie installation.

Check if there are PHP errors-- PHP warnings are not a problem and are ignored by the main script.

If Magpie works okay, you are ready to configure the feed2js_config.php file. Look for the section labeled "INCLUDES" where you must define the correct paths for the Magpie files, the cache directory, and the time setting for having cache files expire, in seconds. the default is 60*60 or one hour. The default settings will work if you store the magpie directory inside the same directory as feed2js.php. This means that you do not have to make any changes to it.

```php
// MAGPIE SETUP ----------------------------------------------------
// Define path to Magpie files and load library
// The easiest setup is to put the 4 Magpie include
// files in the same directory:
// define('MAGPIE_DIR', './magpie/')

// Otherwise, provide a full valid file path to the directory
// where magpie sites
define('MAGPIE_DIR', './magpie/');

// access magpie libraries
require_once(MAGPIE_DIR.'rss_fetch.inc');
require_once(MAGPIE_DIR.'rss_utils.inc');

// value of 2 optionally show lots of debugging info, 0 for production
define('MAGPIE_DEBUG', 0);

// Define cache age in seconds.
define('MAGPIE_CACHE_AGE', 60*60);
```

Some Windows servers do not handle the gmmktime function in line 45- this dynamically determines the server time's offset from GMT. If you see errors on this line, you can edit this line to manually set the time zone offset, e.g.:
```php
$tz_offset = -7;
```
 
Run a known RSS Feed through your installed version of the build.php page and then test the style editor page.

If you are unable to see a feed previewed from the build page, there is likely a syntax error in feed2js.php. To isolate the problem, generate the JavaScript string, copy the URL that appears in the src="......" portion, and load it in your web browser. Any PHP errors should appear.

LIMITING USE OF YOUR INSTALL
----------------------------

You probably do not want to run a public service of Feed2JS; you can modify the feed2js_config.php file to include a domain to limit use of feeds (e.g. only sites within that domain can use your install). At this time, it is limited to either a single domain or a wildcard for subdomains (this needs to be updated in the code to allow a list of domains)

The default configuration file does not limit the usage to a domain

```php
// Restrict RSS url to domain
// Example: www.example.org => allows www.example.org and mywww.example.org
// Example: .example.org => allows www.example.org and other.example.org

// remove the comment here to activate url restriction
//$restrict_url = ".example.org";

// comment out this line to activate url restriction
unset($restrict_url);
```

Modify it to use the domain you want to limit it:

```php
// Restrict RSS url to domain
// Example: www.example.org => allows www.example.org and mywww.example.org
// Example: .example.org => allows www.example.org and other.example.org

// remove the comment here to activate url restriction
$restrict_url = ".example.org";

// comment out this line to activate url restriction
//unset($restrict_url);
```

See also a different approach for limiting via htaccess
http://darcynorman.net/2009/03/18/blocking-script-leechers-by-http-referrer/
