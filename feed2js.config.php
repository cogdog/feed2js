<?php
/* Feed2JS : RSS feed to JavaScript Configuration include

   Use this include to establish server specific paths
   and other common functions used by the feed2js.php
   
   See main script for all the gory details or the code site
   https://github.com/cogdog/feed2js
   
   
 */


// Define cache age. uses PHP strtotime for flexibility
define('CACHE_AGE', "1 hour");

// OTHER SETTIINGS ----------------------------------------------
// Output spec for item date string if used
// see http://www.php.net/manual/en/function.date.php
$date_format = "F d, Y h:i:s a";


// default timezone for your instance, can override server setting
// see http://www.php.net/manual/en/timezones.php

date_default_timezone_set('Europe/London');

// server time zone offset from GMT
// If this line generates errors (common on Windoze servers,
//   then figure out your time zone offset from GMT and enter
//   manually, e.g. $tz_offset = -7;

$tz_offset = gmmktime(0,0,0,1,1,1970) - mktime(0,0,0,1,1,1970);

// ERROR Handling ------------------------------------------------

// Report all errors except E_NOTICE
// This is the default value set in php.ini for Apache but often not Windows
// We recommend changing the value to 0 once your scripts are working

//ini_set('error_reporting', E_ALL^ E_NOTICE);

error_reporting(0);

// Restrict RSS url to domain
// Example: www.example.org => allows www.example.org and mywww.example.org
// Example: .example.org => allows www.example.org and other.example.org

// remove the comment here to activate url restriction
//$restrict_url = ".example.org";

// comment out this line to activate url restriction
unset($restrict_url);

// Utility to remove return characters from strings that might
// pollute JavaScript commands. While we are at it, substitute 
// valid single quotes as well and get rid of any escaped quote
// characters
function strip_returns ($text, $linefeed=" ")
{
	$subquotes = trim( preg_replace( '/\s+/', ' ', $text ) );
	return preg_replace("(\r\n|\n|\r)", $linefeed, $subquotes);
}

?>
