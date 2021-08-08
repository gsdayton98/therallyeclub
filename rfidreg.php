<?
include "config.php";

$ThisStep=48;

$RallyeID = CHTTPVars::GetValue("RallyeID");
$TestMode = intval(CHTTPVars::GetValue("TestMode"));

$CardNumber = CHTTPVars::GetValue("cardnumber");

if($DTRC)
{
	$DClub = "TRC";
	$DTRCCHECKED = "CHECKED";
}

if($NTRC)
{
	$NClub = "TRC";
	$NTRCCHECKED = "CHECKED";
}

if($PTRC)
{
	$PClub = "TRC";
	$PTRCCHECKED = "CHECKED";
}

if($CardNumber != "")
{
	
	$strSQL="select CarClass,DName,DEmail,DClub,NName,NEmail,NClub,PName,PEmail,PClub from rfidcard where CardNumber = '$CardNumber'";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray(false);
	if(count($arr))
	{
		list($CarClass,$DName,$DEmail,$DClub,$NName,$NEmail,$NClub,$PName,$PEmail,$PClub) = $arr[0];
	}
}

// recall data from card base

if(!IsSetStep(16,$RallyeID)) // have I completed the previous step?
	redirect("configdone.php?RallyeID=$RallyeID");


function myprint($str)
{
	print($str."\n");
}


// Since in HTML rows come first, we have a funky order

// this should really be databased
$arrClasses[1] = "First Timer";
$arrClasses[2] = "Beginner";
$arrClasses[3] = "Novice";
$arrClasses[4] = "Senior";
$arrClasses[5] = "Expert";
$arrClasses[6] = "Master";

// must load this information regardless of weather we are are recording or scoring
$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeName, RallyeDate, RallyeMaster, TopX, TopY, CMX, CMY, CPX, CPY, BotX, BotY, CPLoc");
if(count($arr))
{
	list($RallyeName, $RallyeDate, $RallyeMaster, $TopX, $TopY, $CMX, $CMY, $CPX, $CPY, $BotX, $BotY, $CPLoc) = $arr[0];
}

?>
<HTML>
	<HEAD>
		<TITLE>ARS - Register</TITLE>
		<link rel="stylesheet" href="bigstylesheet.css">
		</SCRIPT>

	</HEAD>
	
	<BODY>
		<TABLE WIDTH="100%">
			<TR>
				<TD WIDTH="30%" ALIGN=LEFT VALIGN=TOP>
					<B><?=$RallyeMaster?></B>
				</TD>

				<TD WIDTH="40%" ALIGN=CENTER VALIGN=TOP>
					<B><?=$RallyeName?></B>
				</TD>

				<TD WIDTH="30%" ALIGN=RIGHT VALIGN=TOP>
					<B><?=$RallyeDate?></B>
				</TD>
			</TR>
		</TABLE>
		<BR>
		<H2>Registration Verify: <?=$RallyeName?></H2>
		<IMG ID="sound" SRC="ding.wav" STYLE="Display: none"> <!-- this is really a sound file -->
		<BGSOUND ID="play" SRC=""  LOOP="1">

		<FORM ID=theform METHOD=POST ACTION=rfidcomplete.php>
			<input type=hidden name=RallyeID value=<?=$RallyeID?>>
			<table border=1>
				<tr>
					<td colspan=2>
						Car: <INPUT onKeyDown=CarClassKeyDown(event); onKeyUp=CarClassKeyUp(event); SIZE=2 MAXLENGTH=2 TYPE=TEXT NAME=carnumber ID=car VALUE="<?=@$Car?>"><BR>
						Class: <SELECT NAME=carclass ID=carclass>
<?					
						foreach($arrClasses as $val => $key)
						{
							$val = intval($val);
							$SELECTED = "";
							if($CarClass == $val) $SELECTED = "SELECTED";
							print("<OPTION $SELECTED value=$val>$key");
						}
?>
						</SELECT>
					</td>
				</tr>
				<tr>
					<td>
						<H3>Driver</H3>
						Name: <INPUT SIZE=40 TYPE=TEXT NAME=dname ID=dname VALUE="<?=@$DName?>"><BR>
						Email: <INPUT SIZE=40 TYPE=TEXT NAME=demail ID=demail VALUE="<?=@$DEmail?>"><BR>
						Club: TRC:<INPUT TYPE=CHECKBOX NAME=dtrc VALUE=1> Other:<INPUT SIZE=20 TYPE=TEXT NAME=dclub ID=dclub VALUE="<?=@$DClub?>"><BR>
					</td>
					<td>
						<H3>Navigator</H3>
						Name: <INPUT SIZE=40 TYPE=TEXT NAME=nname ID=nname VALUE="<?=@$NName?>"><BR>
						Email: <INPUT SIZE=40 TYPE=TEXT NAME=nemail ID=nemail VALUE="<?=@$NEmail?>"><BR>
						Club: TRC:<INPUT TYPE=CHECKBOX NAME=ntrc VALUE=1> Other:<INPUT SIZE=20 TYPE=TEXT NAME=nclub ID=nclub VALUE="<?=@$NClub?>"><BR>
					</td>
				</tr>
				<tr>
					<td colspan=2>
						<H3>Passengers</H3>
						Name: <INPUT SIZE=40 TYPE=TEXT NAME=pname ID=pname VALUE="<?=@$PName?>"><BR>
						Email: <INPUT SIZE=40 TYPE=TEXT NAME=pemail ID=pemail VALUE="<?=@$PEmail?>"><BR>
						Club: TRC:<INPUT TYPE=CHECKBOX NAME=ptrc VALUE=1> Other:<INPUT SIZE=20 TYPE=TEXT NAME=pclub ID=pclub VALUE="<?=@$PClub?>"><BR>
					</td>
				</tr>
			<table>
			<INPUT TYPE=hidden readonly NAME=cardnumber ID=cardnumber  VALUE="<?=@$CardNumber?>">
			<input disabled type=submit name=store value=Register>
		</FORM>

	</BODY>
</HTML>

<script language=javascript>
function CarClassKeyDown(e)
{
//alert(e.keyCode);
	if((e.keyCode >= 37 && e.keyCode <= 40) || (e.keyCode >= 1 && e.keyCode <= 31) || (e.keyCode >= 48 && e.keyCode <= 57))
	{
		//alert(e.keyCode);
		return true
	}
	
	//alert("returning false");
	e.returnValue=false;
	return false
}

function CarClassKeyUp(e)
{
	ele = document.getElementById("car");
	sub = document.getElementById("store");
	//alert(ele.value);
	if(parseInt(ele.value,10) > 0)
	{
		//alert(parseInt(ele.value,10));
		sub.disabled=false;
	}
	else
	{
		sub.disabled=true;
	}
}
</script>