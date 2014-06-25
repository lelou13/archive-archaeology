#
# Sample CGI/1.1 Perl Guestbook
#
# In the event of a failure, anything written to stderr can
# be found in the "tmp" directory.

$bookfile = "../journal.htm";

# Get the input
read(STDIN, $data, $ENV{'CONTENT_LENGTH'});

# Split the name-value pairs
@pairs = split(/&/, $data);

foreach $pair (@pairs) 
{
	($name, $value) = split(/=/, $pair);

	# Convert the HTML encoding
	$value =~ tr/+/ /;
	$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
	$value =~ s/<!--(.|\n)*-->//g;

	# Convert HTML stuff as necessary.
	$value =~ s/<([^>]|\n)*>//g;

	$FORM{$name} = $value;
}

# Verify that the required data has been received.
&missing_name unless $FORM{'name'};
&missing_msg unless $FORM{'msg'};

# Read in the book for editing
# Note:  These actions on the book file should be atomic (flock()).
open (FILE, "$bookfile") || die "Can't open $bookfile: $!\n";
@LINES=<FILE>;
close(FILE);
$SIZE=@LINES;

# Open book file
open (FILE, ">$bookfile") || die "Can't open $bookfile: $!\n";

for ($i = 0; $i <= $SIZE; $i++) 
{
	$_=$LINES[$i];

	if (/<!--top-->/) 
	{
		print FILE "<!--top-->\n";
   
		print FILE "<b>$FORM{'msg'}</b><br>\n";
		print FILE "$FORM{'name'}";
		if ($FORM{'email'})
		{
			print FILE " \&lt;<A HREF=\"mailto:$FORM{'email'}\">";
			print FILE "$FORM{'email'}</A>\&gt;";
		}

		print FILE "<HR>\n";
	}
	else 
	{
		print FILE $_;
	}
}

close (FILE);


# Response message.
print "<HTML><HEAD><TITLE>Thanks</TITLE></HEAD><BODY bgcolor=white>\n";
print "<FONT SIZE=5 COLOR=#996633><B>Thanks for your message</B></FONT>\n";
print "<BR><BR>Your entry has been added to our guest book:<HR>\n";
print "<b>$FORM{'msg'}</b><br>\n";
print "$FORM{'name'}";

if ($FORM{'email'})
{
	print " &lt;<A HREF=\"mailto:$FORM{'email'}\">";
	print "$FORM{'email'}</A>&gt;";
}

print "<P><HR>\n";
print "<a href=\"../journal.htm">View your comments.</A>\n";
print "</BODY></HTML>\n";

exit;


sub missing_name
{
	print "<HTML><HEAD><TITLE>Missing Name</TITLE></HEAD><BODY>\n";
	print "<FONT SIZE=5 COLOR=#996633><B>Name field is blank...</B></FONT>\n";
	print "<BR><BR>Please repost the name section of the guestbook.<P>\n";
	print "Return to the <a href=\"../journal.htm">Guest Book</a>.\n";
	print "</BODY></HTML>\n";

	exit;
}

sub missing_msg
{
	print "<HTML><HEAD><TITLE>Missing Comments</TITLE></HEAD>\n";
	print "<FONT SIZE=5 COLOR=#996633><B>Comment field is blank...</B></FONT>\n";
	print "<BR><BR>Please repost the comment section of the guestbook.<P>\n";
	print "Return to the <a href=\"../journal.htm">Guest Book</a>.\n";
	print "</BODY></HTML>\n";

	exit;
}
