
#
# Perl-based Search Engine
#
# Copyright 1998 Tod Sambar
# All rights reserved.
#

my $docroot = $ENV{'DOCUMENT_ROOT'};
my $docrootlen = length($docroot);


#
# PARSE THE CGI FORM
#

	# Buffer the POST content
	read(STDIN, $buffer, $ENV{'CONTENT_LENGTH'});

	# Process the name=value argument pairs
	my $pair;
	my $name;
	my $value;
	my @args = split(/&/, $buffer);

	foreach $pair (@args) 
	{
		($name, $value) = split(/=/, $pair);

		# Unescape the argument value 
		$value =~ tr/+/ /;
		$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;

		# Save the name=value pair for use below.
		$FORM{$name} = $value;
	}


#
# BUILD THE FILE SEARCH LIST
#
# NOTE:		We only search *.htm, *.html and *.txt files
#
# WARNING: 	This search engine is a serious memory hog
#			for large sites with many files.

	# Change to the Server's document root
	chdir($docroot);

	# Get a list of all matching files
	my $list;
	$list = `dir /b/S *.htm`;
	$list .= `dir /b/S *.html`;
	$list .= `dir /b/S *.txt`;

	my @LIST = split(/\s+/, $list);


#
# SEARCH THE FILES
#
	my $file;
	my $data;
	my $match;
	my $found;
	my $query = $FORM{'query'};

	print "Content-type: text/html\n\n";
	print "<HTML><HEAD><TITLE>PERL Search Results</TITLE></HEAD>\n";
	print "<BODY bgcolor=#FFFFFF>\n";
	print "<CENTER>\n";
	print "<FONT SIZE=6 COLOR=#99003><B>Sambar Server</B></FONT><BR>\n";
	print "<FONT SIZE=6 COLOR=#99003><I>PERL Search Results</I></FONT>\n";
	print "</CENTER><P>\n";
	print "<B>Query</B>: <I>".$query."</I><P>\n";
	print "<HR><UL>\n";

	my @query = split(/\s+/, $query);

	$found = 'no';

	foreach $file (@LIST) 
	{
		# Read the file
		open(FILE, "$file");
		@LINES = <FILE>;
		close(FILE);

		# Merge the lines of the file together
		$data = join(' ', @LINES);
		$data =~ s/\n//g;

		$match = 'no';

		if ($FORM{'logic'} eq 'and') 
		{
			foreach $term (@query) 
			{
				# Perform case insensitive comparison
				if (!($data =~ /$term/i)) 
				{
					# Term did not match.
					$match = 'no';
					last;
				}
				else 
				{
					# Term matched
					$match = 'yes';
				}
			}
		}
		elsif ($FORM{'logic'} eq 'or') 
		{
			foreach $term (@query) 
			{
				if ($data =~ /$term/i) 
				{
					# Term matched
					$match = 'yes';
					last;
				}
			}
		}
		else
		{
			print "Unrecognized query logic...\n";
		}

		if ($match eq 'yes')
		{
			$found = 'yes';

			# Strip off the document root
			$file = substr($file, $docrootlen - 1);

			# Fixup the directory slashes
			$file =~ s/\\/\//g;

      		if ($data =~ /<title>(.*)<\/title>/i) 
			{
				print "<LI><A HREF=\"$file\"> ".$1."</A><BR>\n";
      		}
      		else 
			{
				print "<LI><A HREF=\"$file\"> ".$file."</A><BR>\n";
      		}
		}
	}

	if ($found eq 'no')
	{
		print "<I><B>No search results.</B></I><BR>\n";
	}

	print "\n</UL><HR>\n";
	print "</BODY></HTML>\n";


#
# DONE
#

exit(0);
