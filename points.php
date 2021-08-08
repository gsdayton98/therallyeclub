<?
print("<TABLE BORDER=1>");
print("<TR>");
print("<TD>&nbsp;</TD>");
for($i=1; $i<=100; $i++)
{
	print("<TD>".($i)."</TD>");
}
print("</TR>");

for($i=10; $i>=1; $i--)
{
	print("<TR>");
	print("<TD>".(11-$i)."</TD>");
	for($j=10; $j>=$i; $j--)
	{
		print("<TD>".($j*10)."</TD>");
	}
	for($j=(10-$i); $j<99;$j++)
	{
		print("<TD>&nbsp;</TD>");
	}
	print("</TR>");
}

for($i=11; $i<=100; $i++)
{
	$frac = 100/($i);
	
	print("<TR>");
	print("<TD>".($i)."</TD>");
	for($j=0; $j<$i; $j++)
	{
		$k = intval(100-($frac*$j));
		print("<TD>".($k?$k:1)."</TD>");
	}

	for($j=$i; $j<100;$j++)
	{
		print("<TD>&nbsp;</TD>");
	}
	print("</TR>");
}

print("</TABLE>");
?>