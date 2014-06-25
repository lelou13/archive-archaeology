#
# Perl-based Upload Script
#
# Copyright 1998 Tod Sambar
# All rights reserved.
#
# Demonstrates how to upload data via multipart/form-data.
#


#
# PARSE THE CGI FORM
#
	$content_type = $ENV{'CONTENT_TYPE'};
	$content_len = $ENV{'CONTENT_LENGTH'};

	# Buffer the POST content
	binmode STDIN;
	read(STDIN, $buffer, $content_len);

	if ((!$content_type) ||
	    ($content_type eq 'application/x-www-form-urlencoded'))
	{
		# Process the name=value argument pairs
		@args = split(/&/, $buffer);

		$data = '';
		foreach $pair (@args) 
		{
			($name, $value) = split(/=/, $pair);
	
			# Unescape the argument value 
			$value =~ tr/+/ /;
			$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;

			# Save the name=value pair for use below.
			$FORM{$name} = $value;
		}
	}
	elsif ($content_type =~ m#^multipart/form-data#)
	{
		# find boundary
		($boundary = $content_type) =~ s/^.*boundary=(.*)$/\1/;

		@pairs = split(/--$boundary/, $buffer);
		@pairs = splice(@pairs,1,$#pairs-1);

		for $part (@pairs) 
		{
			($dump,$fline,$value) = split(/\r\n/,$part,3);
			next if $fline =~ /filename=\"\"/;
			$fline =~ s/^Content-Disposition: form-data; //;
			(@columns) = split(/;\s+/, $fline);
			($name = $columns[0]) =~ s/^name="([^"]+)"$/\1/g;

			if ($#columns > 0) 
			{
				if ($value =~ /^Content-Type:/) 
				{
					($dump,$dump,$value) = split(/\r\n/,$value,3);
				}
				else 
				{
					($dump,$value) = split(/\r\n/,$value,2);
				}
			}
			else 
			{
				($dump,$value) = split(/\r\n/,$value,2);
				if (grep(/^$name$/, keys(%CGI))) 
				{
					if (@{$FORM{$name}} > 0) 
					{
						push(@{$FORM{$name}}, $value);
					}
					else 
					{
						$arrvalue = $FORM{$name};
						undef $FORM{$name};
						$FORM{$name}[0] = $arrvalue;
						push(@{$FORM{$name}}, $value);
					}
				}
				else 
				{
					next if $value =~ /^\s*$/;
					$FORM{$name} = $value;
				}
				next;
			}

			$FORM{$name} = $value;
		}
	}
	else
	{
		print "Invalid content type!\n";
		exit(1);
	}

#
# VERIFY THE FORM DATA
#
	$upfile = $FORM{'upfile'};
	$upname = $FORM{'upname'};
	if (!($upfile) || !($upname))
	{
		print "<HTML><TITLE>Missing fields</TITLE><BODY>\n";
		print "No upload file specified!\n";
		print "</BODY></HTML>\n";
		exit(1);
	}


#
# CLOSE SECURITY PROBLEMS.
#
	if ($upname =~ /[;><&\*'\|\/\\]/ )
	{
		print "<HTML><TITLE>Invalid file name</TITLE><BODY>\n";
		print "The upload file name is invalid.\n";
		print "</BODY></HTML>\n";
		exit(1);
	}

#
# Write out the upload file
#
	$filename = "../docs/upload/".$upname;
	open(FILE, ">$filename") || exit(1);
	binmode FILE;

	print FILE $upfile;
	close FILE;


#
# DONE
#
	print "Upload of ".$upname." succeeded.\n";

exit(0);
