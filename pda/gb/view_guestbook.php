<?php

  $hostname    = "proust";
  $gb_user     = "phpbb";
  $gb_pass     = "y&sw09lEt";
  $gb_database = "wireless_gb";
  $gb_line_num = 10;

  $max = $gb_line_num; // configure how many rows of message display per page
  $guest_name = $_REQUEST['guest_name'];
  $guest_email = $_REQUEST['guest_email'];
  $guest_msg = $_REQUEST['guest_msg'];
  $pg = $_REQUEST['pg'];

  $dbase_link = mysql_connect($hostname, $gb_user, $gb_pass) or die("Could not connect");
  mysql_select_db($gb_database) or die("Could not select database");

  $cur_rows = 0;
  $max_rows = $max;

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<table width="240" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <center>
        <font face="Arial" size="-1">
          <b>Guest Messages</b>
        </font>
      </center>
    </td>
  </tr>
</table>

<?php

  if ($_REQUEST['id']=="1" && $guest_name != "" && $guest_msg != "") {

    $query = "INSERT INTO guest_book (guest_name, guest_email, guest_msg, guest_date, guest_ip) VALUES ('$guest_name','$guest_email','$guest_msg','".date("Y-m-d h:i:s")."','".$HTTP_SERVER_VARS['REMOTE_ADDR']."')";
    $result = mysql_query($query) or die("Query failed");

    print("<font face=arial size=+2 color=navy>Thank you! $guest_name</font><br><font face=arial size=+1 color=navy>Your message have been successfully <br> added to the guestbook.</font>");

  } elseif ($guest_name == "" && $_REQUEST['id']=="1") {

    print("<font face=arial size=+2 color=red>Error!</font><br><font face=arial size=+1 color=navy>Guest name required.</font>");

  } elseif ($guest_msg == "" && $_REQUEST['id']=="1") {

    print("<font face=arial size=+2 color=red>Error!</font><br><font face=arial size=+1 color=navy>Guest message required.</font>");

  }
?>

<?php

  $result = mysql_query("SELECT * FROM guest_book") or die("Query failed");
  $num_rows = mysql_num_rows($result);
	
  if($pg!=1)
  {
    for($i=1;$i<$pg;$i++)
    {
      $cur_rows=$cur_rows+$max;
      $max_rows=$max_rows+$max;
    }
  }
  print("<table width=240 border=1 cellpadding=0 cellspacing=0>\n");
	
  $query = "SELECT guest_name, guest_email, guest_date, guest_msg, guest_id FROM guest_book ORDER BY guest_id";

  $result = mysql_query($query) or die("Query failed");
  $listed=0;
  while($line = mysql_fetch_array($result, MYSQL_ASSOC))
  {	
    if($listed>=$cur_rows && $listed< $max_rows)
    {
    print "  <tr>\n    <td>";
      foreach($line as $guest_rec[$count])
      {
        if($count==0) print "<br><font face=arial size=1 color=navy><b>Name&nbsp; :&nbsp;</b></font><font face=arial size=-4>$guest_rec[$count]</font>";
        if($count==1 && $guest_rec[$count]!="") print "<br><font face=arial size=1 color=navy><b>E-mail&nbsp;:&nbsp;</b></font><a href=mailto:$guest_rec[$count]><font face=arial size=-4>$guest_rec[$count]</font></a>";
        if($count==2) print "<br><font face=arial size=1 color=navy><b>Date&nbsp;&nbsp;&nbsp;   :&nbsp;</b></font><font face=arial size=-4>$guest_rec[$count]</font>";
        if($count==3) print "<br><font face=arial size=1.5 color=navy><u>Message</u></font><br><font face=arial size=-4>$guest_rec[$count]</font>";
        $count++;
      }
    }
    $listed++;
    $count=0;
    print "</td>\n    </tr>";
  }
  print("</table><br><table align=left border=0 width=240 cellspacing=0>\n  <tr>\n    <td align=left><font face=arial size=2><a href='guestbook.html'>Wireless Guestbook</a></font></td><td><center><font face=arial size=2");
	
  $pages = ceil($num_rows/$max);
	
  if($pg!=1)
  {
    print("<a href=view_guestbook.php?id=0&pg=".($pg-1)." alt='Page ".($pg-1)."'>Previous</a><<&nbsp;");
  }
  for($i=1;$i<=$pages;$i++)		
  {
  if($pages>1)
    print("<a href=view_guestbook.php?id=0&pg=$i alt='Page $i'><u>$i</u></a>&nbsp;");
  }
  if($pg+1<=$pages)
  {
    print("&nbsp;>><a href=view_guestbook.php?id=0&pg=".($pg+1)." alt='Page ".($pg+1)."'>Next</a>");
  }
  print("</font></center></td><td align=right><font face=arial size=2>Page $pg of ".(($pages==0)?1:$pages)."</font></td></tr></table>");
  mysql_close($dbase_link);

?>
</body>
</html>