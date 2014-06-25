#  By Kevin Anderson
#  local (*in) = @_ if @_;
#  local ($i, $key, $val);
#
#  # Read in text
    read(STDIN,$in,$ENV{'CONTENT_LENGTH'});

  @in = split(/&/,$in);

  foreach $i (0 .. $#in) {
    # Convert plus's to spaces
    $in[$i] =~ s/\+/ /g;

    # Split into key and value.
    ($key, $val) = split(/=/,$in[$i],2); # splits on the first =.

    # Convert %XX from hex numbers to alphanumeric
    $key =~ s/%(..)/pack("c",hex($1))/ge;
    $val =~ s/%(..)/pack("c",hex($1))/ge;
   $in{$key} .= $val;
}
#
#
$price=$in{'price'};
$price =~ s/[^0-9.]//gi;
if (($price eq "") || ($price == "0")) { $price=200000; }
$interest=$in{'interest'};
$interest =~ s/[^0-9.]//gi;
if (($interest eq "") || ($interest == "0")) { $interest="8.125"; }
$years=$in{'years'};
$years =~ s/[^0-9.]//gi;
if (($years eq "") || ($years == "0")) { $years=30; }
$xprice=$in{'xprice'};
$xprice =~ s/[^0-9.]//gi;
if (($xprice eq "") || ($xprice == "0")) { $xprice=200000; }
$xinterest=$in{'xinterest'};
$xinterest =~ s/[^0-9.]//gi;
if (($xinterest eq "") || ($xinterest == "0")) { $xinterest="8.125"; }
$xyears=$in{'xyears'};
$xyears =~ s/[^0-9.]//gi;
if (($xyears eq "") || ($xyears == "0")) { $xyears=30; }

$balance=$price;
$monthly=($price*$interest/100/12)/(1-(1/(1+($interest/100/12))**($years*12)));
$total=$monthly*$years*12-$price;
$xbalance=$xprice;
$xmonthly=($xprice*$xinterest/100/12)/(1-(1/(1+($xinterest/100/12))**($xyears*12
)));
$xtotal=$xmonthly*$xyears*12-$xprice;  
print "<html><title>Compare Monthly Payments</title>";
print "<body bgcolor=\"\#FFFFFF\" text=\"\#000000\"><center>";
print "<font size=+1><b>Compare Monthly Payments</b></font>";
print "<FORM METHOD=\"POST\" ACTION=\"/cgi-bin/mortgage.pl\">"; 
print "<table border=1 cellpadding=3><tr><td><td>Mortgage 1<td>Mortgage
2";
print "<tr><td>Purchase Price";
print "<td><input name=\"price\" value=\"$price\">";
print "<td><input name=\"xprice\" value=\"$xprice\">";
print "<tr><td>Interest Rate (eg., 8.125)";
print "<td><input name=\"interest\" value=\"$interest\">";
print "<td><input name=\"xinterest\" value=\"$xinterest\">";
print "<tr><td>Number of Years (1-30)";
print "<td><input name=\"years\" value=\"$years\">";
print "<td><input name=\"xyears\" value=\"$xyears\">";
printf ("<tr><td>Amount per
payment<td>\$%.2f<td>\$%.2f",$monthly,$xmonthly);
printf ("<tr><td>Total Interest
Paid<td>\$%.2f<td>\$%.2f",$total,$xtotal);
print "</table><p>";
print "<input type=submit value=Calculate Mortgage>";
print "</body></html>";
