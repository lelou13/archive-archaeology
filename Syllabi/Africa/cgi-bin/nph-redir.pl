#!/usr/bin/perl
#
# Redirection Script        Version 2.3
# Created by Jeff Carnahan  jeffc@terminalp.com
# Created on: 4/10/95       Last Modified on: 05/30/96 13:45 
# Scripts Archive:          http://www.terminalp.com/scripts/
#
# Copyright (C) 1996 - Jeffrey D. Carnahan
#   Copyright Information Can Be found in the attacted README file.
#
# ---------------------------------------------------------------------
# Program Specific Quickie Notes:
#     * Make Sure The First Line Is Pointing To The Correct Location Of Perl.
#     * Make Sure This Program is chmodded with the permissions '755'.
#     * Make Sure This Programs Name begins with 'nph-'!!
#
# ---------------------------------------------------------------------
# ---------------------------------------------------------------------
# Don't Modify Anything Past This Line Unless You Know What Your Doing!
# ---------------------------------------------------------------------
# ---------------------------------------------------------------------
#

&GetInput;
&RedirectUser;

# ---------------------------------------------------------------------

sub GetInput {

    read(STDIN, $buffer, $ENV{'CONTENT_LENGTH'});
    @forminput = split(/&/, $buffer);
    @moreinput = split(/&/, $ENV{'QUERY_STRING'});
    @finalinput = (@forminput, @moreinput);
    foreach (@finalinput) {
        tr/+/ /;
        ($name, $value) = split(/=/, $_);
        $value =~ s/%(..)/pack("C", hex($1))/eg;
        $name  =~ tr/A-Z/a-z/;
        $in{$name}=$value;
    }
}

# ---------------------------------------------------------------------

sub RedirectUser {
    if ($in{'dest'}) {
        print "HTTP/1.0 302 Found\n";
	    print "Window-target: $in{'target'}\n" if ($in{'target'});
        print "Location: $in{'dest'}\n\n";
        exit;
    } else {
        print "HTTP/1.0 200 OK\n";
        print "Window-target: $in{'target'}\n" if ($in{'target'});
        print "Content-type: text/html\n\n";
        print "<HTML><HEAD><TITLE>Error!</TITLE></HEAD><BODY BGCOLOR=\"\#FFFFFF\">\n";
        print "<H1>Error!</H1>\n";
        print "<P>You didn't supply information for the destination...\n";
        print "</BODY></HTML>\n";
        exit;
    }
}

# ---------------------------------------------------------------------
# EOF
