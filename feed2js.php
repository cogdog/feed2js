<?php
/*  Feed2JS : RSS feed to JavaScript src file

	VERSION 2.5
	
	ABOUT
	This PHP script will take an RSS feed as a value of src="...."
	and return a JavaScript file that can be linked 
	remotely from any other web page. Output includes
	site title, link, and description as well as item site, link, and
	description with these outouts contolled by extra parameters.
	
	Developed by Alan Levine initially released 13.may.2004
	http://cogdogblog.com/
	
	PRIMARY SITE:
	http://feed2js.org/
	 
	CODE:
	https://github.com/cogdog/feed2js
     
	Feed2JS makes use of the Magpie RSS parser from
	 http://magpierss.sourceforge.net/
	
   ------------- small print ---------------------------------------
	GNU General Public License 
	Copyright (C) 2004-2013 Alan Levine
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details
	http://www.gnu.org/licenses/gpl.html
	------------- small print ---------------------------------------

*/

// ERROR CHECKING FOR NO SOURCE -------------------------------

$script_msg = '';
$src = (isset($_GET['src'])) ? $_GET['src'] : '';

// trap for missing src param for the feed, use a dummy one so it gets displayed.
if (!$src or strpos($src, 'http://')!=0) $src=  'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/nosource.php';

// Filter for malicious use of <script> and related tags. Uses https://www.php.net/manual/en/filter.filters.validate.php, requiring PHP7 or later
$src = filter_var($src,FILTER_VALIDATE_URL,FILTER_FLAG_PATH_REQUIRED);
//disasemble src into component parts
$parsed_url_src = parse_url($src);
//Re-asemble src, currently only works with absolute URLs
$src = $parsed_url_src['scheme'].'://'.filter_var($parsed_url_src['host'],FILTER_VALIDATE_DOMAIN).strip_tags($parsed_url_src['path']);

// MAGPIE  SETUP ----------------------------------------------------
// access configuration settings
require_once('feed2js_config.php');

//  check for utf encoding type
$utf = (isset($_GET['utf'])) ? $_GET['utf'] : 'n';

if ($utf == 'y') {
	define('MAGPIE_CACHE_DIR', MAGPIE_DIR . 'cache_utf8/');
	// chacrater encoding
	define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
	

} else {
	define('MAGPIE_CACHE_DIR', MAGPIE_DIR . 'cache/');
	define('MAGPIE_OUTPUT_ENCODING', 'ISO-8859-1');
}

// GET VARIABLES ---------------------------------------------
// retrieve values from posted variables

// flag to show channel info
$chan = (isset($_GET['chan'])) ? $_GET['chan'] : 'n';

// variable to limit number of displayed items; default = 0 (show all, 100 is a safe bet to list a big list of feeds)

$num = (isset($_GET['num'])) ? $_GET['num'] : 0;
if ($num==0) $num = 100;

// indicator to show item description,  0 = no; 1=all; n>1 = characters to display
// values of -1 indicate to displa item without the title as a link
// (default=0)
$desc = (isset($_GET['desc'])) ? $_GET['desc'] : 0;

// flag to show author of items, values: no/yes (default=no)
$auth = (isset($_GET['au'])) ? 'y' : 'n';

// flag to show date of items, values: no/yes (default=no)
$date = (isset($_GET['date'])) ? $_GET['date'] : 'n';

// time zone offset for making local time, 
// e.g. +7, =-10.5; 'feed' = print the time string in the RSS w/o conversion
$tz = (isset($_GET['tz'])) ? $_GET['tz'] : 'feed';


// flag to open target window in new window; n = same window, y = new window,
// other = targeted window, 'popup' = call JavaScript function popupfeed() to display
// in new window (default is n)

$targ = (isset($_GET['targ'])) ? $_GET['targ'] : 'n';
if ($targ == 'n') {
	$target_window = ' target="_self"';
} elseif ($targ == 'y' ) {
	$target_window = ' target="_blank"';
} elseif ($targ == 'popup') {
	$target_window = ' onClick="popupfeed(this.href);return false"';
} else {
	$target_window = ' target="' . $targ . '"';
}

// flag to show feed as full html output rather than JavaScript, used for alternative
// views for JavaScript-less users. 
//     y = display html only for non js browsers (NO LONGER USED)
//     n = default (JavaScript view)
//     a = display javascript output but allow HTML 
//     p  = display text only items but convert linefeeds to BR tags

// default setting for no conversion of linebreaks
$html = (isset($_GET['html'])) ? $_GET['html'] : 'n';

$br = ' ';
if ($html == 'a') {
	$desc = 1;
} elseif ($html == 'p') {
	$br = '<br />';
}

// optional parameter to use different class for the CSS container
$rss_box_id = (isset($_GET['css'])) ? '-' . $_GET['css'] : '';

// optional parameter to use different class for the CSS container
$play_podcast = (isset($_GET['pc'])) ? $_GET['pc'] : 'n';


// PARSE FEED and GENERATE OUTPUT -------------------------------
// This is where it all happens!


// check if site has a setting to restrict to a url
if (isset($restrict_url)) {
	if (strpos($src, 'https://') == 0) {
		$src_host = substr($src, 8);
	} else {
		$src_host = substr($src, 7);
	}
	$src_pos = strpos($src_host,"/");
	if ($src_pos) {
		$src_host = substr($src_host,0, $src_pos);
	}
}
if (isset($restrict_url) && substr($src_host, strlen($src_host)-strlen($restrict_url)) != $restrict_url) {
	$src = htmlspecialchars($src);
	
	$str.= "document.write('<div class=\"rss-box" . $rss_box_id .
		"\"><p class=\"rss-item\"><em>Error:</em> on feed <strong>" .
		$src . "</strong>. " .
		"Feeds are allowed only from URLs from the sites http://*" .
		$restrict_url . " and https://*" . $restrict_url .
		"</p></div>');\n";
		
} else {


	$rss = @fetch_rss( $src );
	
	// begin javascript output string for channel info
	$str= "document.write('<div class=\"rss-box" . $rss_box_id . "\">');\n";
	
	
	// no feed found by magpie, return error statement
	if  (!$rss) {
		//$src needs to be escape before being displayed as an error message, in order to prevent XSS 
		$src = htmlspecialchars($src);
		
		$str.= "document.write('<p class=\"rss-item\">$script_msg<em>Error:</em> Feed failed! Causes may be (1) No data  found for RSS feed $src; (2) There are no items are available for this feed; (3) The RSS feed does not validate.<br /><br /> Please verify that the URL <a href=\"$src\">$src</a> works first in your browser and that the feed passes a <a href=\"http://feedvalidator.org/check.cgi?url=" . urlencode($src) . "\">validator test</a>.</p></div>');\n";
	
	
	} else {
	
	
		// Create CONNECTION CONFIRM
		// create output string for local javascript variable to let 
		// browser know that the server has been contacted
		$feedcheck_str = "feed2js_ck = true;\n\n";
	
		// we have a feed, so let's process
		if ($chan == 'y') {
		
			// output channel title linked and description	
			$str.= "document.write('<p class=\"rss-title\"><a class=\"rss-title\" href=\"" . trim($rss->channel['link']) . '"' . $target_window . ">" . addslashes(strip_returns($rss->channel['title'])) . "</a><br /><span class=\"rss-item\">" . addslashes(strip_returns(strip_tags($rss->channel['description']))) . "</span></p>');\n";
		
		} elseif ($chan == 'title') {
			// output title only with link
			$str.= "document.write('<p class=\"rss-title\"><a class=\"rss-title\" href=\"" . trim($rss->channel['link']) . '"' . $target_window . ">" . addslashes(strip_returns($rss->channel['title'])) . "</a></p>');\n";
			
		} elseif ($chan == 'titlelinkno') {
			// output title only with no link
			$str.= "document.write('<p class=\"rss-title\">" . addslashes(strip_returns($rss->channel['title'])) . "</p>');\n";
		
		}  elseif ($chan == 'linkno') {
			// output title and descript only with no link
			$str.= "document.write('<p class=\"rss-title\">" . addslashes(strip_returns($rss->channel['title'])) . "<br /><span class=\"rss-item\">" . addslashes(strip_returns(strip_tags($rss->channel['description']))) . "</span></p>');\n";
		}
		
		// begin item listing
		$str.= "document.write('<ul class=\"rss-items\">');\n";
			
		// Walk the items and process each one
		$all_items = array_slice($rss->items, 0, $num);
		
		foreach ( $all_items as $item ) {
			
			// set defaults thanks RPFK
			if (!isset($item['summary'])) $item['summary'] = ''; 
			$more_link = '';
			
			// create output for item author
			
			
			$author_str = '';
			if ($auth == 'y') {
				if (isset($item['dc']['creator'])) {
					$author_str = ' <span class="rss-item-auth">(' . addslashes(strip_tags($item['dc']['creator'])) . ')</span>';
				
				} else {
					if (isset($item['author_name'])) {
						$author_str = ' <span class="rss-item-auth">(' . addslashes(strip_tags($item['author_name'])) . ')</span>';	
					}
				}
			
			}
			
			
			
			if ($item['link']) {
				// link url
				$my_url = addslashes($item['link']);
			} elseif  ($item['guid']) {
				//  feeds lacking item -> link
				$my_url = ($item['guid']);
			} else {	
				// nothing to link to
				$my_url = false;
			}
			
			
			if ($desc < 0) {
				$str.= "document.write('<li class=\"rss-item\">');\n";
				
			} elseif ($item['title']) {
				// format item title
				$my_title = addslashes(strip_returns($item['title']));
				
							
	
				// write the title strng
				
				if ($my_url) {
					// link title
					$str.= "document.write('<li class=\"rss-item\"><a class=\"rss-item\" href=\"" . trim($my_url) . "\"" . $target_window . '>' . $my_title . '</a>' .  $author_str . "<br />');\n";
				} else {
					// no link
					$str.= "document.write('<li class=\"rss-item\">" . $my_title . ' ' .  $author_str . "<br />');\n";
				}
	
	
			} else {
				
				$str.= "document.write('<li class=\"rss-item\">');\n";
				
				if ($my_url) {
					// if no title, build a link to tag on the description
					$more_link = " <a class=\"rss-item\" href=\"" . trim($my_url) . '"' . $target_window . ">&laquo;details&raquo;</a>";
				}
			}
		
			// print out date if option indicated
	
			if ($date == 'y') {
						
				if ($tz == 'feed') {
				//   echo the date/time stamp reported in the feed
	
					if ($item['pubdate'] != '') {
						// RSS 2.0 is already formatted, so just use it
						$pretty_date = $item['pubdate'];
					} elseif ($item['published'] != "") {
						// ATOM 1.0 format, remove the "T" and "Z" and the time zone offset
						$pretty_date = str_replace("T", " ", $item['published']);
						$pretty_date= str_replace("Z", " ", $pretty_date);
		
					} elseif ($item['issued'] != "") {
						// ATOM 0.3 format, remove the "T" and "Z" and the time zone offset
						$pretty_date = str_replace("T", " ", $item['issued']);
						$pretty_date= str_replace("Z", " ", $pretty_date);
					} elseif ( $item['dc']['date'] != "") {
						// RSS 1.0, remove the "T" and the time zone offset
						$pretty_date = str_replace("T", " ", $item['dc']['date']);
						$pretty_date = substr($pretty_date, 0,-6);
					} else {
					
						// no time/date stamp, 
						$pretty_date =  'n/a';
					}
	
				} else {
					// convert to local time via conversion to GMT + offset
					
					// adjust local server time to GMT and then adjust time according to user
					// entered offset.
					
					// let's see what kind of timestamps we can pull...
					if ($item['date_timestamp'] != "") {
						$ts = $item['date_timestamp'];
					}  elseif ($item['published'] != "") {
						$ts = strtotime($item['published']);
					} elseif ($item['issued'] != "") {
						$ts = strtotime($item['issued']);
					} elseif ( $item['dc']['date'] != "") {
						$ts = strtotime($item['dc']['date']);
					} else {
						$ts = time();
					}
					
					$pretty_date = date($date_format, $ts - $tz_offset + $tz * 3600);
				
				}
		
				$str.= "document.write('<span class=\"rss-date\">$pretty_date</span><br />');\n"; 
			}
	
			// link to podcast media if availavle
			
			if ($play_podcast == 'y' and is_array($item['enclosure'])) {
				$str.= "document.write('<div class=\"pod-play-box\">');\n";
				for ($i = 0; $i < count($item['enclosure']); $i++) {
				
					// display only if enclosure is a valid URL
					//if (strpos($item['enclosure'][$i]['url'], 'http://')!=0) {
						$str.= "document.write('<a class=\"pod-play\" href=\"" . trim($item['enclosure'][$i]['url']) . "\" title=\"Play Now\" target=\"_blank\"><em>Play</em> <span> " .  substr(trim($item['enclosure'][$i]['url']), -3)  . "</span></a> ');\n";
					//}
				
				}
				
				$str.= "document.write('</div>');\n";
			
			}
	
		
			// output description of item if desired
			if ($desc) {
			
				if ($item['atom_content']) {
					// Atom content - note that wordpress.com feeds return bad data here "A"
					// so revert to description if this is the case.
					$my_blurb = ($item['atom_content'] == "A") ? $item['description'] :   html_entity_decode ( $item['atom_content'], ENT_NOQUOTES, MAGPIE_OUTPUT_ENCODING);
				
				} else if ($item['content']) {
				
					
					
					// Atom/encocded content support (thanks David Carter-Tod)
				
					$my_blurb =   html_entity_decode ( $item['content'], ENT_NOQUOTES, MAGPIE_OUTPUT_ENCODING);
				
					
				} else {   
					$my_blurb = $item['summary'];
				}
				
				// strip html
				if ($html != 'a') $my_blurb = strip_tags($my_blurb);
				
				// trim descriptions
				if ($desc > 1) {
				
					// display specified substring numbers of chars;
					//   html is stripped to prevent cut off tags
					//   make sure we dont chop UTF-8 characters
					
					
					if ($utf == 'y') {
						$my_blurb = mb_substr($my_blurb, 0, $desc, 'UTF-8') . '...';
					} else {
						$my_blurb = substr($my_blurb, 0, $desc) . '...';
					}
	
				}
		
			
				$str.= "document.write('" . addslashes(strip_returns($my_blurb, $br)) . "');\n"; 
				
			}
				
			$str.= "document.write('$more_link</li>');\n";	
		}
	
	
		$str .= "document.write('</ul></div>');\n";
	} // end restrict_url
}

// Render as JavaScript
// START OUTPUT
// headers to tell browser this is a JS file
if ($rss) header("Content-type: application/x-javascript"); 

// Spit out the results as the series of JS statements
echo $feedcheck_str . $str;


?>
