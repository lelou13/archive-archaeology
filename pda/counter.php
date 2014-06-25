<?php

  $hostname    = "proust";
  $co_user     = "phpbb";
  $co_pass     = "y&sw09lEt";
  $co_database = "wireless_db";

  $dbase_link = mysql_connect("$hostname","$co_user","$co_pass") or die("could not connect");
  mysql_select_db("$co_database") or die("could not select database");

  $query = "SELECT * FROM wap_counter where id = 'wap_counter'";
  $result = mysql_query($query) or die("Query failed1");

  while($row = mysql_fetch_row($result))  { 

    $xcount = $row[1];
    $xcount++;
  }

  $query = "UPDATE wap_counter SET count = '$xcount' WHERE id = 'wap_counter'";
  $result = mysql_query($query) or die("Query failed");

  print $xcount;

?>