print "Sambar Server CGI Environment Variables<P>";
print "<PRE>";

while(($key,$val) = each(ENV))
{
	print "<B>$key</B>: $val \n";
}

print "</PRE>";
