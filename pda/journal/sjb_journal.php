<?include "conf.php"?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<table align="left" width="240" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <center>
        <font face="Arial" size="-1">
          <strong>San Juan Bautista el Precursor</strong>
        </font>
      </center>
    </td>
  </tr>
</table>
<br>
<table align="left" width="240" border="1" cellpadding="0" cellspacing="0">
<?

  if ($HTTP_GET_VARS["id"]=="1" && $journal_name != "" && $journal_msg != "") {

    $new_journal_name = conv($journal_name);
    $new_journal_msg = conv($journal_msg);
    $new_journal_email = conv($journal_email);

    $query = "INSERT INTO sjb_journal (journal_name, journal_email, journal_msg, journal_date, user_ip) VALUES ('$new_journal_name','$new_journal_email','$new_journal_msg','".date("Y-m-d h:i:s")."','".$HTTP_SERVER_VARS['REMOTE_ADDR']."')";
    $result = mysql_query($query) or die(mysql_error());
?>
  <font face="Arial" size="+1" color="navy">Thank you! <?=$journal_name?></font><br>
  <font face="Arial" size="+1" color="navy">Your message have been successfully added to the San Juan Journal.</font><br>
  Click <a href="/journal/sjb_journal.php?pg=1">here</a> to view the Journal.
</table>
</body>
</html>
<?
  die();
  } elseif ($journal_name == "" && $$HTTP_GET_VARS["id"]=="1") {
?>
  <font face="Arial" size="+2" color="red">Error!</font><br>
  <font face="Arial" size="+1" color="navy">You must enter a name.</font><br>
  Click <a href="javascript:history.back(1)">here</a> to go back.
</table>
</body>
</html>
<?
  die();
  } elseif ($journal_msg == "" && $$HTTP_GET_VARS["id"]=="1") {
?>
  <font face="Arial" size="+2" color="red">Error!</font><br>
  <font face="Arial" size="+1" color="navy">You must enter a message.</font><br>
  Click <a href="javascript:history.back(1)">here</a> to go back.
</table>
</body>
</html>
<?
  die();
  }

  $result = mysql_query("SELECT * FROM sjb_journal") or die(mysql_error());
  $num_rows = mysql_num_rows($result);

  $pages = ceil($num_rows/$max);

  if (!$pg) {
    $pg = 1;
  }
	
  if($pg!=1) {

    for($i=1;$i<$pg;$i++) {

      $cur_rows=$cur_rows+$max;
      $max_rows=$max_rows+$max;

    }
  }
?>
  <tr>
    <td align="left">
      <font face="Arial" size="1">
        <a href="sjb_journal.htm">Add Journal Entry</a>
        &nbsp; &nbsp; &nbsp;
<?
  if($pg!=1) {
?>
        <a href="sjb_journal.php?id=0&pg=<?=($pg-1)?>">Previous</a> |
<?
  }

  for($i=1;$i<=$pages;$i++) {

    if($pages>1) {
?>
        <a href="sjb_journal.php?id=0&pg=<?=$i?>"><u><?=$i?></u></a>
<?
    }
  }

  if($pg+1<=$pages) {
?>
          <a href="sjb_journal.php?id=0&pg=<?=($pg+1)?>">Next</a>
<?}?>
      </font>
    </td>
  </tr>
<?	
  $query = "SELECT journal_name, journal_email, journal_date, journal_msg, journal_id FROM sjb_journal ORDER BY journal_date";

  $result = mysql_query($query) or die(mysql_error());

  $listed=0;

  while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {	

    if($listed>=$cur_rows && $listed< $max_rows) {
?>
  <tr>
    <td align="left">
<?
      foreach($line as $journal_rec[$count]) {

        if($count==0) { 
?>
      <font face="Arial" size="1" color="navy"><strong>Name:&nbsp; </strong></font>
      <font face="Arial" size="-4"><?=$journal_rec[$count]?></font>
      <br>
<?
        } 
        if($count==1 && $journal_rec[$count]!="") { 
?>
      <font face="Arial" size="1" color="navy"><strong>E-mail: </strong></font>
      <font face="Arial" size="-4"><a href="mailto:<?=$journal_rec[$count]?>"><?=$journal_rec[$count]?></a></font>
      <br>
<?
        }
        if($count==2) { 
?>
      <font face="Arial" size="1" color="navy"><strong>Date:&nbsp; &nbsp; </strong></font>
      <font face="Arial" size="-4"><?=$journal_rec[$count]?></font>
      <br>
<?
        }
        if($count==3) { 
?>
      <font face="Arial" size="1" color="navy"><u>Message:</u></font><br>
      <font face="Arial" size="-4"><?=$journal_rec[$count]?></font>
<?
        }
        $count++;
      }
    }
    $listed++;
    $count=0;
?>
    </td>
  </tr>
<?}?>
  <tr>
    <td align="left">
      <font face="Arial" size="1">
        <a href="sjb_journal.htm">Add Journal Entry</a>
        &nbsp; &nbsp; &nbsp;
<?
  if($pg!=1) {
?>
        <a href="sjb_journal.php?id=0&pg=<?=($pg-1)?>">Previous</a> |
<?
  }

  for($i=1;$i<=$pages;$i++) {

    if($pages>1) {
?>
        <a href="sjb_journal.php?id=0&pg=<?=$i?>"><u><?=$i?></u></a>
<?
    }
  }

  if($pg+1<=$pages) {
?>
        | <a href="sjb_journal.php?id=0&pg=<?=($pg+1)?>">Next</a>
<?}?>
      </font>
    </td>
  </tr>
  <tr>
    <td align="right">
      <font face="Arial" size="1">Page <?=$pg?> of <?=(($pages==0)?1:$pages)?></font>
    </td>
  </tr>
</table>
</body>
</html>
<? mysql_close($dbase_link); ?>