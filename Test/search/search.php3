<?php
// ----------------------------------------------------------------------------
// Zoom Search Engine 2.0 (23/7/2003)
// PHP search front-end
// 
// A fast custom website search engine using pre-indexed data files.
// Copyright (C) Wrensoft 2000 - 2003
//
// This script is for PHP 3.0.17 up till (and not including) PHP 4.0.0
//
// PLEASE NOTE: We will not provide additional features or enhancements to 
// this version of the script and it exists solely for compatibility reasons.
//
// We recommend all users to upgrade to PHP4 and use the PHP4 script
// where possible.
//
// email: zoom@wrensoft.com
// www: http://www.wrensoft.com
// ----------------------------------------------------------------------------

if(strcmp(phpversion(), '4.0.0') > 0) 
    die("This version of the script is designed for older versions of PHP. Please use the PHP4 script available.<br>");

// ----------------------------------------------------------------------------
// Settings (change if necessary)
// ----------------------------------------------------------------------------

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
$MaxKeyWordLineLen = 2000;  

$WordSplit = 1;     //0 = Only split input search phrase into words when a
                    //    Space character is found
                    //1 = Split input search phrase at Space ' ',
                    //    UnderScore '_' , Dash '-' and Plus '+' characters


// ----------------------------------------------------------------------------
// Parameter initilisation (necessary since PHP 4.2.0 and later
// prefers register_globals to be off)
// ----------------------------------------------------------------------------

// check if magic_quotes are on for Get/Post/Cookie variables
// and fix accordingly (we don't use cookies)
if (get_magic_quotes_gpc() == 1) {
    if (isset($HTTP_GET_VARS))
        while (list($key, $value) = each($HTTP_GET_VARS)) 
            $HTTP_GET_VARS["$key"] = stripslashes($value);
    if (isset($HTTP_POST_VARS))
        while (list($key, $value) = each($HTTP_POST_VARS)) 
            $HTTP_POST_VARS["$key"] = stripslashes($value);
}

// check magic_quotes for runtime stuff (reading from files, etc)
if (get_magic_quotes_runtime() == 1)
    set_magic_quotes_runtime(0);

// for compatibility with Zoom < 2.0 HTML forms
// using the POST 'searchword' parameter
if (isset($HTTP_POST_VARS['searchword']))
    $query = $HTTP_POST_VARS['searchword'];

// we use the method=GET and 'query' parameter now (for sub-result pages etc)
if (isset($HTTP_GET_VARS['zoom_query']))
    $query = $HTTP_GET_VARS['zoom_query'];

// number of results per page, defaults to 10 if not specified
if (isset($HTTP_GET_VARS['zoom_per_page']))
    $per_page = $HTTP_GET_VARS['zoom_per_page'];
else
    $per_page = 10;

// current result page number, defaults to the first page if not specified
if (isset($HTTP_GET_VARS['zoom_page']))
    $page = $HTTP_GET_VARS['zoom_page'];
else
    $page = 1;

// AND operator.
// 1 if we are searching for ALL terms
// 0 if we are searching for ANY terms (default)
if (isset($HTTP_GET_VARS['zoom_and']))
    $and = $HTTP_GET_VARS['zoom_and'];
else
    $and = 0;


function PrintEndOfTemplate($template, $line) {
    global $ZoomInfo;    
    //Let others know about Zoom.
    if ($ZoomInfo == 1)
        echo "\n<center><p><small>Search powered by the <a href=\"http://www.wrensoft.com/zoom/\"><b>Zoom Search Engine</b></a></small></p></center>\n";
    
    //Print out the end of the template
    while ($line < count($template)) {
        echo $template[$line];
        $line++;
    }
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
    $i = 0;
    $len = strlen($pattern);    
    // add "^" and "$" to res before and after the pattern, to ensure a 
    // distinct match, eg: no word stemming
    $res = "^";
    while ($i < $len) {
        $c = $pattern[$i];
        $i++;
        if ($c == '*')      
            $res = $res . ".*";         
        else 
        if ($c == '?')
            $res = $res . ".";          
        else
            //$res = $res . preg_quote($c, '/');
            $res = $res . $c;
    }           
    return $res . '$';
}           
        
// Implementation of array_values() for PHP 3 users
// Uncomment this function for PHP3
function my_array_values ($arr) {
    $t = array();
    while (list($k, $v) = each ($arr)) {
        $t[] = $v;
    }
    return $t;
}

// ----------------------------------------------------------------------------
// Main starts here
// ----------------------------------------------------------------------------

//Open and print start of result page template
$template = file ($TemplateFilename);
$numtlines = count ($template); //Number of lines in the template
$line = 0;
while ($line < $numtlines) {
    if (!stristr($template[$line], "<!--ZOOMSEARCH-->")) {
        echo $template[$line];
        $line++;
    }
    else {
        break;
    }
}
$line++;

// Replace the key text <!--ZOOMSEARCH--> with the following
if ($FormFormat > 0)
{
    // Insert the form
    print("<form method=\"GET\" action=\"".$HTTP_SERVER_VARS['PHP_SELF']."\">\n");
    print("<p>Enter search word <input type=\"text\" name=\"zoom_query\" size=\"20\" value=\"".$query."\">\n");
    print("<input type=\"submit\" value=\"Submit\">\n");
    if ($FormFormat == 2) {
        print("<small>Results per page:\n");
        print("<select name='zoom_per_page'>");
        reset($PerPageOptions);
        while (list(, $ppo) = each ($PerPageOptions)) { // equivalent to php4 foreach()
            print("<option");
            if ($ppo == $per_page)
                print(" selected=\"selected\"");
            print(">". $ppo ."</option>");
        }
        print("</select></small></p>\n");
        print("<small>Match: \n");
        if ($zoom_and == 0) {
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


//Split search phrase into words
if ($WordSplit == 1)
    $SearchWords = split ('[-_ +]', $query);
else
    $SearchWords = split ('[ ]', $query);


// Load the entire pages file into an array, all URL's on the site
$urls = file ('zoom_pages.dat');
// Load the entire page titles file into an array
$titles = file ('zoom_titles.dat');
if ($OutputStyle == 1)
    $descriptions = file ('zoom_descriptions.dat');

//Print heading
print "<h2>Search Results \"$query\"</h2>";

//Open keywords file
$fpkeywords = fopen ("zoom_keywords.dat", "r");

if ($urls == FALSE || $titles == FALSE || $fpkeywords == FALSE) {
    print("Can not find one or more of the Zoom index files.<br>Please make sure the generated index files are uploaded to the same path as this search script.<br>\n");
    return;
}



//Loop through all search words
$numwords = count ($SearchWords);
$outputline = 0;
$UseWildCards = 1;  // default as using wildcard


for ($sw = 0; $sw < $numwords; $sw++) {

    // check whether there are any wildcards used
    if (strstr($SearchWords[$sw], "*") == FALSE && strstr($SearchWords[$sw], "?") == FALSE)
        $UseWildCards = 0;
    
    //Read in a line at a time from the keywords files
    while ($data = fgetcsv ($fpkeywords, $MaxKeyWordLineLen, ",")) {
        
        // if we're not using wildcards, direct match
        if ($UseWildCards == 0) 
            $result = strcasecmp($SearchWords[$sw], $data[0]);      
        else  // if we have wildcards...
            $result = !(eregi(pattern2regexp($SearchWords[$sw]), $data[0]));
            //$result = !(preg_match(pattern2regexp($SearchWords[$sw]), $data[0]));
                   
        if ($result == 0) {         
        //Keyword found, so include it in the output list                                   
            
           
            $num = count ($data);
            for ($kw=1; $kw < $num; $kw +=2) {
                //Check if page is already in output list
                $pageexists = 0;
                $ipage = $data[$kw];
                for ($ol = 0; $ol < $outputline; $ol++) {
                    if ($output[$ol][0] == $ipage) {
                        //Page is already in output list, so add to count + extra
                        if ($output[$ol][1] > 10000) {
                            // take it easy if its too big (to prevent huge scores)
                            $output[$ol][1] += 1;
                        } else {
                            $output[$ol][1] += $data[$kw+1];    //Add in score
                            $output[$ol][1] *= 2;               //Double Score as we have two words matching
                        }
                        $output[$ol][2] += 1;               //Increase word match count
                        $pageexists = 1;
                    }
                }
                if ($pageexists == 0) {
                    //New page to add to list
                    $output[$outputline][0] = $ipage;       //Page index
                    $output[$outputline][1] = $data[$kw+1]; //Score
                    $output[$outputline][2] = 1;            //Single word match only so far
                    $outputline++;
                }
            }
            if ($UseWildCards == 0)
                break;  //This search word was found, so skip to next
        }
    }
    //Return to start of file
    rewind ($fpkeywords);
}

//Close the files
fclose ($fpkeywords);

//Get number of pages matched
$matches = $outputline;

//Sort results in order of score, use the "SortCompare" function
if ($matches > 1)
    usort ($output, "SortCompare");


//Count number of output lines that match ALL search terms
$oline = 0;
$fullmatches = 0;

while (($oline < $matches) && $numwords > 1) {
    if ($output[$oline][2] == $numwords) {
        $fullmatches++;
    } else if ($and == 1) {
        // AND search
        // Remove the not-AND result
        unset($output[$oline]);
    }
    $oline++;
}

if ($and == 1 && $numwords > 1) {
    // we need to re-index the array
    $output = my_array_values($output);
    // and the matches now are only the number of fullmatches
    $matches = count($output);
}


//Display search result information
print "<small>";
if ($matches == 1)
    print "<i>1 Match found.</i><br>";
else
if ($matches == 0)
    print "<i>No Matches found.</i><br>";
else
if ($numwords > 1 && $and == 0) {
    //OR
    $SomeTermMatches = $matches - $fullmatches;
    print "<i>$fullmatches pages found containing all search terms. ";
    if ($SomeTermMatches > 0)
        print "$SomeTermMatches pages found containing some search terms.";
    print "</i><br>";
}
else
if ($numwords > 1 && $and == 1) //AND
    print "<i>$fullmatches pages found containing all search terms.</i><br>";
else
    print "<i>$matches Matches found.</i><br>";

if ($matches < 3 && $and == 1 && $numwords > 1) {
    print "<br>You can possibly get more results searching for <a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?zoom_query=".$query."&zoom_page=".$page."&zoom_per_page=".$per_page."&zoom_and=0\">any of the terms</a>.<br>";
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
        print "<p><b>".($arrayline+1).".</b>&nbsp;<a href=\"".rtrim($urls[$ipage])."\">".rtrim($titles[$ipage])."</a><br>";
    print rtrim($descriptions[$ipage]);
            
        print "...<br>\n";          
        print "<font color=\"#999999\"><small><i>Terms matched: ". $output[$arrayline][2]. " Score: " . $score ."&nbsp;&nbsp;URL: ".rtrim($urls[$ipage])."</i></small></font></p>\n";
    }
    $arrayline++;
}

// Show links to other result pages
if ($num_pages > 1) {
    print "<p>Result Pages: ";
    if ($page > 1)
        print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?zoom_query=".$query."&zoom_page=".($page-1)."&zoom_per_page=".$per_page."&zoom_and=".$and."\">&lt;&lt; Previous</a>&nbsp;";

    for ($i = 1; $i <= $num_pages; $i++) {
        if ($i == $page) {
            print $page."&nbsp;";
        } else {
            print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?zoom_query=".$query."&zoom_page=".($i)."&zoom_per_page=".$per_page."&zoom_and=".$and."\">".$i."</a>&nbsp;";
        }
    }

    if ($page != $num_pages)
        print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?zoom_query=".$query."&zoom_page=".($page+1)."&zoom_per_page=".$per_page."&zoom_and=".$and."\">Next &gt;&gt;</a>&nbsp;";
}

//Print out the end of the template
PrintEndOfTemplate($template, $line);

//Log the search words, if required
if ($Logging == 1) {
    $LogString = Date("d-m-y, H:i:s") . ", " . $REMOTE_ADDR . ", " . " \"" .$query  . "\"" . ", Matches = " . $matches;
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
