<?
  if (!isset($_REQUEST['page']))
    $page = "main.stm";
  else
    $page = $_REQUEST['page'];
?>
<html>
<head>
<title>PDA Interface</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<frameset rows="15,195,25" frameborder="NO" border="0" framespacing="0">
  <frame src="title.php" name="topFrame" scrolling="NO" noresize>
  <frame src="<? print $page; ?>" name="mainFrame">
  <frame src="menu.php" name="menuFrame" scrolling="NO" noresize>
</frameset>
</html>