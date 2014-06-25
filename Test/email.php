<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><!-- InstanceBegin template="/Templates/gis.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Institute of GIS and Apatial Analysis</title>
<!-- InstanceEndEditable --> 
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<style TYPE="text/css">
<!--
-->
</style>
<!-- InstanceBeginEditable name="head" -->
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>
<!-- InstanceEndEditable --> 
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
          <td ALIGN="LEFT" VALIGN="TOP" CLASS="main-text"><h1>Contact Us</h1>
            <form name="email" method="post" action="email_send.php">
              <table width="500" border="0" cellspacing="8" cellpadding="2" bordercolor="#003300">
                <tr> 
                  <td valign="top"> <table width="500" border="0" cellpadding="5" cellspacing="3" CLASS="main-text">
                      <tr> 
                        <td width="150"> <div align="right">Your Full Name:</div></td>
                        <td> <input type="text" name="name" size="15" class="forms"> 
                        </td>
                      </tr>
                      <tr> 
                        <td width="150"> <div align="right">Your Email Address:</div></td>
                        <td> <input type="text" name="email" size="15" class="forms"> 
                        </td>
                      </tr>
                      <tr> 
                        <td width="150" valign="top"> <div align="right">Your 
                            Message:</div></td>
                        <td> <textarea name="message" cols="35" rows="8" class="forms"></textarea> 
                        </td>
                      </tr>
                      <tr> 
                        <td width="150">&nbsp;</td>
                        <td> <input name="Submit" type="submit" class="forms" onClick="MM_validateForm('name','','R','email','','NisEmail','message','','R');return document.MM_returnValue" value="Send">
                          <input NAME="clear" TYPE="RESET" ID="clear" VALUE="Clear"> 
                        </td>
                      </tr>
                    </table></td>
                </tr>
              </table>
            </form>
            <p>&nbsp;</p>
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
