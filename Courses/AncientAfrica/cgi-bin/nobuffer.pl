$| = 1;  # Use unbuffered output
print "Content-type: text/html\n\n<H1>Testing unbuffered output</H1>\n";
foreach $_ (0 .. 29) {
	print 30-$_,"<BR>\n";
	sleep(1);
}

print "0<BR>Done.\n";
