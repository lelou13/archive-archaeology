<?php
// ----------------------------------------------------------------------------
// Zoom Search Engine 2.1 (20/11/2003)
// PHP search front-end
// A fast custom website search engine using pre-indexed data files.
// Copyright (C) Wrensoft 2000 - 2003
//
// This script is designed for PHP 4.0 + only.
// If you wish to use Zoom on a PHP3 server, please use the older script
// supplied in the "/Zoom/scripts/older versions" directory.
//
// email: zoom@wrensoft.com
// www: http://www.wrensoft.com
// ----------------------------------------------------------------------------

if(strcmp('4.0.0', phpversion()) > 0)
    die("This version of the script requires PHP 4.0.0 or higher.<br>");

// ----------------------------------------------------------------------------
// Settings (change if necessary)
// ----------------------------------------------------------------------------

// Highlight matched words in results
$Highlighting = 0;              // 0 = off, 1 = on
$HighlightColor = "#FFFF40";    // Highlight colour
$HighlightLimit = 1000;         // Max number of words matched before
                                // highlighting is disabled

// Set this to your template HTML page
// (for the formatting and location of the search form)
$TemplateFilename = "search_template.html";

// The options available in the dropdown menu for number of results
// per page
$PerPageOptions = array(10, 20, 50, 100);

$FormFormat = 2;    //0 = No search form (note that you must pass parameters to
                    //    the script directly from elsewhere on your website).
                    //1 = Basic search form
                    //2 = Advanced search form (with options)

$ZoomInfo = 1;      //0 = Don't display Zoom info line at bottom of search
                    //1 = Display Zoom info line at bottom of search

$OutputStyle = 1;   //0 = Basic Style, Page Title, Score and URL
                    //1 = Descriptive Style, Match number, Page Title,
                    //    Page description, Score and URL

$Logging = 0;       //0 = No logging of words that a user enter.
                    //1 = Words are logged to a file for later analysis. (See
                    //    documentation for file permission issues)

//Path and File name of search word log file
$LogFileName = "./logs/searchwords.log";

// Maximum line length of a single line in the KeyWords file.
// Increase, if required, so that
// MaxKeyWordLineLen >= Number of web pages in site * 6
$MaxKeyWordLineLen = 30000;

$WordSplit = 1;     //0 = Only split input search phrase into words when a
                    //    Space character is found
                    //1 = Split input search phrase at Space ' ',
                    //    UnderScore '_' , Dash '-' and Plus '+' characters

$Timing = 0;        // 0 = do not perform timing on searches
                    // 1 = perform timing and display results at bottom of page

$UseUTF8 = 0;       // 0 = do not use UTF-8 for search parameters
                    // 1 = use UTF-8 encoding for search parameters

$SearchAsSubstring = 0; // 0 = do not force substring search, word must match entirely
                        // 1 = force substring search for all searchwords

$ToLowerSearchWords = 1;    // 0 = Do not change search words to lowercase (for non-alphabetic languages)
                            // 1 = Change search words to lowercase (for alphanumeric languages)

// For foreign language support, setlocale may be required on the server for
// wildcards and highlighting to work. Uncomment the following lines and specify
// the appropriate locale information
//if (setlocale(LC_ALL, "ru_RU.cp1251") == false) // for russian
//  print("Locale setting invalid");

// ----------------------------------------------------------------------------
// Parameter initialisation (necessary since PHP 4.2.0 and later
// prefers register_globals to be off)
// ----------------------------------------------------------------------------

// For versions of PHP before 4.1.0
// we will emulate the superglobals by creating references
// NOTE: references created are NOT superglobals
if (!isset($_SERVER) && isset($HTTP_SERVER_VARS))
    $_SERVER = &$HTTP_SERVER_VARS;
if (!isset($_GET) && isset($HTTP_GET_VARS))
    $_GET = &$HTTP_GET_VARS;
if (!isset($_POST) && isset($HTTP_POST_VARS))
    $_POST = &$HTTP_POST_VARS;

// check if magic_quotes are on for Get/Post/Cookie variables
// and fix accordingly (we don't use cookies so we leave them out)
if (get_magic_quotes_gpc() == 1) {
    while (list($key, $value) = each($_GET))
        $_GET["$key"] = stripslashes($value);
    while (list($key, $value) = each($_POST))
        $_POST["$key"] = stripslashes($value);
}

// check magic_quotes for runtime stuff (reading from files, etc)
if (get_magic_quotes_runtime() == 1)
    set_magic_quotes_runtime(0);

// we use the method=GET and 'query' parameter now (for sub-result pages etc)
if (isset($_GET['zoom_query'])) {
    $query = $_GET['zoom_query'];
    $query = str_replace("<", "&lt", $query);
}
else
    $query = "";

// number of results per page, defaults to 10 if not specified
if (isset($_GET['zoom_per_page']))
    $per_page = $_GET['zoom_per_page'];
else
    $per_page = 10;

// current result page number, defaults to the first page if not specified
if (isset($_GET['zoom_page']))
    $page = $_GET['zoom_page'];
else
    $page = 1;

// AND operator.
// 1 if we are searching for ALL terms
// 0 if we are searching for ANY terms (default)
if (isset($_GET['zoom_and']))
    $and = $_GET['zoom_and'];
else
    $and = 0;

// for category support
if (isset($_GET['zoom_cat']))
    $cat = $_GET['zoom_cat'];
else
    $cat = -1;  // search all categories

function PrintEndOfTemplate($template, $line) {
    global $ZoomInfo;
    //Let others know about Zoom.
    if ($ZoomInfo == 1)
        print("\n<center><p><small>Search powered by <a href=\"http://www.wrensoft.com/zoom/\"><b>Zoom Search Engine</b></a></small></p></center>\n");

    //Print out the end of the template
    while ($line < count($template)) {
        print($template[$line]);
        $line++;
    }
}

function PrintHighlightDescription($line) {
    global $matchwords;
    global $matchwords_num;
    global $HighlightColor;
    $res = $line;
    for ($i = 0; $i < $matchwords_num; $i++) {
        // replace with marker text, assumes [;:] and [:;] is not the search text...
        $res = preg_replace("|\b(" .quotemeta($matchwords[$i]) . ")\b|iU", "[;:]\\1[:;]", $res);
    }
    // replace the marker text with the html text
    // this is to avoid finding previous <span>'ed text.
    $res = str_replace("[;:]", "<span style=\"background: " . $HighlightColor . "\">", $res);
    $res = str_replace("[:;]", "</span>", $res);
    print $res;
}

// ----------------------------------------------------------------------------
// Compares the two values, used for sorting output results
// Results that match all search terms are put first, highest score
// ----------------------------------------------------------------------------
function SortCompare ($a, $b) {
    if ($a[2] < $b[2])
        return 1;
    else
    if ($a[2] > $b[2])
        return -1;
    else {
        if ($a[1] < $b[1])
            return 1;
        else
        if ($a[1] > $b[1])
            return -1;
        else
            return 0;
    }
}

// ----------------------------------------------------------------------------
// Translates a typical shell wildcard pattern ("zoo*" => "zoom" etc.)
// to a regular expression pattern. Supports only '*' and '?' characters.
// ----------------------------------------------------------------------------
function pattern2regexp($pattern) {
    global $SearchAsSubstring;
    global $ToLowerSearchWords;
    $i = 0;
    $len = strlen($pattern);
    // Add "\b" to res before and after the pattern, to ensure a distinct match
    // eg: no word stemming

    $res = "/";
    if ($SearchAsSubstring == 0)
        $res = $res . "\b";

    while ($i < $len) {
        $c = $pattern[$i];
        if ($c == '*')
            $res = $res . ".*";
        else
        if ($c == '?')
            $res = $res . ".";
        else
        if ($c == '.')
            $res = $res . "\.";
        else
            $res = $res . preg_quote($c, '/');
        $i++;
    }

    if ($SearchAsSubstring == 0)
        $res = $res . "\b";

    if ($ToLowerSearchWords == 0)
        return $res . "/";
    else
        return $res . "/i";
}

// ----------------------------------------------------------------------------
// Main starts here
// ----------------------------------------------------------------------------

if ($Timing == 1) {
    $mtime = explode(" ", microtime());
    $starttime = doubleval($mtime[1]) + doubleval($mtime[0]);
}

//Open and print start of result page template
$template = file ($TemplateFilename);
$numtlines = count ($template); //Number of lines in the template
$line = 0;
while ($line < $numtlines) {
    if (!stristr($template[$line], "<!--ZOOMSEARCH-->")) {
        print($template[$line]);
        $line++;
    }
    else {
        break;
    }
}
$line++;

$UseCats = false;
if (file_exists("zoom_cats.dat")) {
    $UseCats = true;
    $catnames = file("zoom_cats.dat");
    $catpages = file("zoom_catpages.dat");
    if ($catpages == FALSE) {
        print("Missing file zoom_catpages.dat required for category enabled search mode");
        return;
    }
}

// Replace the key text <!--ZOOMSEARCH--> with the following
if ($FormFormat > 0)
{
    // Insert the form
    print("<form method=\"GET\" action=\"".$_SERVER['PHP_SELF']."\">\n");
    print("<p>Search for: <input type=\"text\" name=\"zoom_query\" size=\"20\" value=\"".$query."\">\n");
    print("<input type=\"submit\" value=\"Submit\">\n");
    if ($FormFormat == 2) {
        print("<small>Results per page:\n");
        print("<select name='zoom_per_page'>");
        reset($PerPageOptions);
        foreach ($PerPageOptions as $ppo) {
            print("<option");
            if ($ppo == $per_page)
                print(" selected=\"selected\"");
            print(">". $ppo ."</option>");
        }
        print("</select></small></p>\n");
        if ($UseCats) {
            print("Category: ");
            print("<select name='zoom_cat'>");
            // 'all cats option
            print("<option value=\"-1\">All</option>");
            for($i = 0; $i < count($catnames); $i++) {
                print("<option value=\"". $i . "\"");
                if ($i == $cat)
                    print(" selected=\"selected\"");
                print(">". $catnames[$i] . "</option>");
            }
            print("</select>&nbsp;&nbsp;\n");
        }
        print("<small>Match: \n");
        if ($and == 0) {
            print("<input type=\"radio\" name=\"zoom_and\" value=\"0\" checked>any search words\n");
            print("<input type=\"radio\" name=\"zoom_and\" value=\"1\">all search words\n");
        } else {
            print("<input type=\"radio\" name=\"zoom_and\" value=\"0\">any search words\n");
            print("<input type=\"radio\" name=\"zoom_and\" value=\"1\" checked>all search words\n");
        }
        print("</small>\n");
    }
    print("</form>\n");
}


// Give up early if no search words provided
if (empty($query))
{
    // only display 'no query' line if no form is shown
    if ($FormFormat == 0)
        print("No search query entered.<br>");

    PrintEndOfTemplate($template, $line);
    return;
}


// prepare search query, strip quotes, trim whitespace
$query = str_replace("\"", " ", $query);
$query = str_replace("'", " ", $query);
$query = trim($query);
//Split search phrase into words
if ($WordSplit == 1)
    $SearchWords = split ("[_ +]", $query);
else
    $SearchWords = split ("[ ]", $query);


// Load the entire pages file into an array, all URL's on the site
$urls = file ('zoom_pages.dat');
// Load the entire page titles file into an array
$titles = file ('zoom_titles.dat');
if ($OutputStyle == 1)
    $descriptions = file ('zoom_descriptions.dat');

//Print heading
print("<h2>Search results for \"".$query."\"");
if ($UseCats) {
    if ($cat == -1)
        print(" in all categories");
    else
        print(" in category \"". rtrim($catnames[$cat]) . "\"");
}
print "</h2>";

//Open keywords file
$fpkeywords = fopen ("zoom_keywords.dat", "rb");
if ($urls == FALSE || $titles == FALSE || $fpkeywords == FALSE) {
    print("Can not find one or more of the Zoom index files.<br>Please make sure the generated index files are uploaded to the same path as this search script.<br>\n");
    return;
}

if (!file_exists("zoom_keywords_idx.dat")) {
    print("<b>Error:</b> Can not find zoom_keywords_idx.dat.<br>This file is now required for the new Zoom PHP script as it can improve search ");
    print("performances significantly. Please re-index your site with the latest version of Zoom Indexer and upload this file in addition to the other index files.");
    return;
}
$keywordsidx = file("zoom_keywords_idx.dat");

//Loop through all search words
$numwords = count ($SearchWords);
$pagesCount = count($urls);
$outputline = 0;
$matches = 0;
$UseWildCards = 1;  // default as using wildcard

if ($Highlighting == 1) {
    $matchwords = array();
    $matchwords_num = 0;
}

$keywordsidx_count = count($keywordsidx);

// initialise $res_table to be a 2D array of count($pages) long, filled with zeros.
if (function_exists('array_fill'))
    $res_table = array_fill(0, $pagesCount, array_fill(0, 2, 0));
else {
    $res_table = array();
    for ($i = 0; $i < $pagesCount; $i++) {
        $res_table[$i] = array();
        $res_table[$i][0] = 0;
        $res_table[$i][1] = 0;
    }
}

for ($sw = 0; $sw < $numwords; $sw++) {

    // check whether there are any wildcards used
    if (strpos($SearchWords[$sw], "*") === false && strpos($SearchWords[$sw], "?") === false)
        $UseWildCards = 0;
    else {
        $pattern = pattern2regexp($SearchWords[$sw]);
        $UseWildCards = 1;
    }

    for ($i = 0; $i < $keywordsidx_count; $i++) {
        $idxline = explode(",", $keywordsidx[$i]);

        // if we're not using wildcards, direct match
        if ($UseWildCards == 0) {
            if ($ToLowerSearchWords == 0)
                $SWord = $SearchWords[$sw];
            else if ($UseUTF8 == 1 && function_exists('mb_strtolower'))
            	$SWord = mb_strtolower($SearchWords[$sw], "UTF-8");
            else
                $SWord = strtolower($SearchWords[$sw]);

            if ($SearchAsSubstring == 0)
                $result = strcmp($SWord, $idxline[0]);
            else
                $result = strpos($idxline[0], $SWord) === false;

        } else {
            // if we have wildcards...
            $result = !(preg_match($pattern, $idxline[0]));
        }

        if ($result == 0) {
            // keyword found in the idx file
            // seek to position in $fpkeywords
            fseek($fpkeywords, $idxline[1]);
            $data = fgetcsv($fpkeywords, $MaxKeyWordLineLen, ",");
            //print "found this word in keywords.dat: " . $data[0] . " at pos: " . $idxline[1] . "<br>";
            if ($Highlighting == 1) {
                // Add to matched words if not already in list
                if (!in_array($data[0], $matchwords)) {
                    $matchwords[$matchwords_num] = $data[0];
                    $matchwords_num++;
                    if ($matchwords_num >= $HighlightLimit) {
                        $Highlighting = 0;
                        print("<small>Too many words to highlight. Highlighting disabled.</small><br><br>");
                    }
                }
            }
            $num = count ($data);
            for ($kw=1; $kw < $num; $kw +=2) {
                //Check if page is already in output list
                $pageexists = 0;
                $ipage = $data[$kw];
                if ($res_table[$ipage][0] == 0) {
                    $matches++; // count this page as a unique match
                    $res_table[$ipage][0] += $data[$kw+1];
                }
                else {
                    if ($res_table[$ipage][0] > 10000) {
                        // take it easy if its too big (to prevent huge scores)
                        $res_table[$ipage][0] += 1;
                    } else {
                        $res_table[$ipage][0] += $data[$kw+1];    //Add in score
                        $res_table[$ipage][0] *= 2;               //Double Score as we have two words matching
                    }
                }
                $res_table[$ipage][1] += 1;
            }
            if ($UseWildCards == 0)
                break;  //This search word was found, so skip to next
        }
    }
    //Return to start of file
    //rewind($fpkeywords);
}

//Close the files
fclose($fpkeywords);

//Count number of output lines that match ALL search terms
$oline = 0;
$fullmatches = 0;

// Need to traverse results if more than one word, or we're using category filter
$ResFiltered = false;
for ($i = 0; $i < $pagesCount; $i++)
{
    $IsFiltered = false;
    if ($res_table[$i][0] != 0) {
        if ($UseCats && $cat != -1) {
            // Using cats and not doing an "all cats" search
            if (rtrim($catpages[$i]) != $cat)
                $IsFiltered = true;
        }

        if ($IsFiltered == false) {
	        if ($res_table[$i][1] >= $numwords)
	            $fullmatches++;
	        else {
	        	// if AND search, only copy AND results
	            if ($and == 1)
	                $IsFiltered = true;
	        }
	    }

        if ($IsFiltered == false) {
            // copy if not filtered out
            $output[$oline][0] = $i;
            $output[$oline][1] = $res_table[$i][0];
            $output[$oline][2] = $res_table[$i][1];
            $oline++;
        } else {
            $ResFiltered = true;
        }
    }
}
if ($ResFiltered) {
    $matches = $oline;
}

//Sort results in order of score, use the "SortCompare" function
if ($matches > 1)
    usort($output, "SortCompare");

//Display search result information
print "<small>";
if ($matches == 1)
    print "<i>1 result found.</i><br>";
else
if ($matches == 0)
    print "<i>No results found.</i><br>";
else
if ($numwords > 1 && $and == 0) {
    //OR
    $SomeTermMatches = $matches - $fullmatches;
    print "<i>$fullmatches results found containing all search terms. ";
    if ($SomeTermMatches > 0)
        print "$SomeTermMatches results found containing some search terms.";
    print "</i><br>";
}
else
if ($numwords > 1 && $and == 1) //AND
    print "<i>$fullmatches results found containing all search terms.</i><br>";
else
    print "<i>$matches results found.</i><br>";

if ($matches < 3) {
    if ($and == 1 && $numwords > 1)
        print "<br>You can possibly get more results searching for <a href=\"".$_SERVER['PHP_SELF']."?zoom_query=".$query."&zoom_page=".$page."&zoom_per_page=".$per_page."&zoom_cat=".$cat."&zoom_and=0\">any of the terms</a>.<br>";
    else if ($UseCats)
        print "<br>You can possibly get more results searching in <a href=\"".$_SERVER['PHP_SELF']."?zoom_query=".$query."&zoom_page=".$page."&zoom_per_page=".$per_page."&zoom_cat=-1&zoom_and=".$and."\">all categories</a>.<br>";
}
print "</small>\n";


// Number of pages of results
$num_pages = ceil($matches / $per_page);
if ($num_pages > 1)
    print "<br>$num_pages pages of results.<br>\n";

// Determine current line of result from the $output array
if ($page == 1) {
    $arrayline = 0;
} else {
    $arrayline = (($page - 1) * $per_page);
}

// The last result to show on this page
$result_limit = $arrayline + $per_page;

// Display the results
while ($arrayline < $matches && $arrayline < $result_limit) {
    $ipage = $output[$arrayline][0];
    $score = $output[$arrayline][1];
    if ($OutputStyle == 0) {
        //Basic style
        print "<p>"."Page: <a href=\"".rtrim($urls[$ipage])."\">".rtrim($titles[$ipage])."</a><br>\n";
        print "Score: " . $score ."&nbsp;&nbsp;<small><i>URL:".rtrim($urls[$ipage])."</i></small></p>\n";
    }
    else {
        //Descriptive style
        print "<p><b>".($arrayline+1).".</b>&nbsp;<a href=\"".rtrim($urls[$ipage])."\">".rtrim($titles[$ipage])."</a>";
        if ($UseCats) {
            $catindex = rtrim($catpages[$ipage]);
            print " <font color=\"#999999\">[". rtrim($catnames[$catindex]) . "]</font>";
        }
        print "<br>\n";

        if ($Highlighting == 1)
            PrintHighlightDescription(rtrim($descriptions[$ipage]));
        else
            print rtrim($descriptions[$ipage]);

        print "...<br>\n";
        print "<font color=\"#999999\"><small><i>Terms matched: ". $output[$arrayline][2]. " Score: " . $score ."&nbsp;&nbsp;URL: ".rtrim($urls[$ipage])."</i></small></font></p>\n";
    }
    $arrayline++;
}

// Show links to other result pages
if ($num_pages > 1) {

    // 10 results to the left of the current page
    $start_range = $page - 10;
    if ($start_range < 1)
        $start_range = 1;

    // 10 to the right
    $end_range = $page + 10;
    if ($end_range > $num_pages)
        $end_range = $num_pages;

    print "<p>Result Pages: ";
    if ($page > 1)
        print "<a href=\"".$_SERVER['PHP_SELF']."?zoom_query=".$query."&zoom_page=".($page-1)."&zoom_per_page=".$per_page."&zoom_cat=".$cat."&zoom_and=".$and."\">&lt;&lt; Previous</a> ";

    for ($i = $start_range; $i <= $end_range; $i++) {
        if ($i == $page) {
            print $page." ";
        } else {
            print "<a href=\"".$_SERVER['PHP_SELF']."?zoom_query=".$query."&zoom_page=".($i)."&zoom_per_page=".$per_page."&zoom_cat=".$cat."&zoom_and=".$and."\">".$i."</a> ";
        }
    }

    if ($page != $num_pages)
        print "<a href=\"".$_SERVER['PHP_SELF']."?zoom_query=".$query."&zoom_page=".($page+1)."&zoom_per_page=".$per_page."&zoom_cat=".$cat."&zoom_and=".$and."\">Next &gt;&gt;</a> ";
}

if ($Timing == 1) {
    $mtime = explode(" ", microtime());
    $endtime   = doubleval($mtime[1]) + doubleval($mtime[0]);
    $difference = abs($starttime - $endtime);
    print "<br><br><small>Search took: " . number_format($difference, 5, '.', '') . " seconds</small>\n";
}


//Print out the end of the template
PrintEndOfTemplate($template, $line);

//Log the search words, if required
if ($Logging == 1) {
    $LogString = Date("d-m-y, H:i:s") . ", " . $_SERVER['REMOTE_ADDR'] . ", " . " \"" .$query  . "\"" . ", Matches = " . $matches;
    if ($and == 1)
        $LogString = $LogString . ", AND\n";
    else
        $LogString = $LogString . ", OR\n";

    $fp = fopen ($LogFileName, "a");
    if ($fp != false) {
        fputs ($fp, $LogString);
        fclose ($fp);
    }
}

?>
