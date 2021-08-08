<?
include "config.php";
?>

<HTML>
	<HEAD>
		<TITLE>ARS - Scoreboard</TITLE>
		<link rel="stylesheet" href="scorestyle.css">
<?
$RallyeID = CHTTPVars::GetValue("RallyeID"); 
if(trim($RallyeID) == "") $RallyeID = CHTTPVars::GetValue("rallyeid");

if(!@$sub)
{
		//print("<meta http-equiv=refresh content=30>");
}
?>
	</HEAD>
	
	<BODY>

	<DIV id=outerDiv style="width:100%; height:640px; overflow:hidden;">
	<DIV id=innerDiv style="position:relative; left:0px; top:0px;">
	</DIV>
	</DIV>
	<DIV id=timediv style="width:100%; height:100px; overflow:hidden;">
		<TABLE WIDTH=100% HEIGHT=100% BORDER=0>
			<TR>
				<TD ALIGN=RIGHT><B><FONT SIZE=+3>Official Rallye Time:&nbsp;</FONT></B></TD>
				<TD id=timetd ALIGN=LEFT></TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT><B><FONT SIZE=+3>Protests Close:&nbsp;</FONT></B></TD>
				<TD id=protest ALIGN=LEFT><B><FONT SIZE=+3>9:20:00pm</FONT></B></TD>
			</TR>
		</TABLE>
	</DIV>

<SCRIPT>
intScroll = setInterval("ShiftUp()",100);
refresh = setInterval("Refresh()",30000);

var direction=1;

function ShiftUp()
{
	xoDiv = document.getElementById("outerDiv");
	xiDiv = document.getElementById("innerDiv");
	xtimetd = document.getElementById("timetd");

	s = '';
	ii = parseInt(xiDiv.style.top);
	odivheight = parseInt(xoDiv.style.height);
	idivtop = parseInt(xiDiv.style.top);
	idivbot = xiDiv.offsetHeight + parseInt(xiDiv.style.top);
	idivheight = xiDiv.offsetHeight;

	currentDate = new Date()
	a = 'am';
	h = currentDate.getHours();
	m = currentDate.getMinutes();
	s = currentDate.getSeconds();

	if(h > 12)
	{
		a = 'pm';
		h -= 12;
	}

	Om='';
	if(m < 10) Om='0';

	Os='';
	if(s < 10) Os='0';


	timetd.innerHTML = '<B><FONT SIZE=+3>'+h+':'+Om+m+':'+Os+s+a+'</FONT><B>';

	if(idivheight > odivheight)
	{
		if(direction)
		{
			ii-=3;
			if(idivbot <= odivheight)
			{
				direction = 0;
			}
		}
		else
		{
			ii+=3;
			if(idivtop >= 0)
			{
				direction = 1;
			}
		}
		xiDiv.style.top=''+ii+'px';
	}
}

function handler()
{
    if (oReq.readyState == 4 /* complete */) {
        if (oReq.status == 200) 
	{
		xiDiv = document.getElementById("innerDiv");
		xiDiv.innerHTML=oReq.responseText;
        }
    }
}

function getXMLHttpRequest() 
{
    if (window.XMLHttpRequest) {
        return new window.XMLHttpRequest;
    }
    else {
        try {
            return new ActiveXObject("MSXML2.XMLHTTP.3.0");
        }
        catch(ex) {
            return null;
        }
    }
}

function Refresh()
{
	oReq = getXMLHttpRequest();

	if(oReq != null) 
	{
    		oReq.open("GET", "newscoreboard.php?RallyeID=<?=$RallyeID?>", true);
    		oReq.onreadystatechange = handler;
    		oReq.send();
	}
}

Refresh();

</SCRIPT>
	</BODY>
</HTML>
