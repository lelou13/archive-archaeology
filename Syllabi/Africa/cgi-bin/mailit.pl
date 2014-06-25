#
# Perl-based Mail Tool
#
# Copyright 1998 Tod Sambar
# All rights reserved.
#
# Arbitrary Mail Form Data can be pre-pended to the mail
# message by adding input parameters that begin with the 
# characters: FD
#

#
# PARSE THE CGI FORM
#

	# Buffer the POST content
	read(STDIN, $buffer, $ENV{'CONTENT_LENGTH'});

	# Process the name=value argument pairs
	my $data;
	my $pair;
	my $name;
	my $value;
	my @args = split(/&/, $buffer);

	$data = '';
	foreach $pair (@args) 
	{
		($name, $value) = split(/=/, $pair);

		# Unescape the argument value 
		$value =~ tr/+/ /;
		$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;

		# Any fields starting with FD (form-data) are prepended
		if ($name =~ /^FD/)
		{
			$name =~ s/FD//;
			$name =~ tr/+/ /;
			$data .= $name." : ".$value."\n";
		}
		else
		{
			# Save the name=value pair for use below.
			$FORM{$name} = $value;
		}
	}

#
# VERIFY THE FORM DATA
#
	$server = $FORM{'server'};
	$from = $FORM{'from'};
	$to = $FORM{'recipient'};
	if (!($server) || !($from) || !($to))
	{
		print "<HTML><TITLE>Missing fields</TITLE><BODY>\n";
		print "Missing one of the following required arguments:<BR>\n";
		print "<I>server</I> <I>from</I> <I>to</I>\n";
		print "</BODY></HTML>\n";
		exit(1);
	}

	$subject = $FORM{'subject'};
	if (!($subject))
	{
		$subject = "none";
	}

	$bodyfile = '';
	$body = $FORM{'body'};
	if ($data)
	{
		$body = $data."\n\n".$body;
	}

#
# CLOSE SECURITY PROBLEMS.
#
	if (($server =~ /[;><&\*'\|]/ ) ||
	    ($from =~ /[;><&\*'\|]/ ) ||
	    ($subject =~ /[;><&\*'\|]/ ) ||
	    ($to =~ /[;><&\*'\|]/ ))
	{
		print "<HTML><TITLE>Invalid fields</TITLE><BODY>\n";
		print "One or more the following fields have invalid characters:<BR>\n";
		print "<I>server</I> <I>from</I> <I>to</I>\n";
		print "</BODY></HTML>\n";
		exit(1);
	}

#
# Prepare the BODY of the message
#
	if ($body)
	{
		# Write the body to a temporary file.
		do {
			$bodyfile = int(rand(99999999))."mit";
		} until !(-e $bodyfile);

		open(FILE, ">$bodyfile") || exit(1);

		print FILE $body;
		close FILE;
	}

	$attach = $FORM{'attach'};
	
	# Fixup any quote characters...
	$server =~ s/"/\\"/g;
	$from =~ s/"/\\"/g;
	$to =~ s/"/\\"/g;
	$subject =~ s/"/\\"/g;
		

#
# BUILD THE MAIL COMMAND
#
# Syntax:  mailit <server> <from> <to> <subject> [<body-file> [<attach1>]]
#

	$commandline = "..\\bin\\mailit.exe ";
	$commandline .= " \"$server\"";
	$commandline .= " \"$from\"";
	$commandline .= " \"$to\"";
	$commandline .= " \"$subject\"";
	$commandline .= " $bodyfile" if $bodyfile;
	$commandline .= " $attach" if $attach;


#
# EXECUTE THE MAILIT COMMAND
#
	system($commandline);
	$result = $?;

	# Remove the body file.
	if ($bodyfile)
	{
		unlink($bodyfile);
	}

	# Test the result...
	if ($result != 0)
	{
		print "\nMailIt Failed [$result].\n";
		print "Command: ".$commandline;
		exit(1);
	}


#
# DONE
#
	print "MailIt Succeeded.\n";

exit(0);
