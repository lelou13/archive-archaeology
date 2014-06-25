<?

  $hostname      = "proust";
  $journal_user  = "phpbb";
  $journal_pass  = "y&sw09lEt";
  $journal_db    = "wireless_jb";
  $max = 10;

  $journal_name  = $HTTP_POST_VARS["journal_name"];
  $journal_email = $HTTP_POST_VARS["journal_email"];
  $journal_msg   = $HTTP_POST_VARS["journal_msg"];
  $pg            = $HTTP_GET_VARS["pg"];

  $dbase_link = mysql_connect("$hostname", "$journal_user", "$journal_pass") or die("could not connect!");
  mysql_select_db($journal_db) or die("could not select database!");

  $cur_rows = 0;
  $max_rows = $max;

  function conv($str) {

    $len = strlen($str);
    $count = 0;

    while ($count < $len) {

      $ordinal = ord($str);

      if ($ordinal == 92) {

        $str = substr($str, 1);
        $ordinal = ord($str);
      }

        // check for quotation
        if ($ordinal == 34) {

          $new_str = $new_str . "&#34";
          $str = substr($str, 1);
          $count = $count + 1;
        }

        // check for apostrophe
        if ($ordinal == 39) {

          $new_str = $new_str . "&#39";
          $str = substr($str, 1);
          $count = $count + 1;
        }

        // check for backslash
        if ($ordinal == 92) {

          $new_str = $new_str . "&#92";
          $str = substr($str, 1);
          $count = $count + 1;

        } else {

        $temp = substr($str, 0, 1);
        $new_str = $new_str . $temp;
        $str = substr($str, 1);
      }
      $count = $count + 1;

    }
    return $new_str;
  }

?>