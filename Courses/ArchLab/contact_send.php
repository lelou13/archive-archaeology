<!-- InstanceBegin template="/Templates/index.dwt.php" codeOutsideHTMLIsLocked="false" --><head>
<!-- InstanceBeginEditable name="doctitle" -->
<title>The Virtual Learning Lab</title>
<META http-equiv=REFRESH content=5;URL=index.php>
<!-- InstanceEndEditable --> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="virtual.css" rel="stylesheet" type="text/css" />
<meta name="Designer" content="Gonzalo Núñez" />
<meta name="Website" content="The Virtual Learning Lab" />
<meta name="Client" content="Ruben Mendoza" />
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable --> 
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="750" height="100%" border="0" cellpadding="0" cellspacing="0" class="border">
  <tr>
    <td align="left" valign="top"> 
      <table width="750" height="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td height="90" align="left" valign="top"><img src="images/banner04.jpg" width="750" height="92" border="0" usemap="#Map" /></td>
        </tr>
        <tr>
          <td height="16" align="left" valign="top" class="navigation-bg"><table width="100%" border="0" cellpadding="2" cellspacing="0" class="navigation-m">
              <tr> 
                <td align="left" valign="top"> <table width="100%" border="0" cellpadding="1" cellspacing="0">
                    <tr> 
                      <td align="left" valign="top" class="navigation-m-center" onClick="location.href = &quot;project_s.php&quot;;" onMouseOver="this.bgColor = &quot;#0099CC&quot;;" onMouseOut="this.bgColor = &quot;#FFD32F&quot;;"> 
                        <div align="center">Project Sites</div></td>
                    </tr>
                  </table></td>
                <td align="left" valign="middle"><table width="100%" border="0" cellpadding="1" cellspacing="0">
                    <tr> 
                      <td align="center" valign="middle" class="navigation-m-center" onClick="location.href = &quot;field_r.php&quot;;" onMouseOver="this.bgColor = &quot;#0099CC&quot;;" onMouseOut="this.bgColor = &quot;#FFD32F&quot;;"> 
                        <div align="center">Field 
                          Reports</div></td>
                    </tr>
                  </table></td>
                <td align="left" valign="middle"><table width="100%" border="0" cellpadding="1" cellspacing="0">
                    <tr> 
                      <td align="center" valign="middle" class="navigation-m-center" onClick="location.href = &quot;journals.php&quot;;" onMouseOver="this.bgColor = &quot;#0099CC&quot;;" onMouseOut="this.bgColor = &quot;#FFD32F&quot;;"> 
                        <div align="center">Journals</div></td>
                    </tr>
                  </table></td>
                <td align="left" valign="middle"><table width="100%" border="0" cellpadding="1" cellspacing="0">
                    <tr> 
                      <td align="center" valign="middle" class="navigation-m-center" onClick="location.href = &quot;multimedia.php&quot;;" onMouseOver="this.bgColor = &quot;#0099CC&quot;;" onMouseOut="this.bgColor = &quot;#FFD32F&quot;;"> 
                        <div align="center">Multimedia</div></td>
                    </tr>
                  </table></td>
                <td align="left" valign="middle"><table width="100%" border="0" cellpadding="1" cellspacing="0">
                    <tr> 
                      <td align="center" valign="middle" class="navigation-m-center" onClick="location.href = &quot;http://wireless_archaeology.csumb.edu/&quot;;" onMouseOver="this.bgColor = &quot;#0099CC&quot;;" onMouseOut="this.bgColor = &quot;#FFD32F&quot;;"> 
                        <div align="center">PDA Interface</div></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td align="left" valign="top"><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td width="159" align="left" valign="top" class="images"><img src="images/imges.jpg" width="160" height="400" /></td>
                <td align="left" valign="top"><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td align="left" valign="top"> <!-- InstanceBeginEditable name="Content " -->
                        <table width="100%" border="0" cellpadding="14" cellspacing="0" class="text">
                          <tr> 
                            <td align="left" valign="top"><h1>Contact us</h1></td>
                          </tr>
                          <tr> 
                            <td align="left" valign="top">
                            <div align="center">
                                <table width="100%" border="0" cellpadding="5" cellspacing="3" CLASS="text">
                                  <tr> 
                                    <td colspan="2"><font FACE="Verdana, Arial, Helvetica, sans-serif"><b> 
                                      <?php
                                        //get HTTP[POST] args
                                        $name = $HTTP_POST_VARS['name'];
                                        $email = $HTTP_POST_VARS['email'];
                                        $message = $HTTP_POST_VARS['message'];

                                        //e-mail to ASTV
                                        $msg1  =  "Hello, $name has e-mailed you a message.\n\n";
					$msg1 .=  "Senders Name:\t$name\n";
					$msg1 .=  "Senders Email:\t$email\n";
					$msg1 .=  "Senders Comments:\t$message\n\n";
					$msg1 .=  "Site Developed by Gonzalo Nunez.\nAdditional enhancements by Christian Graves.\n";

					mail("archaeology_institute@csumb.edu", "PHP Message", $msg1, "From: $email\r\n");
					
					
					//e-mail to person who submitted form.
					$msg2  = "Hello, $name.\n\n";
					$msg2 .= "We appreciate hearing from you!\n";
					$msg2 .= "Please give us a few business days to reply to your e-mail.\n";
					$msg2 .= "Thank you,\n\n";
					$msg2 .= "Institute for Archaeological Science, Technology and Visualization Social and Behavioral Sciences\n";
                                        $msg2 .= "California State University Monterey Bay\n100 Campus Center Seaside, California 93955-8001\n";
      					$msg2 .= "Site Developed by Gonzalo Nunez.\nAdditional enhancements by Christian Graves.\n";

                                        mail($email, "Thank you from archaeology.csumb.edu", $msg2, "From: archaeology_institute@csumb.edu\r\n");

                                      ?>
                                      </b></font>Thank You, <b> <?php echo $name ?>
                                      </b> for your message. You submitted the 
                                      following information....</td>
                                  </tr>
                                  <tr> 
                                    <td width="150"> <div align="right">Your Email:</div></td>
                                    <td width="490"><b> <?php echo $email ?> </b></td>
                                  </tr>
                                  <tr> 
                                    <td width="150" valign="top"> <div align="right">Your 
                                        Message:</div></td>
                                    <td width="490"> <b> <?php echo $message ?> 
                                      </b></td>
                                  </tr>
                                  <tr> 
                                    <td colspan="2"> <div align="center"> 
                                        <p>&nbsp;</p>
                                        <p>&nbsp;</p>
                                      </div></td>
                                  </tr>
                                </table>
                                <p align="left">&nbsp;</p>
                              </div>
                              </td>
                          </tr>
                        </table>
                        <!-- InstanceEndEditable --></td>
                    </tr>
                    <tr>
                      <td align="left" valign="top"><table width="100%" height="100%" border="0" cellpadding="14" cellspacing="0">
                          <tr> 
                            <td align="left" valign="top" class="text"> 
                              <hr align="center" size="1" /> 
                              <div align="center">Copyright &copy; 2004 Ruben 
                                Mendoza. All Rights Reserved<br />
                                <br />
                                Site Developed by Gonzalo N&uacute;&ntilde;ez.</div>
                              </td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
                <td width="169" align="left" valign="top" class="navigation"> 
                  <table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr> 
                      <td colspan="2"><font size="3">&nbsp;</font></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td width="100%" class="a"><a href="http://archaeology.csumb.edu/" class="right">ASTV</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="index.php" class="right">Introduction</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="grant_p.php" class="right">Grant Proposal</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="technology.php" class="right">Technology</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="personnel.php" class="right">Personnel</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="curriculum.php" class="right">Curriculum</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="forums.php" class="right">Forums</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="resources.php" class="right">Resources</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="gallery.php" class="right">Photo Gallery</a></td>
                    </tr>
                    <tr> 
                      <td>&#149;</td>
                      <td><a href="new.php" class="right">What's New</a></td>
                    </tr>
                    <tr>
                      <td>&#149;</td>
                      <td><a href="contact.php" class="right">Contact Us</a></td>
                    </tr>
                  </table>
                  <p>&nbsp; </p>
                  <p>&nbsp; </p></td>
              </tr>
            </table></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<map name="Map">
  <area shape="rect" coords="104,23,506,65" href="index.php" alt="The Virtual Learning Lab">
</map>
</body>
<!-- InstanceEnd --></html>
