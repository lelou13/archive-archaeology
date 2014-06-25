<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><!-- InstanceBegin template="/Templates/gis.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- InstanceBeginEditable name="doctitle" --> 
<title>Institute of GIS and Apatial Analysis</title>
<META http-equiv=REFRESH content=3;URL=index.php>
<!-- InstanceEndEditable --> 
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<style TYPE="text/css">
<!--
-->
</style>
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable --> 
<link HREF="gis02.css" REL="stylesheet" TYPE="text/css">
</head>

<body LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0">
<table WIDTH="750" BORDER="0" CELLSPACING="0" CELLPADDING="0">
  <tr>
    <td WIDTH="750" HEIGHT="92" ALIGN="LEFT" VALIGN="TOP"><img SRC="images/banner.jpg" WIDTH="750" HEIGHT="92"></td>
  </tr>
  <tr>
    <td HEIGHT="10" ALIGN="LEFT" VALIGN="TOP"> 
      <table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="8">
        <tr> 
          <td WIDTH="130" ALIGN="LEFT" VALIGN="TOP" NOWRAP CLASS="navigation"> 
            <p>&gt; <a HREF="index.php">Home<br>
              </a>&gt; <a HREF="mission.php">Mission</a><br>
              &gt; <a HREF="curriculum.php">Curriculum</a><br>
              &gt; <a HREF="research.php">Research Project</a><br>
              &gt; <a HREF="student.php">Student Work</a><br>
              &gt; <a HREF="gis.php">GIS Links</a><br>
              &gt; <a HREF="esri.php">GIS Workshops</a></p></td>
          <!-- InstanceBeginEditable name="text" -->
          <td ALIGN="LEFT" VALIGN="TOP" CLASS="main-text"><h1>Mission </h1>
            <table width="540" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr valign="top"> 
                <td> <form name="email" method="post" action="">
                    <table width="500" border="0" cellspacing="8" cellpadding="5" bordercolor="#003300">
                      <tr> 
                        <td valign="top"> <table width="500" border="0" cellpadding="5" cellspacing="3" CLASS="main-text">
                            <tr> 
                              <td colspan="2"><font FACE="Verdana, Arial, Helvetica, sans-serif"><b> 
                                <?php

						$msg =  "Hello, $name has e-mailed you a message.\n\n";

						$msg .=  "Senders Name:\t$name\n";

						$msg .=  "Senders Email:\t$email\n";

						$msg .=  "Senders Comments:\t$message\n\n";

						$msg .=  "Web Designed! by gonzalo nunez \n";



						$mailheaders = "From: $email\n";



						mail("gonzalo_nunez@csumb.edu", "PHP Message", $msg, $mailheaders);



 					?>
                                <?php_track_vars ?>
                                </b></font>Thank You, <b> <?php echo $name ?> 
                                </b> for your message. You submitted the following 
                                information....</td>
                            </tr>
                            <tr> 
                              <td width="150"> <div align="right">Your Email Address 
                                  :</div></td>
                              <td width="490"><b> <?php echo $email ?> </b></td>
                            </tr>
                            <tr> 
                              <td width="150" valign="top"> <div align="right">Your 
                                  Message :</div></td>
                              <td width="490"> <b> <?php echo $message ?> </b></td>
                            </tr>
                            <tr> 
                              <td colspan="2"> <div align="center"> 
                                  <p>&nbsp;</p>
                                  <p>&nbsp;</p>
                                </div></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table>
                  </form>
                  <p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><font color="#990000"><b><font size="1"> 
                    </font></b></font></font> </p>
                  <div align="left"></div></td>
              </tr>
            </table>
            <p><br>
              <br>
            </p>
            </td>
          <!-- InstanceEndEditable --></tr>
      </table></td>
  </tr>
  <tr>
    <td CLASS="credits">
<div ALIGN="CENTER">
        <p>&nbsp;</p>
        <p>Social and Behavioral Sciences Center<br>
          <font COLOR="#000000">&middot; &middot; &middot; &middot; &middot; &middot; 
          &middot; &middot; &middot; &middot; &middot; &middot; &middot; &middot; 
          &middot; &middot; &middot; &middot; &middot; &middot; &middot; &middot; 
          &middot; &middot; &middot; &middot; &middot; &middot; &middot; &middot; 
          &middot; &middot; &middot; &middot; &middot; &middot; &middot; &middot; 
          &middot; &middot; &middot; &middot; &middot; &middot; &middot; &middot; 
          &middot; &middot; &middot; &middot; &middot; &middot; &middot; &middot; 
          &middot; &middot; &middot; </font><br>
          &copy;2003 CSU Monterey Bay. All rights reserved. Web Designed by Gonzalo 
          N&uacute;&ntilde;ez. <a HREF="email.php">Contact Us</a>.</p>
      </div></td>
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
